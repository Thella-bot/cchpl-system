@extends('layouts.admin')

@section('title', 'Create ' . ucfirst($type))

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Create {{ ucfirst($label) }}</h1>
    <p class="text-muted mb-0">Add a new {{ strtolower($label) }} to the system.</p>
</div>

<div class="admin-shell-card">
    <form method="POST" action="{{ route('admin.services.store', $type) }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label for="title" class="form-label fw-semibold small text-muted">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
                <label for="membership_category_id" class="form-label fw-semibold small text-muted">Membership Category</label>
                <select id="membership_category_id" name="membership_category_id" class="form-select @error('membership_category_id') is-invalid @enderror">
                    <option value="">All Members</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('membership_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('membership_category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="col-12">
                <label for="description" class="form-label fw-semibold small text-muted">Description</label>
                <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            @if($type === 'minutes')
                <div class="col-md-6">
                    <label for="meeting_date" class="form-label fw-semibold small text-muted">Meeting Date</label>
                    <input type="date" id="meeting_date" name="meeting_date" value="{{ old('meeting_date') }}" class="form-control @error('meeting_date') is-invalid @enderror" required>
                    @error('meeting_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="location" class="form-label fw-semibold small text-muted">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" class="form-control @error('location') is-invalid @enderror">
                    @error('location')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="file" class="form-label fw-semibold small text-muted">Upload File (PDF, DOC, DOCX)</label>
                    <input type="file" id="file" name="file" class="form-control @error('file') is-invalid @enderror">
                    @error('file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            @endif

            @if($type === 'events')
                <div class="col-md-6">
                    <label for="event_date" class="form-label fw-semibold small text-muted">Event Date & Time</label>
                    <input type="datetime-local" id="event_date" name="event_date" value="{{ old('event_date') }}" class="form-control @error('event_date') is-invalid @enderror" required>
                    @error('event_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="location" class="form-label fw-semibold small text-muted">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" class="form-control @error('location') is-invalid @enderror">
                    @error('location')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="venue" class="form-label fw-semibold small text-muted">Venue</label>
                    <input type="text" id="venue" name="venue" value="{{ old('venue') }}" class="form-control @error('venue') is-invalid @enderror">
                    @error('venue')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="image" class="form-label fw-semibold small text-muted">Event Image</label>
                    <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror">
                    @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="capacity" class="form-label fw-semibold small text-muted">Capacity</label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity') }}" class="form-control @error('capacity') is-invalid @enderror">
                    @error('capacity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="spots_taken" class="form-label fw-semibold small text-muted">Spots Taken</label>
                    <input type="number" id="spots_taken" name="spots_taken" value="{{ old('spots_taken', 0) }}" class="form-control @error('spots_taken') is-invalid @enderror">
                    @error('spots_taken')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="registration_deadline" class="form-label fw-semibold small text-muted">Registration Deadline</label>
                    <input type="date" id="registration_deadline" name="registration_deadline" value="{{ old('registration_deadline') }}" class="form-control @error('registration_deadline') is-invalid @enderror">
                    @error('registration_deadline')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="price" class="form-label fw-semibold small text-muted">Price</label>
                    <input type="number" step="0.01" id="price" name="price" value="{{ old('price') }}" class="form-control @error('price') is-invalid @enderror">
                    @error('price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="currency" class="form-label fw-semibold small text-muted">Currency</label>
                    <input type="text" id="currency" name="currency" value="{{ old('currency', 'M') }}" class="form-control @error('currency') is-invalid @enderror">
                    @error('currency')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            @endif

            @if(in_array($type, ['jobs', 'scholarships', 'internships']))
                <div class="col-md-6">
                    <label for="company_name" class="form-label fw-semibold small text-muted">Company / Provider Name</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" class="form-control @error('company_name') is-invalid @enderror" required>
                    @error('company_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="application_deadline" class="form-label fw-semibold small text-muted">Application Deadline</label>
                    <input type="date" id="application_deadline" name="application_deadline" value="{{ old('application_deadline') }}" class="form-control @error('application_deadline') is-invalid @enderror">
                    @error('application_deadline')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="application_url" class="form-label fw-semibold small text-muted">Application URL</label>
                    <input type="url" id="application_url" name="application_url" value="{{ old('application_url') }}" class="form-control @error('application_url') is-invalid @enderror">
                    @error('application_url')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="contact_email" class="form-label fw-semibold small text-muted">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" class="form-control @error('contact_email') is-invalid @enderror">
                    @error('contact_email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            @endif

            @if($type === 'jobs')
                <div class="col-md-6">
                    <label for="location" class="form-label fw-semibold small text-muted">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" class="form-control @error('location') is-invalid @enderror">
                    @error('location')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="employment_type" class="form-label fw-semibold small text-muted">Employment Type</label>
                    <input type="text" id="employment_type" name="employment_type" value="{{ old('employment_type') }}" class="form-control @error('employment_type') is-invalid @enderror" placeholder="e.g. Full-time, Part-time, Contract">
                    @error('employment_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="salary_range" class="form-label fw-semibold small text-muted">Salary Range</label>
                    <input type="text" id="salary_range" name="salary_range" value="{{ old('salary_range') }}" class="form-control @error('salary_range') is-invalid @enderror">
                    @error('salary_range')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            @endif

            @if($type === 'scholarships')
                <div class="col-md-6">
                    <label for="provider" class="form-label fw-semibold small text-muted">Provider / Sponsor</label>
                    <input type="text" id="provider" name="provider" value="{{ old('provider') }}" class="form-control @error('provider') is-invalid @enderror">
                    @error('provider')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="eligibility" class="form-label fw-semibold small text-muted">Eligibility Criteria</label>
                    <textarea id="eligibility" name="eligibility" rows="3" class="form-control @error('eligibility') is-invalid @enderror">{{ old('eligibility') }}</textarea>
                    @error('eligibility')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="benefit" class="form-label fw-semibold small text-muted">Benefits</label>
                    <textarea id="benefit" name="benefit" rows="3" class="form-control @error('benefit') is-invalid @enderror">{{ old('benefit') }}</textarea>
                    @error('benefit')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            @endif

            @if($type === 'internships')
                <div class="col-md-6">
                    <label for="duration" class="form-label fw-semibold small text-muted">Duration</label>
                    <input type="text" id="duration" name="duration" value="{{ old('duration') }}" class="form-control @error('duration') is-invalid @enderror" placeholder="e.g. 3 months, 6 months">
                    @error('duration')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="stipend" class="form-label fw-semibold small text-muted">Stipend</label>
                    <input type="text" id="stipend" name="stipend" value="{{ old('stipend') }}" class="form-control @error('stipend') is-invalid @enderror" placeholder="e.g. M2,000/month">
                    @error('stipend')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            @endif

            <div class="col-12">
                <div class="form-check form-switch">
                    <input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="form-check-input">
                    <label for="is_published" class="form-check-label fw-semibold">Published</label>
                </div>

                <div class="col-md-6">
                    <label for="expires_at" class="form-label fw-semibold small text-muted">Expiry Date (Optional)</label>
                    <input type="date" id="expires_at" name="expires_at" value="{{ old('expires_at') }}" class="form-control @error('expires_at') is-invalid @enderror">
                    <small class="text-muted">Leave blank for no expiry.</small>
                    @error('expires_at')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Save
                </button>
                <a href="{{ route('admin.services.index', $type) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection