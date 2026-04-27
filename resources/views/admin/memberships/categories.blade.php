@extends('layouts.admin')

@section('title', 'Membership Categories & Fees')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Membership Categories &amp; Fees</h1>
    <p class="text-muted mb-0">Update membership fees and category details for the next membership cycle.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Category</th>
                        <th class="text-end">Annual Fee (M)</th>
                        <th class="text-center">Voting Rights</th>
                        <th>Notes</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $category->name }}</div>
                                <div class="small text-muted">{{ Str::limit($category->eligibility_criteria, 80) }}</div>
                            </td>
                            <td class="text-end fw-bold">M{{ number_format($category->annual_fee, 2) }}</td>
                            <td class="text-center">
                                @if ($category->voting_rights)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ Str::limit($category->other_notes, 80) }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.memberships.categories.edit', $category->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
