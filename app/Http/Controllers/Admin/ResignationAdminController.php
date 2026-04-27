<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Membership;
use App\Models\Resignation;
use App\Notifications\ResignationAcknowledgementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResignationAdminController extends Controller
{
    /**
     * Display a listing of member resignations.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $query = Resignation::with('user', 'membership.category')
            ->orderByRaw("CASE 
                WHEN status='" . Resignation::STATUS_PENDING . "' THEN 0 
                WHEN status='" . Resignation::STATUS_ACKNOWLEDGED . "' THEN 1 
                WHEN status='" . Resignation::STATUS_CANCELLED . "' THEN 2 
                ELSE 3 
            END")
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $resignations = $query->paginate(20)->withQueryString();

        return view('admin.resignations.index', compact('resignations'));
    }

    /**
     * Display the specified resignation request.
     *
     * @param Resignation $resignation
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Resignation $resignation)
    {
        $resignation->load('user', 'membership.category', 'acknowledgedBy');
        return view('admin.resignations.show', compact('resignation'));
    }

    /**
     * Acknowledge a member's resignation request.
     *
     * @param Request $request
     * @param Resignation $resignation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acknowledge(Request $request, Resignation $resignation)
    {
        abort_if($resignation->status !== Resignation::STATUS_PENDING, 403, 'This resignation has already been processed.');

        $validated = $request->validate([
            'acknowledgement_notes' => 'nullable|string|max:2000',
            'confirm_acknowledgement' => 'required|accepted',
        ]);

        DB::transaction(function () use ($resignation, $validated) {
            $resignation->update([
                'status' => Resignation::STATUS_ACKNOWLEDGED,
                'acknowledged_by' => auth()->id(),
                'acknowledged_at' => now(),
                'acknowledgement_notes' => $validated['acknowledgement_notes'],
            ]);

            if ($resignation->membership) {
                $resignation->membership->update(['status' => Membership::STATUS_RESIGNED]);
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'resignation.acknowledged',
                'auditable_id' => $resignation->id,
                'auditable_type' => Resignation::class,
            ]);
        });

        // Notify the member
        $resignation->user?->notify(new ResignationAcknowledgementNotification($resignation));

        return redirect()->route('admin.resignations.index')->with('success', 'Resignation for ' . $resignation->user->name . ' has been acknowledged.');
    }
}