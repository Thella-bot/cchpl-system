<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CCHPL Admin')</title>
    @livewireStyles
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Tailwind CSS (preflight disabled to avoid Bootstrap conflict) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { corePlugins: { preflight: false } }</script>
    <style>
        html, body { height: 100%; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: linear-gradient(180deg, #eef6f1 0%, #f8fafc 28%, #f1f5f9 100%); }
        .admin-topbar {
            height: 76px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: linear-gradient(90deg, #113826 0%, #174b32 42%, #1d5e3d 100%);
            box-shadow: 0 14px 30px rgba(10, 32, 22, 0.18);
        }
        .admin-layout { display: flex; padding-top: 76px; min-height: 100vh; }
        .admin-sidebar-wrap {
            width: 290px;
            background: linear-gradient(180deg, #113826 0%, #174b32 42%, #1c5b3b 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            position: fixed;
            top: 92px;
            left: 16px;
            height: calc(100vh - 108px);
            overflow-y: auto;
            box-shadow: 0 28px 50px rgba(10, 32, 22, 0.28), inset -1px 0 0 rgba(255, 255, 255, 0.06);
            z-index: 1035;
            transform: translateX(-100%);
            transition: transform 0.25s ease;
            border-radius: 28px;
        }
        .admin-sidebar-wrap.is-open { transform: translateX(0); }
        .admin-sidebar-backdrop {
            position: fixed;
            inset: 76px 0 0 0;
            background: radial-gradient(circle at top left, rgba(45, 155, 90, 0.08), rgba(15, 23, 42, 0.24));
            backdrop-filter: blur(2px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
            z-index: 1030;
        }
        .admin-sidebar-backdrop.is-open {
            opacity: 1;
            pointer-events: auto;
        }
        .admin-content { flex: 1; min-width: 0; overflow-x: hidden; padding: 1.5rem; width: 100%; }
        .admin-shell-card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(15, 23, 42, 0.06);
            border-radius: 24px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
            min-height: calc(100vh - 108px);
            padding: 1.5rem;
        }
        .admin-topbar-brand { color: #f7fff9; font-size: 1.15rem; letter-spacing: 0.01em; }
        .admin-topbar-brand:hover { color: #ffffff; }
        .admin-topbar-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.9);
        }
        .admin-topbar-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff;
        }
        .admin-topbar-action {
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-radius: 999px;
            padding: 0.55rem 1rem;
            font-weight: 600;
        }
        .admin-topbar-action:hover {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
        }
        .admin-topbar-action.logout {
            background: rgba(127, 29, 29, 0.3);
            border-color: rgba(248, 113, 113, 0.24);
        }
        .admin-topbar-action.logout:hover {
            background: rgba(153, 27, 27, 0.48);
        }
        .admin-menu-toggle {
            width: 46px;
            height: 46px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(10, 32, 22, 0.12);
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <header class="admin-topbar d-flex align-items-center px-4 px-lg-5">
        <div class="d-flex align-items-center gap-3 me-auto">
            <button type="button" class="btn btn-sm admin-topbar-action admin-menu-toggle" id="admin-menu-toggle" aria-label="Toggle admin menu" aria-expanded="false" aria-controls="admin-sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <a href="{{ auth()->user()->adminHome() }}" class="fw-bold text-decoration-none admin-topbar-brand d-flex align-items-center">
<img src="{{ asset('images/logo/cchpl-official-logo.png') }}" alt="CCHPL Logo" height="40" class="d-inline-block align-top me-3">
                <span>
                    <span class="d-block small fw-normal" style="color: rgba(255, 255, 255, 0.68);">
                        Operations and oversight panel
                    </span>
                </span>
            </a>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="admin-topbar-pill d-none d-md-inline-flex">
                <span class="admin-topbar-badge" style="width: 32px; height: 32px;">
                    <i class="fas fa-user-shield"></i>
                </span>
                <span class="lh-sm">
                    <span class="d-block fw-semibold">{{ auth()->user()->name }}</span>
                    <span class="small" style="color: rgba(255, 255, 255, 0.68);">
                        {{ auth()->user()->isSuperAdmin() ? 'Super Administrator' : 'Admin Account' }}
                    </span>
                </span>
            </div>
            <a href="{{ route('member.dashboard') }}" class="btn btn-sm admin-topbar-action">
                <i class="fas fa-home me-1"></i><span class="d-none d-sm-inline">Member Portal</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm admin-topbar-action logout">
                    <i class="fas fa-sign-out-alt"></i><span class="d-none d-sm-inline ms-1">Logout</span>
                </button>
            </form>
        </div>
    </header>

    <!-- Layout -->
    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="admin-sidebar-wrap" id="admin-sidebar">
            @include('components.admin-sidebar')
        </div>
        <div class="admin-sidebar-backdrop" id="admin-sidebar-backdrop"></div>

        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-shell-card">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');
            const toggle = document.getElementById('admin-menu-toggle');

            if (!sidebar || !backdrop || !toggle) {
                return;
            }

            const setOpen = (isOpen) => {
                sidebar.classList.toggle('is-open', isOpen);
                backdrop.classList.toggle('is-open', isOpen);
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            };

            toggle.addEventListener('click', () => {
                setOpen(!sidebar.classList.contains('is-open'));
            });

            backdrop.addEventListener('click', () => setOpen(false));

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    setOpen(false);
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth < 576) {
                    setOpen(false);
                }
            });
        })();
    </script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
