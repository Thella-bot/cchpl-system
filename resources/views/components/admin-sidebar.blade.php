<nav class="admin-nav">
    {{-- Super Admin --}}
    @if(auth()->user()->isSuperAdmin())
        <h2 class="admin-nav-heading">Super Admin</h2>
        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-fw fa-gauge-high"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.admins.list') }}" class="admin-nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
            <i class="fas fa-fw fa-users-gear"></i>
            <span>Manage Admins</span>
        </a>
        <a href="{{ route('admin.audit-log') }}" class="admin-nav-link {{ request()->routeIs('admin.audit-log') ? 'active' : '' }}">
            <i class="fas fa-fw fa-clipboard-list"></i>
            <span>Audit Log</span>
        </a>
        <a href="{{ route('admin.roles.manage') }}" class="admin-nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
            <i class="fas fa-fw fa-user-shield"></i>
            <span>Manage Roles</span>
        </a>
    @endif

    {{-- Membership --}}
    @if(auth()->user()->hasAnyRole(['membership_admin', 'super_admin']))
        <h2 class="admin-nav-heading">Membership</h2>
        <a href="{{ route('admin.memberships.index') }}" class="admin-nav-link {{ request()->routeIs('admin.memberships.index') ? 'active' : '' }}">
            <i class="fas fa-fw fa-hourglass-half"></i>
            <span>Pending Applications</span>
        </a>
        <a href="{{ route('admin.memberships.list') }}" class="admin-nav-link {{ request()->routeIs('admin.memberships.list') ? 'active' : '' }}">
            <i class="fas fa-fw fa-id-card"></i>
            <span>All Members</span>
        </a>
        <a href="{{ route('admin.memberships.rejected') }}" class="admin-nav-link {{ request()->routeIs('admin.memberships.rejected') ? 'active' : '' }}">
            <i class="fas fa-fw fa-user-xmark"></i>
            <span>Rejected Applications</span>
        </a>
    @endif

    {{-- Finance --}}
    @if(auth()->user()->hasAnyRole(['finance_admin', 'super_admin']))
        <h2 class="admin-nav-heading">Finance</h2>
        <a href="{{ route('admin.memberships.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.memberships.categories.*') ? 'active' : '' }}">
            <i class="fas fa-fw fa-money-bill-wave"></i>
            <span>Membership Fees</span>
        </a>
    @endif

    {{-- Resignations --}}
    @if(auth()->user()->hasAnyRole(['membership_admin', 'super_admin']))
        <h2 class="admin-nav-heading">Resignations</h2>
        @php
            $pendingResignations = \App\Models\Resignation::where('status', 'pending')->count();
        @endphp
        <a href="{{ route('admin.resignations.index') }}" class="admin-nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.resignations.*') ? 'active' : '' }}">
            <span>
                <i class="fas fa-fw fa-door-open"></i>
                <span>Resignation Notices</span>
            </span>
            @if($pendingResignations > 0)
                <span class="badge bg-warning rounded-pill">{{ $pendingResignations }}</span>
            @endif
        </a>
    @endif

    {{-- Payments --}}
    @if(auth()->user()->hasAnyRole(['payment_admin', 'super_admin']))
        <h2 class="admin-nav-heading">Payments</h2>
        <a href="{{ route('admin.payments.index') }}" class="admin-nav-link {{ request()->routeIs('admin.payments.index') ? 'active' : '' }}">
            <i class="fas fa-fw fa-wallet"></i>
            <span>Pending Payments</span>
        </a>
        <a href="{{ route('admin.payments.verified') }}" class="admin-nav-link {{ request()->routeIs('admin.payments.verified') ? 'active' : '' }}">
            <i class="fas fa-fw fa-circle-check"></i>
            <span>Verified Payments</span>
        </a>
        <a href="{{ route('admin.payments.rejected') }}" class="admin-nav-link {{ request()->routeIs('admin.payments.rejected') ? 'active' : '' }}">
            <i class="fas fa-fw fa-circle-xmark"></i>
            <span>Rejected Payments</span>
        </a>
    @endif

    {{-- Documents --}}
    @if(auth()->user()->hasAnyRole(['super_admin', 'membership_admin', 'payment_admin']))
        <h2 class="admin-nav-heading">Documents</h2>
        <a href="{{ route('admin.documents.queue') }}" class="admin-nav-link {{ request()->routeIs('admin.documents.queue') ? 'active' : '' }}">
            <i class="fas fa-fw fa-folder-open"></i>
            <span>Document Queue</span>
        </a>
        <a href="{{ route('admin.documents.compose.agm') }}" class="admin-nav-link {{ request()->routeIs('admin.documents.compose.agm') ? 'active' : '' }}">
            <i class="fas fa-fw fa-bullhorn"></i>
            <span>Compose AGM Notice</span>
        </a>
        <a href="{{ route('admin.documents.compose.minutes') }}" class="admin-nav-link {{ request()->routeIs('admin.documents.compose.minutes') ? 'active' : '' }}">
            <i class="fas fa-fw fa-file-lines"></i>
            <span>Compose EC Minutes</span>
        </a>
    @endif

    {{-- Reports --}}
    @if(auth()->user()->hasAnyRole(['reports_admin', 'super_admin']))
        <h2 class="admin-nav-heading">Reports</h2>
        <a href="{{ route('admin.reports.index') }}" class="admin-nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>Report Dashboard</span>
        </a>
    @endif

    {{-- Services --}}
    @if(auth()->user()->hasAnyRole(['content_admin', 'super_admin']))
        <h2 class="admin-nav-heading">Services</h2>
        <a href="{{ route('admin.services.index', 'minutes') }}" class="admin-nav-link {{ request()->routeIs('admin.services.index') && request('type') === 'minutes' ? 'active' : '' }}">
            <i class="fas fa-fw fa-file-lines"></i>
            <span>Meeting Minutes</span>
        </a>
        <a href="{{ route('admin.services.index', 'events') }}" class="admin-nav-link {{ request()->routeIs('admin.services.index') && request('type') === 'events' ? 'active' : '' }}">
            <i class="fas fa-fw fa-calendar-star"></i>
            <span>Events</span>
        </a>
        <a href="{{ route('admin.services.index', 'jobs') }}" class="admin-nav-link {{ request()->routeIs('admin.services.index') && request('type') === 'jobs' ? 'active' : '' }}">
            <i class="fas fa-fw fa-briefcase"></i>
            <span>Job Listings</span>
        </a>
        <a href="{{ route('admin.services.index', 'scholarships') }}" class="admin-nav-link {{ request()->routeIs('admin.services.index') && request('type') === 'scholarships' ? 'active' : '' }}">
            <i class="fas fa-fw fa-graduation-cap"></i>
            <span>Scholarships</span>
        </a>
        <a href="{{ route('admin.services.index', 'internships') }}" class="admin-nav-link {{ request()->routeIs('admin.services.index') && request('type') === 'internships' ? 'active' : '' }}">
            <i class="fas fa-fw fa-handshake"></i>
            <span>Internships</span>
        </a>
    @endif
</nav>