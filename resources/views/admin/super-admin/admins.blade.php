@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0 text-gray-800">
            <i class="fas fa-users-cog text-muted me-2"></i>Admin Management
        </h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAdminModal">
            <i class="fas fa-plus me-1"></i> Create New Admin
        </button>
    </div>

    <!-- Admins Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4">Admin User</th>
                            <th>Roles</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $admin)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $admin->name }}</div>
                                <div class="small text-muted">{{ $admin->email }}</div>
                            </td>
                            <td>
                                @forelse($admin->roles as $role)
                                    <span class="badge bg-info me-1">{{ $role->display_name }}</span>
                                @empty
                                    <span class="badge bg-secondary">No Roles</span>
                                @endforelse
                            </td>
                            <td>
                                @if($admin->last_login_at)
                                    <div class="small">{{ $admin->last_login_at->format('d M Y, H:i') }}</div>
                                    <div class="small text-muted">({{ $admin->last_login_at->diffForHumans() }})</div>
                                @else
                                    <span class="text-muted small">Never</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.admins.show', $admin) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i> Manage
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <p class="mb-0">No admin users found. Create the first one!</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Admin Modal -->
<div class="modal fade" id="createAdminModal" tabindex="-1" aria-labelledby="createAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.admins.create') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createAdminModalLabel">Create New Admin User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number (Optional)</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Assign Roles</label>
                        <div class="row">
                            @foreach($roles as $role)
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role-{{ $role->id }}">
                                        <label class="form-check-label" for="role-{{ $role->id }}">
                                            {{ $role->display_name }}
                                        </label>
                                        <div class="form-text text-muted small">{{ $role->description }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection