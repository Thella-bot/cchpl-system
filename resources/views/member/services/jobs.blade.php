@extends('layouts.app')

@section('title', 'Job Listings')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="stat-card animate-fade-in" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%); border: none;">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 72px; height: 72px;">
                    <i class="fas fa-briefcase text-white fs-1"></i>
                </div>
                <div class="text-center text-md-start">
                    <h1 class="text-white fw-bold mb-2 fs-2">Job Listings</h1>
                    <p class="text-white text-opacity-90 mb-0 lead">
                        Explore career opportunities in the culinary and hospitality industry.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="admin-shell-card">
            <form method="GET" action="{{ route('member.services.jobs') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="q" class="form-label fw-semibold small text-muted">Search Jobs</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by position or company...">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-12">
        <div class="admin-shell-card">
            @if($jobs->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fas fa-briefcase fa-2x mb-3 text-secondary opacity-50"></i>
                    <p class="mb-0">No job listings available at the moment.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Position</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Deadline</th>
                                <th class="text-center">Views</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $job->title }}</td>
                                    <td class="small">{{ $job->company_name }}</td>
                                    <td class="small text-muted">{{ $job->location ?? '—' }}</td>
                                    <td class="small">{{ $job->employment_type ?? '—' }}</td>
                                    <td class="small text-muted">{{ $job->application_deadline?->format('M d, Y') ?? 'Open' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-eye me-1"></i>{{ number_format($job->views) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($job->application_url)
                                            <a href="{{ route('member.services.redirect', ['jobs', $job->id]) }}" target="_blank" class="btn btn-sm btn-primary">
                                                Apply
                                            </a>
                                        @else
                                            <span class="text-muted small">Contact employer</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $jobs->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection