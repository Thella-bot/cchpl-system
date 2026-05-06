<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CCHPL System')</title>
    @livewireStyles

    {{-- Core CSS (Bootstrap) + Icons (Font Awesome) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">



    <style>
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background-color: #f8f9fa; }
        .navbar-brand-cchpl { color: #1a6b3c !important; font-weight: 700; }
        .navbar-brand-cchpl:hover { color: #155a32 !important; }
        .nav-topbar { border-bottom: 3px solid #1a6b3c; }

        .bg-brand-green { background-color: #1a6b3c; }
        .bg-brand-green-light { background-color: #2d9b5a; }
        .bg-brand-green-dark { background-color: #123b28; }
        .text-brand-green { color: #1a6b3c; }
        .text-brand-green-light { color: #2d9b5a; }
        .border-brand-green { border-color: #1a6b3c; }
        .btn-brand-green { background-color: #1a6b3c; border-color: #1a6b3c; }
        .btn-brand-green:hover { background-color: #155a32; border-color: #155a32; }

        .bg-green-50 { background-color: rgba(26, 107, 60, 0.08) !important; }
        .bg-green-100 { background-color: rgba(26, 107, 60, 0.15) !important; }
        .bg-green-600 { background-color: #1a6b3c !important; }
        .bg-green-700 { background-color: #155a32 !important; }
        .text-green-600 { color: #1a6b3c !important; }
        .text-green-700 { color: #155a32 !important; }
        .border-green-600 { border-color: #1a6b3c !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm nav-topbar">
        <div class="container">
            <a class="navbar-brand navbar-brand-cchpl" href="{{ url('/') }}">
                <img src="{{ asset('images/logo/cchpl-alt-logo.png') }}" alt="CCHPL Logo" height="36" class="d-inline-block align-top">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-1">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('member.dashboard') ? 'fw-semibold' : '' }}"
                               href="{{ route('member.dashboard') }}">
                                <i class="fas fa-home me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('member.profile') ? 'fw-semibold' : '' }}"
                               href="{{ route('member.profile') }}">
                                <i class="fas fa-user-circle me-1"></i>Profile
                            </a>
                        </li>
                        @if (Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link text-success fw-semibold" href="{{ auth()->user()->adminHome() }}">
                                    <i class="fas fa-cog me-1"></i>Admin Panel
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger ms-2">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </button>
                            </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        @include('components.flash-messages')
        @yield('content')
    </div>

    {{-- Bootstrap JS (required for navbar toggling, dropdowns, modals, etc.) --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Livewire scripts --}}
    @livewireScripts

    @stack('scripts')
</body>
</html>
