@extends('layouts.admin')

@section('title', 'Content Dashboard')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">@yield('title')</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Content Management</h6>
                    </div>
                    <div class="card-body">
                        <p>Manage the content of the website. There are <strong>{{ $categoryCount }}</strong> membership categories.</p>
                        <a href="{{ route('admin.memberships.categories.index') }}" class="btn btn-primary">Manage Membership Categories</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Document Management</h6>
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