@extends('layouts.admin')

@section('title', 'Edit Membership Category')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.memberships.categories.index') }}" class="text-decoration-none text-muted small">
        <i class="fas fa-arrow-left me-1"></i>Back to Categories
    </a>
    <h1 class="h3 fw-bold mt-2 mb-1">Edit Category: {{ $category->name }}</h1>
    <p class="text-muted mb-0">Update the annual fee, voting rights, and other details for this membership category.</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.memberships.categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}"
                               class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="annual_fee" class="form-label fw-semibold">Annual Fee (M) <span class="text-danger">*</span></label>
                        <input id="annual_fee" name="annual_fee" type="number" step="0.01" min="0"
                               value="{{ old('annual_fee', $category->annual_fee) }}"
                               class="form-control @error('annual_fee') is-invalid @enderror" required>
                        @error('annual_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="joining_fee" class="form-label fw-semibold">Joining Fee (M)</label>
                        <input id="joining_fee" name="joining_fee" type="number" step="0.01" min="0"
                               value="{{ old('joining_fee', $category->joining_fee) }}"
                               class="form-control @error('joining_fee') is-invalid @enderror">
                        @error('joining_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="hidden" name="voting_rights" value="0">
                            <input class="form-check-input" type="checkbox" id="voting_rights" name="voting_rights"
                                   value="1" {{ old('voting_rights', $category->voting_rights) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="voting_rights">Has Voting Rights</label>
                        </div>
                        @error('voting_rights')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="eligibility_criteria" class="form-label fw-semibold">Eligibility Criteria</label>
                        <textarea id="eligibility_criteria" name="eligibility_criteria" rows="3"
                                  class="form-control @error('eligibility_criteria') is-invalid @enderror">{{ old('eligibility_criteria', $category->eligibility_criteria) }}</textarea>
                        @error('eligibility_criteria')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="other_notes" class="form-label fw-semibold">Notes</label>
                        <textarea id="other_notes" name="other_notes" rows="3"
                                  class="form-control @error('other_notes') is-invalid @enderror">{{ old('other_notes', $category->other_notes) }}</textarea>
                        @error('other_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i>Save Changes
                        </button>
                        <a href="{{ route('admin.memberships.categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
