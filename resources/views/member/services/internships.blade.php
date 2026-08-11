@extends('layouts.app')

@section('title', 'Internships')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="stat-card animate-fade-in" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%); border: none;">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 72px; height: 72px;">
                    <i class="fas fa-handshake text-white fs-1"></i>
                </div>
                <div class="text-center text-md-start">
                    <h1 class="text-white fw-bold mb-2 fs-2">Internships</h1>
                    <p class="text-white text-opacity-90 mb-0 lead">
                        Gain practical experience with internship opportunities from leading organizations.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="admin-shell-card">
            <form method="GET" action="{{ route('member.services.internships') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="q" class="form-label fw-semibold small text-muted">Search Internships</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by title or company...">
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
            @if($internships->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fas fa-handshake fa-2x mb-3 text-secondary opacity-50"></i>
                    <p class="mb-0">No internships available at the moment.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($internships as $internship)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="fw-semibold mb-0">{{ $internship->title }}</h5>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-eye me-1"></i>{{ number_format($internship->views) }}
                                        </span>
                                    </div>
                                    <p class="small text-muted mb-2">{{ $internship->company_name }}</p>
                                    <p class="text-muted small flex-grow-1">{{ $internship->description }}</p>
                                    <div class="mt-3">
                                        @if($internship->location)
                                            <div class="d-flex align-items-center gap-2 small text-muted mb-1">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span>{{ $internship->location }}</span>
                                            </div>
                                        @endif
                                        @if($internship->duration)
                                            <div class="d-flex align-items-center gap-2 small text-muted mb-1">
                                                <i class="fas fa-clock"></i>
                                                <span>{{ $internship->duration }}</span>
                                            </div>
                                        @endif
                                        @if($internship->stipend)
                                            <div class="d-flex align-items-center gap-2 small text-muted mb-1">
                                                <i class="fas fa-money-bill"></i>
                                                <span>{{ $internship->stipend }}</span>
                                            </div>
                                        @endif
                                        @if($internship->application_deadline)
                                            <div class="d-flex align-items-center gap-2 small text-muted">
                                                <i class="fas fa-calendar"></i>
                                                <span>Deadline: {{ $internship->application_deadline->format('M d, Y') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($internship->application_url)
                                        <a href="{{ route('member.services.redirect', ['internships', $internship->id]) }}" target="_blank" class="btn btn-primary mt-3">
                                            Apply Now
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-3">{{ $internships->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection