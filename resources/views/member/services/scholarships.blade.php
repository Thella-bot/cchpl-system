@extends('layouts.app')

@section('title', 'Scholarships')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="stat-card animate-fade-in" style="background: linear-gradient(135deg, #1B1464 0%, #22B573 100%); border: none;">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 72px; height: 72px;">
                    <i class="fas fa-graduation-cap text-white fs-1"></i>
                </div>
                <div class="text-center text-md-start">
                    <h1 class="text-white fw-bold mb-2 fs-2">Scholarships</h1>
                    <p class="text-white text-opacity-90 mb-0 lead">
                        Find scholarship opportunities to advance your education and career.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="admin-shell-card">
            <form method="GET" action="{{ route('member.services.scholarships') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="q" class="form-label fw-semibold small text-muted">Search Scholarships</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by title or provider...">
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
            @if($scholarships->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fas fa-graduation-cap fa-2x mb-3 text-secondary opacity-50"></i>
                    <p class="mb-0">No scholarships available at the moment.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($scholarships as $scholarship)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="fw-semibold mb-0">{{ $scholarship->title }}</h5>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-eye me-1"></i>{{ number_format($scholarship->views) }}
                                        </span>
                                    </div>
                                    @if($scholarship->provider)
                                        <p class="small text-muted mb-2">Provider: {{ $scholarship->provider }}</p>
                                    @endif
                                    <p class="text-muted small flex-grow-1">{{ $scholarship->description }}</p>
                                    @if($scholarship->eligibility)
                                        <div class="mt-2 p-2 bg-light rounded">
                                            <small class="text-muted d-block mb-1">Eligibility</small>
                                            <small>{{ $scholarship->eligibility }}</small>
                                        </div>
                                    @endif
                                    @if($scholarship->benefit)
                                        <div class="mt-2 p-2 bg-light rounded">
                                            <small class="text-muted d-block mb-1">Benefits</small>
                                            <small>{{ $scholarship->benefit }}</small>
                                        </div>
                                    @endif
                                    <div class="mt-3">
                                        @if($scholarship->application_deadline)
                                            <div class="d-flex align-items-center gap-2 small text-muted mb-1">
                                                <i class="fas fa-clock"></i>
                                                <span>Deadline: {{ $scholarship->application_deadline->format('M d, Y') }}</span>
                                            </div>
                                        @endif
                                        @if($scholarship->contact_email)
                                            <div class="d-flex align-items-center gap-2 small text-muted">
                                                <i class="fas fa-envelope"></i>
                                                <span>{{ $scholarship->contact_email }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($scholarship->application_url)
                                        <a href="{{ route('member.services.redirect', ['scholarships', $scholarship->id]) }}" target="_blank" class="btn btn-primary mt-3">
                                            Apply Now
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-3">{{ $scholarships->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection