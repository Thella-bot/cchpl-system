<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DocumentReview;
use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\MembershipDocument;
use App\Notifications\ApplicationStatusNotification;
use App\Notifications\DocumentReviewNotification;
use App\Notifications\FeeChangedNotification;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MembershipAdminController extends Controller
{
    // ── Pending applications ───────────────────────────────────────────────

    public function index(Request $request)
    {
        $pendingCount  = Membership::where('status', Membership::STATUS_PENDING)->count();
        $approvedCount = Membership::where('status', Membership::STATUS_APPROVED)->count();
        $rejectedCount = Membership::where('status', Membership::STATUS_REJECTED)->count();

        $query = Membership::where('status', Membership::STATUS_PENDING);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('user', fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $memberships = $query
            ->with('user', 'category', 'documents')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.membership-admin.index', compact(
            'memberships', 'pendingCount', 'approvedCount', 'rejectedCount'
        ));
    }

    public function show(Membership $membership)
    {
        $membership->load('user', 'category', 'documents', 'payments');
        return view('admin.membership-admin.show', compact('membership'));
    }

    // ── Approve ────────────────────────────────────────────────────────────

    public function approve(Request $request, Membership $membership)
    {
        $oldValues = $membership->only(['status', 'member_id']);

        $membership->update(['status' => Membership::STATUS_APPROVED]);
        $memberId = $membership->generateMemberId();

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'membership.application.approved',
            'auditable_type' => Membership::class,
            'auditable_id'   => $membership->id,
            'old_values'     => $oldValues,
            'new_values'     => $membership->fresh()->only(['status', 'member_id']),
            'meta'           => [
                'approved_by' => auth()->user()->email ?? null,
                'member_id'   => $memberId,
            ],
        ]);

        // Brief approval notification (no PDF attachment)
        $membership->user->notify(
            new ApplicationStatusNotification($membership, Membership::STATUS_APPROVED)
        );

        // Auto-send membership certificate PDF
        DocumentService::sendToMember(
            DocumentReview::TYPE_CERTIFICATE,
            [
                'memberName' => strtoupper($membership->user->name),
                'category'   => strtoupper($membership->category->name) . ' MEMBER',
                'memberId'   => $memberId,
                'validFrom'  => now()->format('d F Y'),
                'validUntil' => '31 March ' . now()->addYear()->year,
                'dateIssued' => now()->format('d F Y'),
            ],
            $membership->user->email,
            $membership->user->name,
            auth()->id()
        );

        return back()->with(
            'success',
            "✅ Application for {$membership->user->name} approved. Member ID: {$memberId}. Certificate emailed."
        );
    }

    // ── Reject ─────────────────────────────────────────────────────────────

    public function reject(Request $request, Membership $membership)
    {
        $request->validate(['reason' => 'required|string|min:10']);

        $oldValues = $membership->only(['status']);
        $membership->update(['status' => Membership::STATUS_REJECTED]);

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'membership.application.rejected',
            'auditable_type' => Membership::class,
            'auditable_id'   => $membership->id,
            'old_values'     => $oldValues,
            'new_values'     => $membership->only(['status']),
            'meta'           => [
                'rejected_by' => auth()->user()->email ?? null,
                'reason'      => $request->reason,
            ],
        ]);

        $membership->user->notify(
            new ApplicationStatusNotification($membership, Membership::STATUS_REJECTED, $request->reason)
        );

        return back()->with('success', "❌ Application for {$membership->user->name} rejected.");
    }

    // ── Bulk actions ────────────────────────────────────────────────────────

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'exists:memberships,id',
            'action' => 'required|in:approve,reject',
            'reason' => 'required_if:action,reject|string|min:10',
        ]);

        $memberships = Membership::whereIn('id', $request->ids)
            ->where('status', Membership::STATUS_PENDING)
            ->with('user', 'category')
            ->get();

        foreach ($memberships as $membership) {
            $oldValues = $membership->only(['status']);
            $newStatus = $request->action === 'approve' ? Membership::STATUS_APPROVED : Membership::STATUS_REJECTED;

            $membership->update(['status' => $newStatus]);

            $memberId = null;

            if ($newStatus === Membership::STATUS_APPROVED) {
                // FIX: generate member ID and send certificate for every bulk-approved member
                $memberId = $membership->generateMemberId();

                DocumentService::sendToMember(
                    DocumentReview::TYPE_CERTIFICATE,
                    [
                        'memberName' => strtoupper($membership->user->name),
                        'category'   => strtoupper($membership->category->name) . ' MEMBER',
                        'memberId'   => $memberId,
                        'validFrom'  => now()->format('d F Y'),
                        'validUntil' => '31 March ' . now()->addYear()->year,
                        'dateIssued' => now()->format('d F Y'),
                    ],
                    $membership->user->email,
                    $membership->user->name,
                    auth()->id()
                );
            }

            AuditLog::create([
                'user_id'        => auth()->id(),
                'action'         => "membership.application.{$newStatus}",
                'auditable_type' => Membership::class,
                'auditable_id'   => $membership->id,
                'old_values'     => $oldValues,
                'new_values'     => $membership->fresh()->only(['status', 'member_id']),
                'meta'           => [
                    ($newStatus === Membership::STATUS_APPROVED ? 'approved_by' : 'rejected_by') => auth()->user()->email ?? null,
                    'reason'    => $request->reason ?? null,
                    'member_id' => $memberId,
                ],
            ]);

            $membership->user->notify(
                new ApplicationStatusNotification($membership, $newStatus, $request->reason ?? null)
            );
        }

        $count   = $memberships->count();
        $message = $request->action === 'approve'
            ? "✅ Approved {$count} application(s). Certificates emailed."
            : "❌ Rejected {$count} application(s).";

        return back()->with('success', $message);
    }

    // ── Export ──────────────────────────────────────────────────────────────

    public function export(Request $request)
    {
        $query = Membership::where('status', Membership::STATUS_PENDING);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('user', fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $memberships = $query->with('user', 'category')->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pending-memberships.csv"',
        ];

        $callback = function () use ($memberships) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Category', 'Fee (M)', 'Applied At', 'Status']);
            foreach ($memberships as $m) {
                fputcsv($handle, [
                    $m->user->name,
                    $m->user->email,
                    $m->category->name,
                    number_format($m->category->annual_fee, 2),
                    $m->created_at->format('Y-m-d H:i:s'),
                    ucfirst($m->status),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Document review ─────────────────────────────────────────────────────

    public function reviewDocument(Request $request, Membership $membership, MembershipDocument $document)
    {
        if ($document->membership_id !== $membership->id) {
            abort(404);
        }

        $request->validate([
            'status' => ['required', Rule::in([MembershipDocument::STATUS_APPROVED, MembershipDocument::STATUS_REJECTED])],
            'reason' => 'nullable|string|max:500',
        ]);

        $oldValues = $document->only(['status']);
        $document->update(['status' => $request->status]);

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => "membership.document.{$request->status}",
            'auditable_type' => $document::class,
            'auditable_id'   => $document->id,
            'old_values'     => $oldValues,
            'new_values'     => $document->only(['status']),
            'meta'           => [
                'reviewed_by' => auth()->user()->email ?? null,
                'reason'      => $request->reason,
            ],
        ]);

        $membership->user->notify(
            new DocumentReviewNotification($document, $request->status, $request->reason)
        );

        return back()->with('success', "Document status updated to {$request->status}.");
    }

    // ── Members list ────────────────────────────────────────────────────────

    public function listMembers(Request $request)
    {
        $query = Membership::where('status', Membership::STATUS_APPROVED)->with('user', 'category');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('user', fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $members = $query
            ->orderBy('expiry_date', 'asc')
            ->paginate(20)
            ->withQueryString();

        $expiringCount = $members->filter(fn ($m) => $m->isExpiringSoon())->count();
        $expiredCount  = $members->filter(fn ($m) => $m->isExpired())->count();

        return view('admin.membership-admin.members', compact('members', 'expiringCount', 'expiredCount'));
    }

    public function listRejected()
    {
        $rejected = Membership::where('status', Membership::STATUS_REJECTED)
            ->with('user', 'category')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        return view('admin.membership-admin.rejected', compact('rejected'));
    }

    // ── Categories (finance admin) ──────────────────────────────────────────

    public function categories()
    {
        $categories = MembershipCategory::orderBy('name')->get();
        return view('admin.memberships.categories', compact('categories'));
    }

    public function editCategory(MembershipCategory $category)
    {
        return view('admin.memberships.edit-category', compact('category'));
    }

    public function updateCategory(Request $request, MembershipCategory $category)
    {
        $request->validate([
            'name'                 => 'required|string|max:255',
            'annual_fee'           => 'required|numeric|min:0',
            'joining_fee'          => 'nullable|numeric|min:0',
            'voting_rights'        => 'required|boolean',
            'eligibility_criteria' => 'nullable|string',
            'other_notes'          => 'nullable|string',
        ]);

        $oldValues = $category->only([
            'name', 'annual_fee', 'joining_fee', 'voting_rights', 'eligibility_criteria', 'other_notes',
        ]);

        $category->update($request->only([
            'name', 'annual_fee', 'joining_fee', 'voting_rights', 'eligibility_criteria', 'other_notes',
        ]));

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'membership_category.updated',
            'auditable_type' => MembershipCategory::class,
            'auditable_id'   => $category->id,
            'old_values'     => $oldValues,
            'new_values'     => $category->only([
                'name', 'annual_fee', 'joining_fee', 'voting_rights', 'eligibility_criteria', 'other_notes',
            ]),
            'meta' => ['changed_by' => auth()->user()->email ?? null],
        ]);

        // Notify active members in this category about the fee change
        Membership::where('category_id', $category->id)
            ->where('status', Membership::STATUS_APPROVED)
            ->with('user')
            ->get()
            ->each(fn ($m) => $m->user->notify(
                new FeeChangedNotification($category, $oldValues['annual_fee'])
            ));

        return redirect()
            ->route('admin.memberships.categories')
            ->with('success', "✅ '{$category->name}' updated successfully.");
    }
}
