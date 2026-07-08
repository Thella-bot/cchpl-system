<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CCHPL Admin')</title>
    @livewireStyles

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body>
    <header class="admin-topbar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button id="admin-menu-toggle" class="btn btn-sm admin-topbar-action" aria-label="Toggle admin menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a href="{{ auth()->user()->adminHome() }}" class="admin-topbar-brand">
                        <img src="{{ asset('images/logo/cchpl-alt-logo.png') }}" alt="CCHPL Logo">
                        <span>Admin Panel</span>
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    <div class="admin-topbar-pill d-none d-md-flex">
                        <span class="admin-topbar-badge"><i class="fas fa-user-shield"></i></span>
                        <span class="lh-sm">
                            <span class="d-block fw-semibold">{{ auth()->user()->name }}</span>
                            <small>{{ auth()->user()->isSuperAdmin() ? 'Super Administrator' : 'Admin' }}</small>
                        </span>
                    </div>
                    <a href="{{ route('member.dashboard') }}" class="btn btn-sm admin-topbar-action" title="Member Portal">
                        <i class="fas fa-home"></i>
                        <span class="d-none d-sm-inline ms-1">Member Portal</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm admin-topbar-action logout" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="d-none d-sm-inline ms-1">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="admin-layout">
        <aside class="admin-sidebar" id="admin-sidebar">
            @include('components.admin-sidebar')
        </aside>
        <div class="admin-sidebar-backdrop" id="admin-sidebar-backdrop"></div>

        <main class="admin-content">
            <div class="container-fluid">
                <div class="admin-shell-card">
                    @include('components.flash-messages')
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const toggle = document.getElementById('admin-menu-toggle');

            if (sidebar && backdrop && toggle) {
                const setSidebarOpen = (isOpen) => {
                    document.body.classList.toggle('admin-sidebar-open', isOpen);
                };

                toggle.addEventListener('click', () => {
                    setSidebarOpen(!document.body.classList.contains('admin-sidebar-open'));
                });

                backdrop.addEventListener('click', () => setSidebarOpen(false));

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && document.body.classList.contains('admin-sidebar-open')) {
                        setSidebarOpen(false);
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>