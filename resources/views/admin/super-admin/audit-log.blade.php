@extends('layouts.admin')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">
        <i class="fas fa-history text-muted me-2"></i>System Audit Log
    </h1>
    <p class="text-muted mb-0">Track administrative actions and changes across the system.</p>
</div>

<div class="admin-shell-card mb-4">
    <form method="GET" action="{{ route('admin.audit-log') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="user" class="form-label fw-semibold small text-muted">User (Email)</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" id="user" name="user" value="{{ request('user') }}" placeholder="Search by email...">
            </div>
        </div>
        <div class="col-md-4">
            <label for="action" class="form-label fw-semibold small text-muted">Action</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-bolt"></i></span>
                <input type="text" class="form-control" id="action" name="action" value="{{ request('action') }}" placeholder="e.g. login, update...">
            </div>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            <a href="{{ route('admin.audit-log') }}" class="btn btn-outline-secondary">
                <i class="fas fa-undo me-1"></i> Reset
            </a>
        </div>
    </form>
</div>

<div class="admin-shell-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Date</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Target</th>
                    <th>Changes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="ps-4 text-nowrap">
                        <div class="fw-bold">{{ $log->created_at->format('d M Y') }}</div>
                        <div class="small text-muted">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td>
                        @if($log->user && $log->user->exists)
                            <div class="fw-bold">{{ $log->user->name }}</div>
                            <div class="small text-muted">{{ $log->user->email }}</div>
                        @else
                            <span class="badge bg-light text-secondary border">System / Deleted</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-secondary font-monospace">{{ $log->action }}</span>
                    </td>
                    <td>
                        @if($log->auditable_type)
                            <div class="small">
                                <span class="text-muted">Type:</span> 
                                <span class="fw-bold">{{ class_basename($log->auditable_type) }}</span>
                            </div>
                            <div class="small">
                                <span class="text-muted">ID:</span> 
                                <span class="font-monospace">{{ $log->auditable_id }}</span>
                            </div>
                        @else
                            <span class="text-muted small">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if(!empty($log->old_values) || !empty($log->new_values) || !empty($log->meta))
                            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#log-{{ $log->id }}">
                                <i class="fas fa-eye me-1"></i> Details
                            </button>
                        @else
                            <span class="text-muted small">No details</span>
                        @endif
                    </td>
                </tr>

                <tr class="collapse bg-light" id="log-{{ $log->id }}">
                    <td colspan="5" class="p-0">
                        <div class="p-3 border-top border-bottom">
                            <div class="row g-3">
                                @if(!empty($log->old_values))
                                <div class="col-md-4">
                                    <h6 class="small fw-bold text-danger text-uppercase mb-2">Old Values</h6>
                                    <pre class="small bg-white p-2 border rounded text-secondary" style="max-height: 200px; overflow-y: auto;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                                @endif

                                @if(!empty($log->new_values))
                                <div class="col-md-4">
                                    <h6 class="small fw-bold text-success text-uppercase mb-2">New Values</h6>
                                    <pre class="small bg-white p-2 border rounded text-secondary" style="max-height: 200px; overflow-y: auto;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                                @endif

                                @if(!empty($log->meta))
                                <div class="col-md-4">
                                    <h6 class="small fw-bold text-info text-uppercase mb-2">Metadata</h6>
                                    <pre class="small bg-white p-2 border rounded text-secondary" style="max-height: 200px; overflow-y: auto;">{{ json_encode($log->meta, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="fas fa-clipboard-list fa-2x mb-3 text-secondary opacity-50"></i>
                        <p class="mb-0">No audit logs found matching your criteria.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="p-3 border-top">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection