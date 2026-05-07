<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CCHPL Admin')</title>
    @livewireStyles

    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">

    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>


    <style>

.bg-green-50 { background-color: rgba(26, 107, 60, 0.08) !important; }
        .bg-green-100 { background-color: rgba(26, 107, 60, 0.15) !important; }
        .bg-green-200 { background-color: rgba(26, 107, 60, 0.25) !important; }
        .bg-green-600 { background-color: #1a6b3c !important; }
        .bg-green-700 { background-color: #155a32 !important; }
        .bg-green-800 { background-color: #123b28 !important; }
        .text-green-500 { color: #2d9b5a !important; }
        .text-green-600 { color: #1a6b3c !important; }
        .text-green-700 { color: #155a32 !important; }
        .text-green-800 { color: #123b28 !important; }
        .border-green-500 { border-color: #2d9b5a !important; }
        .border-green-600 { border-color: #1a6b3c !important; }
        .border-green-200 { border-color: rgba(26, 107, 60, 0.25) !important; }
        .focus\:ring-green-500:focus { --tw-ring-color: #2d9b5a !important; }

html, body { height: 100%; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: linear-gradient(180deg, #eef6f1 0%, #f8fafc 28%, #f1f5f9 100%); }

.admin-topbar {
            height: 76px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1040;
            border-bottom: 1px solid rgba(15, 23, 42, 0.08);
            background: #ffffff;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
        }
        .admin-layout { display: flex; padding-top: 76px; min-height: 100vh; }
.admin-sidebar-wrap {
            width: 290px;
            background: linear-gradient(180deg, #123b28 0%, #1a6b3c 42%, #2d9b5a 100%);
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
        .admin-topbar-brand { color: #1a6b3c; font-size: 1.15rem; letter-spacing: 0.01em; }
        .admin-topbar-brand:hover { color: #155a32; }
        .admin-topbar-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.55rem 0.9rem;
            border-radius: 999px;
            background: rgba(26, 107, 60, 0.07);
            border: 1px solid rgba(26, 107, 60, 0.14);
            color: #1f2937;
        }
        .admin-topbar-badge {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(26, 107, 60, 0.12);
            color: #1a6b3c;
        }
        .admin-topbar-action {
            border: 1px solid rgba(26, 107, 60, 0.18);
            background: rgba(26, 107, 60, 0.08);
            color: #1a6b3c;
            border-radius: 999px;
            padding: 0.55rem 1rem;
            font-weight: 600;
        }
        .admin-topbar-action:hover {
            background: rgba(26, 107, 60, 0.14);
            color: #155a32;
        }
        .admin-topbar-action.logout {
            background: rgba(220, 38, 38, 0.08);
            border-color: rgba(220, 38, 38, 0.2);
            color: #b91c1c;
        }
        .admin-topbar-action.logout:hover {
            background: rgba(220, 38, 38, 0.14);
            color: #991b1b;
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

<header class="admin-topbar d-flex align-items-center px-4 px-lg-5">
        <div class="d-flex align-items-center gap-3 me-auto">
            <button type="button" class="btn btn-sm admin-topbar-action admin-menu-toggle" id="admin-menu-toggle" aria-label="Toggle admin menu" aria-expanded="false" aria-controls="admin-sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <a href="{{ auth()->user()->adminHome() }}" class="fw-bold text-decoration-none admin-topbar-brand d-flex align-items-center">
<img src="{{ asset('images/logo/cchpl-alt-logo.png') }}" alt="CCHPL Logo" height="40" class="d-inline-block align-top me-3">
                <span>
                    <span class="d-block small fw-normal" style="color: #64748b;">
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
                    <span class="small" style="color: #64748b;">
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

<div class="admin-layout">

<div class="admin-sidebar-wrap" id="admin-sidebar">
            @include('components.admin-sidebar')
        </div>
        <div class="admin-sidebar-backdrop" id="admin-sidebar-backdrop"></div>

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
