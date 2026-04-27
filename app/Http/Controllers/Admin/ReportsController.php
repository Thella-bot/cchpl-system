<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    /**
     * Main reports dashboard overview.
     */
    public function index()
    {
        $stats = [
            'total_members' => Membership::where('status', 'approved')->count(),
            'pending_applications' => Membership::where('status', 'pending')->count(),
            'total_revenue' => Payment::where('status', 'verified')->sum('amount'),
            'pending_revenue' => Payment::where('status', 'pending')->sum('amount'),
        ];

        $membersByCategory = MembershipCategory::withCount(['memberships' => function ($q) {
            $q->where('status', 'approved');
        }])->get();

        $recentPayments = Payment::with('membership.user')
            ->where('status', 'verified')
            ->latest('verified_at')
            ->take(5)
            ->get();

        return view('admin.reports.index', compact('stats', 'membersByCategory', 'recentPayments'));
    }

    /**
     * Filterable membership report.
     */
    public function membershipReport(Request $request)
    {
        $categories = MembershipCategory::all();
        $query = $this->filterMemberships($request);
        
        $stats = [
            'count' => $query->count(),
        ];

        $memberships = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reports.memberships', compact('memberships', 'categories', 'stats'));
    }

    /**
     * Filterable payment report.
     */
    public function paymentReport(Request $request)
    {
        $query = $this->filterPayments($request);

        $stats = [
            'count' => $query->count(),
            'total_amount' => $query->sum('amount'),
        ];

        $payments = $query->latest()->paginate(15)->withQueryString();

        return view('admin.reports.payments', compact('payments', 'stats'));
    }

    /**
     * Export filtered members to CSV.
     */
    public function exportMembers(Request $request)
    {
        $query = $this->filterMemberships($request);
        $filename = 'members-export-' . now()->format('Y-m-d-His') . '.csv';

        return $this->streamCsv($filename, function ($handle) use ($query) {
            fputcsv($handle, ['ID', 'Name', 'Email', 'Category', 'Status', 'Joined Date', 'Expiry Date']);

            $query->chunk(100, function ($memberships) use ($handle) {
                foreach ($memberships as $m) {
                    fputcsv($handle, [
                        $m->member_id ?? 'N/A',
                        $m->user->name,
                        $m->user->email,
                        $m->category->name,
                        ucfirst($m->status),
                        $m->created_at->format('Y-m-d'),
                        $m->expiry_date ? $m->expiry_date->format('Y-m-d') : 'N/A',
                    ]);
                }
            });
        });
    }

    /**
     * Export filtered payments to CSV.
     */
    public function exportPayments(Request $request)
    {
        $query = $this->filterPayments($request);
        $filename = 'payments-export-' . now()->format('Y-m-d-His') . '.csv';

        return $this->streamCsv($filename, function ($handle) use ($query) {
            fputcsv($handle, ['Receipt #', 'Member', 'Reference', 'Provider', 'Amount', 'Status', 'Date']);

            $query->chunk(100, function ($payments) use ($handle) {
                foreach ($payments as $p) {
                    fputcsv($handle, [
                        $p->receipt_number ?? 'N/A',
                        $p->membership->user->name ?? 'Unknown',
                        $p->transaction_reference,
                        ucfirst($p->provider),
                        number_format($p->amount, 2),
                        ucfirst($p->status),
                        $p->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });
        });
    }

    private function filterMemberships(Request $request)
    {
        $query = Membership::with(['user', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function filterPayments(Request $request)
    {
        $query = Payment::with(['membership.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return $query;
    }

    private function streamCsv(string $filename, callable $callback)
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return new StreamedResponse(function () use ($callback) {
            $handle = fopen('php://output', 'w');
            $callback($handle);
            fclose($handle);
        }, 200, $headers);
    }
}