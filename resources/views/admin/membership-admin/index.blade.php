@extends('layouts.admin')

@section('title', 'Pending Applications')

@section('content')
<div class="mb-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3">
    <div>
        <h1 class="h3 fw-bold mb-1">Pending Applications</h1>
        <p class="text-muted mb-0">Review and approve or reject membership applications.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <form method="GET" action="" class="d-flex align-items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name or email…"
                   class="form-control form-control-sm" style="min-width: 200px;">
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </form>
        @if(auth()->user()->hasAnyRole(['finance_admin', 'super_admin']))
            <a href="{{ route('admin.memberships.categories.index') }}" class="btn btn-sm btn-outline-indigo">
                <i class="fas fa-tags me-1"></i>Manage Fees
            </a>
        @endif
        <a href="{{ route('admin.memberships.export', ['q' => request('q')]) }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-download me-1"></i>Export CSV
        </a>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="fas fa-clock text-warning fs-5"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0">{{ number_format($pendingCount) }}</div>
                    <div class="small text-muted">Pending</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="fas fa-check-circle text-success fs-5"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0">{{ number_format($approvedCount) }}</div>
                    <div class="small text-muted">Approved</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="fas fa-times-circle text-danger fs-5"></i>
                </div>
                <div>
                    <div class="h4 fw-bold mb-0">{{ number_format($rejectedCount) }}</div>
                    <div class="small text-muted">Rejected</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($memberships->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="fas fa-inbox fa-3x mb-3 text-secondary opacity-50"></i>
            <p class="mb-0 fs-5">No pending applications at the moment.</p>
        </div>
    </div>
@else
    <!-- Bulk Actions Bar -->
    <form id="bulk-form" method="POST" action="{{ route('admin.memberships.bulk') }}">
        @csrf
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body d-flex flex-wrap align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" id="select-all" class="btn btn-sm btn-outline-secondary">All</button>
                    <button type="button" id="select-none" class="btn btn-sm btn-outline-secondary">None</button>
                    <span class="text-muted small">Selected: <strong id="selected-count">0</strong></span>
                </div>
                <div class="d-flex gap-2 ms-auto flex-wrap align-items-center">
                    <input type="text" name="reason" class="form-control form-control-sm"
                           placeholder="Rejection reason (required for bulk reject)…" style="min-width: 260px;">
                    <button type="submit" name="action" value="approve"
                            class="btn btn-sm btn-success">
                        <i class="fas fa-check me-1"></i>Approve Selected
                    </button>
                    <button type="submit" name="action" value="reject"
                            class="btn btn-sm btn-danger">
                        <i class="fas fa-times me-1"></i>Reject Selected
                    </button>
                </div>
            </div>
        </div>

        <!-- Application Cards -->
        <div class="d-flex flex-column gap-3">
            @foreach ($memberships as $membership)
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <input type="checkbox" name="ids[]" value="{{ $membership->id }}"
                                   class="bulk-checkbox form-check-input mt-1 flex-shrink-0">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <h5 class="mb-1 fw-semibold">{{ $membership->user->name }}</h5>
                                        <p class="text-muted mb-0 small">
                                            <i class="fas fa-envelope me-1"></i>{{ $membership->user->email }}
                                            &nbsp;·&nbsp;
                                            <i class="fas fa-calendar me-1"></i>Applied {{ $membership->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-4">
                                <small class="text-muted">Category</small>
                                <p class="fw-semibold mb-0">{{ $membership->category->name }}</p>
                            </div>
                            <div class="col-sm-4">
                                <small class="text-muted">Annual Fee</small>
                                <p class="fw-semibold mb-0">M{{ number_format($membership->category->annual_fee, 2) }}</p>
                            </div>
                            <div class="col-sm-4">
                                <small class="text-muted">Documents</small>
                                <p class="fw-semibold mb-0">{{ $membership->documents->count() }} uploaded</p>
                            </div>
                        </div>

                        @if($membership->documents->isNotEmpty())
                            <div class="mb-3">
                                <small class="text-muted">Uploaded Documents:</small>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    @foreach ($membership->documents as $doc)
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                           class="badge bg-light text-dark border text-decoration-none">
                                            <i class="fas fa-file me-1"></i>{{ $doc->document_type }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.memberships.show', $membership->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View Details
                            </a>
                            <form action="{{ route('admin.memberships.approve', $membership->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success"
                                        onclick="return confirm('Approve application for {{ addslashes($membership->user->name) }}?')">
                                    <i class="fas fa-check me-1"></i>Approve
                                </button>
                            </form>
                            <!-- Reject with reason (inline collapsed form) -->
                            <button class="btn btn-sm btn-danger" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#reject-{{ $membership->id }}">
                                <i class="fas fa-times me-1"></i>Reject
                            </button>
                        </div>

                        <div class="collapse mt-3" id="reject-{{ $membership->id }}">
                            <form action="{{ route('admin.memberships.reject', $membership->id) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="reason" class="form-control form-control-sm"
                                           placeholder="Reason for rejection (required, min 10 chars)…" required minlength="10">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Confirm Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $memberships->links() }}</div>
    </form>
@endif

@push('scripts')
<script>
    const checkboxes = document.querySelectorAll('.bulk-checkbox');
    const countEl    = document.getElementById('selected-count');

    function updateCount() {
        countEl.textContent = Array.from(checkboxes).filter(c => c.checked).length;
    }
    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));

    document.getElementById('select-all')?.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = true); updateCount();
    });
    document.getElementById('select-none')?.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = false); updateCount();
    });
</script>
@endpush
@endsection
