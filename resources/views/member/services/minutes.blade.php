@extends('layouts.app')

@section('title', 'Meeting Minutes')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="stat-card animate-fade-in" style="background: linear-gradient(135deg, #1B1464 0%, #22B573 100%); border: none;">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 72px; height: 72px;">
                    <i class="fas fa-file-lines text-white fs-1"></i>
                </div>
                <div class="text-center text-md-start">
                    <h1 class="text-white fw-bold mb-2 fs-2">Meeting Minutes</h1>
                    <p class="text-white text-opacity-90 mb-0 lead">
                        Access past meeting minutes and official council documents.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="admin-shell-card">
            <form method="GET" action="{{ route('member.services.minutes') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="q" class="form-label fw-semibold small text-muted">Search Minutes</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by title...">
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
            @if($minutes->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fas fa-folder-open fa-2x mb-3 text-secondary opacity-50"></i>
                    <p class="mb-0">No meeting minutes available at the moment.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Title</th>
                                <th>Category</th>
                                <th>Meeting Date</th>
                                <th>Location</th>
                                <th class="text-center">Views</th>
                                <th class="text-end">Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($minutes as $minute)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $minute->title }}</td>
                                    <td class="small">{{ $minute->category->name ?? 'All Members' }}</td>
                                    <td class="small text-muted">{{ $minute->meeting_date->format('M d, Y') }}</td>
                                    <td class="small text-muted">{{ $minute->location ?? '—' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-eye me-1"></i>{{ number_format($minute->views) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if($minute->file_path)
                                            <a href="{{ route('member.services.redirect', ['minutes', $minute->id]) }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        @else
                                            <span class="text-muted small">No file</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $minutes->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('footer')
@endsection