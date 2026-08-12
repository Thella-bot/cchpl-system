<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CCHPL Admin')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/cchpl-official-logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo/cchpl-official-logo.png') }}">
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
                <div class="d-flex align-items-center gap-3">
                    <button id="admin-menu-toggle" class="btn btn-sm" aria-label="Toggle admin menu">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="admin-topbar-brand d-flex align-items-center gap-2 text-decoration-none">
                        <img src="{{ asset('images/logo/cchpl-official-logo.png') }}" alt="CCHPL Logo" class="sidebar-logo">
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
            @include('components.flash-messages')
            @yield('content')
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
                const topbar = document.querySelector('.admin-topbar');
                const mainContent = document.querySelector('.admin-content');
                
                const setSidebarOpen = (isOpen) => {
                    document.body.classList.toggle('admin-sidebar-open', isOpen);
                    if (isOpen) {
                        backdrop.setAttribute('aria-hidden', 'false');
                        sidebar.setAttribute('aria-expanded', 'true');
                    } else {
                        backdrop.setAttribute('aria-hidden', 'true');
                        sidebar.setAttribute('aria-expanded', 'false');
                    }
                };

                // Initialize ARIA attributes
                setSidebarOpen(false);

                toggle.addEventListener('click', () => {
                    setSidebarOpen(!document.body.classList.contains('admin-sidebar-open'));
                });

                backdrop.addEventListener('click', () => setSidebarOpen(false));

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && document.body.classList.contains('admin-sidebar-open')) {
                        setSidebarOpen(false);
                    }
                });

                // Sidebar section collapsible headers
                document.querySelectorAll('.admin-nav-section-header').forEach(header => {
                    header.addEventListener('click', () => {
                        const section = header.closest('.admin-nav-section');
                        const isExpanded = section.classList.contains('expanded');

                        if (isExpanded) {
                            section.classList.remove('expanded');
                            header.setAttribute('aria-expanded', 'false');
                        } else {
                            section.classList.add('expanded');
                            header.setAttribute('aria-expanded', 'true');
                        }
                    });
                });

                // Auto-expand sections with active items
                document.querySelectorAll('.admin-nav-section').forEach(section => {
                    const hasActive = section.querySelector('.admin-nav-link.active');
                    if (hasActive) {
                        section.classList.add('expanded');
                        const header = section.querySelector('.admin-nav-section-header');
                        if (header) header.setAttribute('aria-expanded', 'true');
                    }
                });

                // Close sidebar when clicking on links (mobile only)
                if (window.innerWidth < 992) {
                    sidebar.querySelectorAll('a').forEach(link => {
                        link.addEventListener('click', () => setSidebarOpen(false));
                    });
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>