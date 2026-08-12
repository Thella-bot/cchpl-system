@extends('layouts.admin')

@section('title', 'Content Dashboard')

@section('content')
<div class="admin-shell-card">
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1">@yield('title')</h1>
        <p class="text-muted mb-0">Manage website content and organizational documents.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="fas fa-id-card text-muted me-2"></i>Content Management
                </div>
                <div class="card-body">
                    <p>Manage the content of the website. There are <strong>{{ $categoryCount }}</strong> membership categories.</p>
                    <a href="{{ route('admin.memberships.categories.index') }}" class="btn btn-primary">Manage Membership Categories</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white fw-semibold">
                    <i class="fas fa-file-alt text-muted me-2"></i>Document Management
                </div>
                <div class="card-body">
                    <p>Manage organizational documents. There are <strong>{{ $pendingDocuments }}</strong> documents pending review.</p>
                    <a href="{{ route('admin.documents.queue') }}" class="btn btn-primary">Manage Documents</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
