@extends('layouts.admin')

@section('title', 'Manage System Roles')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">System Roles</h1>
    <p class="text-muted mb-0">Overview of all roles and their assigned administrators.</p>
</div>

<div class="row g-4">
    @foreach ($roles as $role)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">{{ $role->display_name }}</span>
                    <span class="badge bg-secondary">{{ $role->users->count() }} admin(s)</span>
                </div>
                <div class="card-body">
                    @if($role->description)
                        <p class="text-muted small mb-3">{{ $role->description }}</p>
                    @endif

                    @if($role->users->isNotEmpty())
                        <ul class="list-unstyled mb-0">
                            @foreach($role->users as $user)
                                <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-primary small"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $user->name }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $user->email }}</div>
                                    </div>
                                    <a href="{{ route('admin.admins.show', $user) }}" class="btn btn-sm btn-link ms-auto text-decoration-none p-0">
                                        Manage
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted small mb-0 fst-italic">No admins assigned to this role.</p>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <span class="small text-muted font-monospace">{{ $role->name }}</span>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
