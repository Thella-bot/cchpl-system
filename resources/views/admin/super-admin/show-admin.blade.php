@extends('layouts.admin')

@section('title', 'Manage Admin: ' . $user->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.admins.list') }}" class="text-decoration-none text-muted small">
        <i class="fas fa-arrow-left me-1"></i>Back to Admin List
    </a>
    <h1 class="h3 fw-bold mt-2 mb-0">
        Manage Admin: <span class="text-primary">{{ $user->name }}</span>
    </h1>
</div>

<div class="row g-4">
    <!-- Left: Details & Deactivation -->
    <div class="col-lg-4">
        <!-- Details Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-id-card text-muted me-2"></i>Admin Details
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">Name</dt>
                    <dd class="col-7 fw-semibold">{{ $user->name }}</dd>
                    <dt class="col-5 text-muted">Email</dt>
                    <dd class="col-7">{{ $user->email }}</dd>
                    <dt class="col-5 text-muted">Phone</dt>
                    <dd class="col-7">{{ $user->phone ?? '—' }}</dd>
                    <dt class="col-5 text-muted">Joined</dt>
                    <dd class="col-7">{{ $user->created_at->format('d M Y') }}</dd>
                    <dt class="col-5 text-muted">Last Login</dt>
                    <dd class="col-7">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                    </dd>
                </dl>
            </div>
        </div>

        <!-- Current Roles -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-tags text-muted me-2"></i>Current Roles
            </div>
            <div class="card-body">
                @forelse($user->roles as $role)
                    <span class="badge bg-info text-dark me-1 mb-1">{{ $role->display_name }}</span>
                @empty
                    <span class="badge bg-secondary">No Roles Assigned</span>
                @endforelse
            </div>
        </div>

        <!-- Danger Zone -->
        @if(Auth::id() !== $user->id)
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-header bg-danger text-white fw-semibold">
                    <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                </div>
                <div class="card-body">
                    <p class="small text-muted">Deactivating removes all roles and revokes admin access immediately.</p>
                    <button class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deactivateModal">
                        <i class="fas fa-user-slash me-1"></i>Deactivate Admin
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Right: Role Management -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-user-shield text-muted me-2"></i>Manage Roles
            </div>
            <form action="{{ route('admin.admins.roles.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Select the roles this admin should have. The Super Admin role cannot be removed from the primary super admin.
                    </p>
                    <div class="row g-3">
                        @foreach($roles as $role)
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           name="roles[]" value="{{ $role->id }}"
                                           id="role-{{ $role->id }}"
                                           {{ $user->roles->contains($role) ? 'checked' : '' }}
                                           {{ ($role->name === 'super_admin' && $user->id === 1) ? 'disabled' : '' }}>
                                    @if($role->name === 'super_admin' && $user->id === 1)
                                        <input type="hidden" name="roles[]" value="{{ $role->id }}">
                                    @endif
                                    <label class="form-check-label fw-semibold" for="role-{{ $role->id }}">
                                        {{ $role->display_name }}
                                    </label>
                                    @if($role->description)
                                        <div class="form-text">{{ $role->description }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-light text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Roles
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deactivation Modal -->
<div class="modal fade" id="deactivateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.admins.deactivate', $user) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deactivation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to deactivate <strong>{{ $user->name }}</strong>?</p>
                    <p class="text-danger fw-semibold mb-0">
                        This will immediately revoke all admin privileges. It can be reversed by a Super Admin.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-user-slash me-1"></i>Yes, Deactivate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
