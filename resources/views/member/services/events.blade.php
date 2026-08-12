@extends('layouts.app')

@section('title', 'Upcoming Events')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="stat-card animate-fade-in" style="background: linear-gradient(135deg, #1B1464 0%, #22B573 100%); border: none;">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 72px; height: 72px;">
                    <i class="fas fa-calendar-star text-white fs-1"></i>
                </div>
                <div class="text-center text-md-start">
                    <h1 class="text-white fw-bold mb-2 fs-2">Upcoming Events</h1>
                    <p class="text-white text-opacity-90 mb-0 lead">
                        Discover workshops, networking sessions, and industry events.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="admin-shell-card">
            <form method="GET" action="{{ route('member.services.events') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="q" class="form-label fw-semibold small text-muted">Search Events</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by event name...">
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
            @if($events->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fas fa-calendar-xmark fa-2x mb-3 text-secondary opacity-50"></i>
                    <p class="mb-0">No upcoming events at the moment.</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($events as $event)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                @if($event->image_path)
                                    <img src="{{ asset('storage/' . $event->image_path) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 180px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                        <i class="fas fa-calendar text-muted fa-2x"></i>
                                    </div>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="fw-semibold mb-0">{{ $event->title }}</h5>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-eye me-1"></i>{{ number_format($event->views) }}
                                        </span>
                                    </div>
                                    <p class="text-muted small flex-grow-1">{{ $event->description }}</p>
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center gap-2 small text-muted mb-1">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ $event->event_date->format('M d, Y H:i') }}</span>
                                        </div>
                                        @if($event->location)
                                            <div class="d-flex align-items-center gap-2 small text-muted mb-1">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span>{{ $event->location }}</span>
                                            </div>
                                        @endif
                                        @if($event->venue)
                                            <div class="d-flex align-items-center gap-2 small text-muted mb-1">
                                                <i class="fas fa-building"></i>
                                                <span>{{ $event->venue }}</span>
                                            </div>
                                        @endif
                                        @if($event->price)
                                            <div class="d-flex align-items-center gap-2 small text-muted mb-1">
                                                <i class="fas fa-tag"></i>
                                                <span>{{ $event->currency }}{{ number_format($event->price, 2) }}</span>
                                            </div>
                                        @endif
                                        @if($event->spots_remaining !== null)
                                            <div class="d-flex align-items-center gap-2 small text-muted">
                                                <i class="fas fa-users"></i>
                                                <span>{{ $event->spots_remaining }} spots remaining</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($event->is_published)
                                        <span class="badge bg-success mt-3 align-self-start">Published</span>
                                    @else
                                        <span class="badge bg-warning text-dark mt-3 align-self-start">Draft</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-3">{{ $events->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection