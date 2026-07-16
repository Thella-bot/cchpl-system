<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Council for Culinary and Hospitality Professionals Lesotho - Professional membership portal">
    <title>@yield('title', 'CCHPL System')</title>
    @livewireStyles

    {{-- Core CSS (Bootstrap) + Icons (Font Awesome) --}}
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">

    {{-- Application-specific styles --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm nav-topbar sticky-top">
        <div class="container">
            <a class="navbar-brand navbar-brand-cchpl d-flex align-items-center gap-2" href="{{ url('/') }}">
                <img src="{{ asset('images/logo/cchpl-alt-logo.png') }}" alt="CCHPL Logo" height="36" class="d-inline-block align-top">
                <span class="d-none d-sm-inline fw-bold">CCHPL</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2 mt-3 mt-lg-0">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded-3 transition-all hover:bg-light" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded-3 transition-all hover:bg-light" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded-3 transition-all hover:bg-light {{ request()->routeIs('member.dashboard') ? 'bg-green-50 text-green-700 fw-semibold' : '' }}"
                               href="{{ route('member.dashboard') }}">
                                <i class="fas fa-home me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded-3 transition-all hover:bg-light {{ request()->routeIs('member.profile') ? 'bg-green-50 text-green-700 fw-semibold' : '' }}"
                               href="{{ route('member.profile') }}">
                                <i class="fas fa-user-circle me-1"></i>Profile
                            </a>
                        </li>
                        @if (Auth::user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link px-3 py-2 rounded-3 bg-green-50 text-green-700 fw-semibold transition-all hover:bg-green-100" href="{{ auth()->user()->adminHome() }}">
                                    <i class="fas fa-cog me-1"></i>Admin Panel
                                </a>
                            </li>
                        @endif
                        <li class="nav-item mt-2 mt-lg-0">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger ms-2 px-4 py-2 rounded-3 transition-all hover:bg-danger hover:text-white">
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

    @include('components.site-footer')

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