<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Council for Culinary and Hospitality Professionals Lesotho - Professional membership portal">
    <title>@yield('title', 'CCHPL System')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/cchpl-official-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/cchpl-official-logo.png') }}">
    @livewireStyles

    {{-- Core CSS (Bootstrap) + Icons (Font Awesome) --}}
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">

    {{-- Application-specific styles --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <header class="member-header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo/cchpl-official-logo.png') }}" alt="CCHPL Logo" height="48" class="header-logo">
                </a>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center gap-1 mt-3 mt-lg-0">
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
                                <a class="nav-link {{ request()->routeIs('member.dashboard') ? 'active' : '' }}"
                                   href="{{ route('member.dashboard') }}">
                                    <i class="fas fa-home me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('member.profile') ? 'active' : '' }}"
                                   href="{{ route('member.profile') }}">
                                    <i class="fas fa-user-circle me-1"></i>Profile
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('member.services.*') ? 'active' : '' }}"
                                   href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-briefcase me-1"></i>Services
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('member.services.minutes') }}"><i class="fas fa-file-lines me-2 text-muted"></i>Meeting Minutes</a></li>
                                    <li><a class="dropdown-item" href="{{ route('member.services.events') }}"><i class="fas fa-calendar me-2 text-muted"></i>Events</a></li>
                                    <li><a class="dropdown-item" href="{{ route('member.services.jobs') }}"><i class="fas fa-briefcase me-2 text-muted"></i>Job Listings</a></li>
                                    <li><a class="dropdown-item" href="{{ route('member.services.scholarships') }}"><i class="fas fa-graduation-cap me-2 text-muted"></i>Scholarships</a></li>
                                    <li><a class="dropdown-item" href="{{ route('member.services.internships') }}"><i class="fas fa-handshake me-2 text-muted"></i>Internships</a></li>
                                </ul>
                            </li>
                            @if (Auth::user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link nav-link-admin" href="{{ auth()->user()->adminHome() }}">
                                        <i class="fas fa-cog me-1"></i>Admin Panel
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item nav-item-logout">
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-logout">
                                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                                    </button>
                                </form>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container py-4">
            @include('components.flash-messages')
            @yield('content')
        </div>
    </main>

    @yield('footer')
    @section('footer')
        @include('components.site-footer')
    @show

    {{-- Bootstrap JS (required for navbar toggling, dropdowns, modals, etc.) --}}
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    {{-- Livewire scripts --}}
    @livewireScripts

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.password-toggle-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const input = document.getElementById(btn.dataset.target);
                    if (!input) return;
                    const show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    btn.classList.toggle('show', show);
                    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                });
            });
        });
    </script>
</body>
</html>