<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DocumentReview;
use App\Models\Membership;
use App\Models\MembershipCategory;
use App\Models\MembershipDocument;
use App\Models\User;
use App\Notifications\ApplicationStatusNotification;
use App\Notifications\DocumentReviewNotification;
use App\Notifications\FeeChangedNotification;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MembershipAdminController extends Controller
{
    public function index(Request $request)
    {
        $pendingCount = Membership::where('status', Membership::STATUS_PENDING)->count();
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

    public function approve(Request $request, Membership $membership)
    {
        $result = $this->approveMembership($membership);

        $message = "✅ Application for {$membership->user->name} approved. Member ID: {$membership->member_id}.";
        if ($result['cert_error']) {
            $message .= ' Certificate could not be emailed due to a technical issue (full details logged). Check logs if needed.';
        } else {
            $message .= ' Certificate emailed.';
        }

        return back()->with('success', $message);
    }

    public function reject(Request $request, Membership $membership)
    {
        $request->validate(['reason' => 'required|string|min:10|max:1000']);

        $this->rejectMembership($membership, $request->reason);

        return back()->with('success', "❌ Application for {$membership->user->name} rejected.");
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1|max:500',
            'ids.*' => 'exists:memberships,id',
        ]);

        $memberships = Membership::whereIn('id', $request->ids)
            ->where('status', Membership::STATUS_PENDING)
            ->with('user')
            ->get();

        $certFailures = [];
        foreach ($memberships as $membership) {
            $result = $this->approveMembership($membership);
            if ($result['cert_error']) {
                $certFailures[] = $membership->user->name;
            }
        }

        $count = $memberships->count();
        $message = "✅ Approved {$count} application(s).";
        if (! empty($certFailures)) {
            $message .= ' Certificate emails could not be sent for some members due to technical issues (full details logged).';
        } else {
            $message .= ' Certificates emailed.';
        }

        return back()->with('success', $message);
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1|max:500',
            'ids.*' => 'exists:memberships,id',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        $memberships = Membership::whereIn('id', $request->ids)
            ->where('status', Membership::STATUS_PENDING)
            ->get();

        foreach ($memberships as $membership) {
            $this->rejectMembership($membership, $request->reason);
        }

        return back()->with('success', "❌ Rejected {$memberships->count()} application(s).");
    }

    private function approveMembership(Membership $membership): array
    {
        $oldValues = $membership->only(['status', 'member_id']);

        $membership->update(['status' => Membership::STATUS_APPROVED]);
        $memberId = $membership->generateMemberId();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'membership.application.approved',
            'auditable_type' => Membership::class,
            'auditable_id' => $membership->id,
            'old_values' => $oldValues,
            'new_values' => $membership->fresh()->only(['status', 'member_id']),
            'meta' => [
                'approved_by' => auth()->user()->email ?? null,
                'member_id' => $memberId,
            ],
        ]);

        $membership->user->notify(
            new ApplicationStatusNotification($membership, Membership::STATUS_APPROVED)
        );

        $certError = null;
        try {
            DocumentService::sendToMember($membership, DocumentReview::TYPE_CERTIFICATE);
        } catch (\Exception $e) {
            $certError = $e->getMessage();
            Log::warning("Certificate email failed for membership #{$membership->id}: {$certError}");
        }

        return ['cert_error' => $certError];
    }

    private function rejectMembership(Membership $membership, string $reason): void
    {
        $oldValues = $membership->only(['status']);
        $membership->update(['status' => Membership::STATUS_REJECTED]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'membership.application.rejected',
            'auditable_type' => Membership::class,
            'auditable_id' => $membership->id,
            'old_values' => $oldValues,
            'new_values' => $membership->only(['status']),
            'meta' => [
                'rejected_by' => auth()->user()->email ?? null,
                'reason' => $reason,
            ],
        ]);

        $membership->user->notify(
            new ApplicationStatusNotification($membership, Membership::STATUS_REJECTED, $reason)
        );
    }

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
            'Content-Type' => 'text/csv',
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
            'user_id' => auth()->id(),
            'action' => "membership.document.{$request->status}",
            'auditable_type' => $document::class,
            'auditable_id' => $document->id,
            'old_values' => $oldValues,
            'new_values' => $document->only(['status']),
            'meta' => [
                'reviewed_by' => auth()->user()->email ?? null,
                'reason' => $request->reason,
            ],
        ]);

        $membership->user->notify(
            new DocumentReviewNotification($document, $request->status, $request->reason)
        );

        return back()->with('success', "Document status updated to {$request->status}.");
    }

    public function updateMemberEmail(Request $request, Membership $membership)
    {
        $membership->load('user');
        $user = $membership->user;
        abort_unless($user->exists, 404);

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $newEmail = strtolower(trim($validated['email']));
        $oldEmail = $user->email;

        if (strcasecmp($oldEmail, $newEmail) === 0) {
            return back()->with('info', 'The member email address is unchanged.');
        }

        $user->forceFill([
            'email' => $newEmail,
            'email_verified_at' => null,
        ])->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'member.email.updated',
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'old_values' => ['email' => $oldEmail],
            'new_values' => ['email' => $newEmail],
            'meta' => [
                'membership_id' => $membership->id,
                'changed_by' => auth()->user()->email ?? null,
            ],
        ]);

        return back()->with('success', 'Member email address updated successfully.');
    }

    public function updateStatus(Request $request, Membership $membership)
    {
        $request->validate([
            'status' => ['required', 'string', Rule::in([
                Membership::STATUS_APPROVED,
                Membership::STATUS_SUSPENDED,
                Membership::STATUS_EXPIRED,
                Membership::STATUS_RESIGNED,
            ])],
            'reason' => 'required|string|min:10|max:500',
        ]);

        $oldStatus = $membership->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return back()->with('info', 'Membership status is unchanged.');
        }

        // Guardrails
        if ($oldStatus === Membership::STATUS_APPROVED && $newStatus === Membership::STATUS_PENDING) {
            return back()->with('error', 'Invalid status transition.');
        }

        if ($oldStatus === Membership::STATUS_RESIGNED && $newStatus === Membership::STATUS_APPROVED) {
            return back()->with('error', 'Cannot revert a resigned membership back to approved.');
        }

        // Only allow admins to move approved members between operational states.
        // (This route is used from the approved members area.)
        if ($oldStatus !== Membership::STATUS_APPROVED && ! in_array($oldStatus, [Membership::STATUS_SUSPENDED, Membership::STATUS_EXPIRED, Membership::STATUS_RESIGNED], true)) {
            return back()->with('error', 'Cannot manage status from the current membership state.');
        }

        $oldValues = $membership->only(['status', 'suspended_at', 'expiry_date', 'rejection_reason']);

        $membership->fill([
            'status' => $newStatus,
        ]);

        if ($newStatus === Membership::STATUS_SUSPENDED) {
            $membership->suspended_at = $membership->suspended_at ?? now();
        } else {
            $membership->suspended_at = null;
        }

        // Keep expiry_date as-is; commands already handle expiry marking.
        $membership->save();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'membership.status.updated',
            'auditable_type' => Membership::class,
            'auditable_id' => $membership->id,
            'old_values' => $oldValues,
            'new_values' => $membership->fresh()->only(['status', 'suspended_at', 'expiry_date', 'rejection_reason']),
            'meta' => [
                'from_status' => $oldStatus,
                'to_status' => $newStatus,
                'changed_by' => auth()->user()->email ?? null,
                'reason' => $request->reason,
            ],
        ]);

        return back()->with('success', 'Membership status updated successfully.');
    }

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
        $expiredCount = $members->filter(fn ($m) => $m->isExpired())->count();

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
            'name' => 'required|string|max:255',
            'annual_fee' => 'required|numeric|min:0',
            'joining_fee' => 'nullable|numeric|min:0',
            'voting_rights' => 'required|boolean',
            'eligibility_criteria' => 'nullable|string',
            'other_notes' => 'nullable|string',
        ]);

        $oldValues = $category->only([
            'name', 'annual_fee', 'joining_fee', 'voting_rights', 'eligibility_criteria', 'other_notes',
        ]);

        $category->update($request->only([
            'name', 'annual_fee', 'joining_fee', 'voting_rights', 'eligibility_criteria', 'other_notes',
        ]));

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'membership_category.updated',
            'auditable_type' => MembershipCategory::class,
            'auditable_id' => $category->id,
            'old_values' => $oldValues,
            'new_values' => $category->only([
                'name', 'annual_fee', 'joining_fee', 'voting_rights', 'eligibility_criteria', 'other_notes',
            ]),
            'meta' => ['changed_by' => auth()->user()->email ?? null],
        ]);

        Membership::where('category_id', $category->id)
            ->where('status', Membership::STATUS_APPROVED)
            ->with('user')
            ->get()
            ->each(fn ($m) => $m->user->notify(
                new FeeChangedNotification($category, $oldValues['annual_fee'])
            ));

        return redirect()
            ->route('admin.memberships.categories.index')
            ->with('success', "'{$category->name}' updated successfully.");
    }
}
