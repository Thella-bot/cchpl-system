@extends('layouts.admin')

@section('title', ucfirst($type))

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold mb-1">{{ ucfirst($type) }}</h1>
        <p class="text-muted mb-0">Manage {{ strtolower($label) }}.</p>
    </div>
    <a href="{{ route('admin.services.create', $type) }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add New
    </a>
</div>

<div class="admin-shell-card mb-3">
    <form method="GET" action="" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="q" class="form-label fw-semibold small text-muted">Search</label>
            <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Search by title..." class="form-control">
        </div>
        <div class="col-md-4">
            <label for="category" class="form-label fw-semibold small text-muted">Membership Category</label>
            <select id="category" name="category" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-search me-1"></i>Filter
            </button>
            <a href="{{ route('admin.services.index', $type) }}" class="btn btn-outline-secondary">
                <i class="fas fa-undo me-1"></i>Reset
            </a>
        </div>
    </form>
</div>

<div class="admin-shell-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Title</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-center">Views</th>
                    <th>Expiry</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr class="{{ $item->expires_at && $item->expires_at->isPast() ? 'table-danger' : '' }}">
                        <td class="ps-4 fw-semibold">{{ $item->title }}</td>
                        <td class="small">{{ $item->category->name ?? 'All Members' }}</td>
                        <td class="small text-muted">
                            @php
                                $date = match($type) {
                                    'minutes' => $item->meeting_date,
                                    'events' => $item->event_date?->format('M d, Y H:i'),
                                    default => $item->created_at->format('M d, Y'),
                                };
                            @endphp
                            {{ $date ?? '—' }}
                        </td>
                        <td>
                            <span class="badge {{ $item->is_published ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $item->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-eye me-1"></i>{{ number_format($item->views) }}
                            </span>
                        </td>
                        <td>
                            @if($item->expires_at)
                                <span class="badge {{ $item->expires_at->isPast() ? 'bg-danger' : 'bg-info' }}">
                                    {{ $item->expires_at->format('M d, Y') }}
                                    @if($item->expires_at->isPast())
                                        <i class="fas fa-exclamation-triangle ms-1"></i>
                                    @endif
                                </span>
                            @else
                                <span class="text-muted small">No expiry</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.services.edit', [$type, $item->id]) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <form action="{{ route('admin.services.destroy', [$type, $item->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash me-1"></i>Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">No {{ strtolower($label) }} found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
        <div class="p-3">{{ $items->links() }}</div>
    @endif
</div>
@endsection