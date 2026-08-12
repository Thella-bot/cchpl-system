<nav class="admin-nav">
    {{-- Super Admin --}}
    @if(auth()->user()->isSuperAdmin())
        <div class="admin-nav-section">
            <button type="button" class="admin-nav-section-header" aria-expanded="false" aria-controls="section-super-admin">
                <span>Super Admin</span>
                <i class="fas fa-chevron-down admin-nav-toggle"></i>
            </button>
            <div class="admin-nav-collapse" id="section-super-admin">
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
            </div>
        </div>
    @endif

    {{-- Membership --}}
    @if(auth()->user()->hasAnyRole(['membership_admin', 'super_admin']))
        <div class="admin-nav-section">
            <button type="button" class="admin-nav-section-header" aria-expanded="false" aria-controls="section-membership">
                <span>Membership</span>
                <i class="fas fa-chevron-down admin-nav-toggle"></i>
            </button>
            <div class="admin-nav-collapse" id="section-membership">
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
            </div>
        </div>
    @endif

    {{-- Finance --}}
    @if(auth()->user()->hasAnyRole(['finance_admin', 'super_admin']))
        <div class="admin-nav-section">
            <button type="button" class="admin-nav-section-header" aria-expanded="false" aria-controls="section-finance">
                <span>Finance</span>
                <i class="fas fa-chevron-down admin-nav-toggle"></i>
            </button>
            <div class="admin-nav-collapse" id="section-finance">
                <a href="{{ route('admin.memberships.categories.index') }}" class="admin-nav-link {{ request()->routeIs('admin.memberships.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-money-bill-wave"></i>
                    <span>Membership Fees</span>
                </a>
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
            </div>
        </div>
    @endif
    @if(auth()->user()->hasAnyRole(['membership_admin', 'super_admin']))
        <div class="admin-nav-section">
            <button type="button" class="admin-nav-section-header" aria-expanded="false" aria-controls="section-resignations">
                <span>Resignations</span>
                <i class="fas fa-chevron-down admin-nav-toggle"></i>
            </button>
            <div class="admin-nav-collapse" id="section-resignations">
                <a href="{{ route('admin.resignations.index') }}" class="admin-nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.resignations.*') ? 'active' : '' }}">
                    <span>
                        <i class="fas fa-fw fa-door-open"></i>
                        <span>Resignation Notices</span>
                    </span>
                    @php
                        $pendingResignations = \App\Models\Resignation::where('status', 'pending')->count();
                    @endphp
                    @if($pendingResignations > 0)
                        <span class="badge bg-warning rounded-pill">{{ $pendingResignations }}</span>
                    @endif
                </a>
            </div>
        </div>
    @endif

    {{-- Documents --}}
    @if(auth()->user()->hasAnyRole(['super_admin', 'membership_admin', 'finance_admin', 'content_admin']))
        <div class="admin-nav-section">
            <button type="button" class="admin-nav-section-header" aria-expanded="false" aria-controls="section-documents">
                <span>Documents</span>
                <i class="fas fa-chevron-down admin-nav-toggle"></i>
            </button>
            <div class="admin-nav-collapse" id="section-documents">
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
            </div>
        </div>
    @endif

    {{-- Reports --}}
    @if(auth()->user()->hasAnyRole(['reports_admin', 'super_admin']))
        <div class="admin-nav-section">
            <button type="button" class="admin-nav-section-header" aria-expanded="false" aria-controls="section-reports">
                <span>Reports</span>
                <i class="fas fa-chevron-down admin-nav-toggle"></i>
            </button>
            <div class="admin-nav-collapse" id="section-reports">
                <a href="{{ route('admin.reports.index') }}" class="admin-nav-link {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-chart-line"></i>
                    <span>Report Dashboard</span>
                </a>
            </div>
        </div>
    @endif

    {{-- Services --}}
    @if(auth()->user()->hasAnyRole(['content_admin', 'super_admin']))
        <div class="admin-nav-section">
            <button type="button" class="admin-nav-section-header" aria-expanded="false" aria-controls="section-services">
                <span>Services</span>
                <i class="fas fa-chevron-down admin-nav-toggle"></i>
            </button>
            <div class="admin-nav-collapse" id="section-services">
                <a href="{{ route('admin.services.index', 'minutes') }}" class="admin-nav-link {{ request()->routeIs('admin.services.index') && request('type') === 'minutes' ? 'active' : '' }}">
                    <i class="fas fa-fw fa-file-lines"></i>
                    <span>Meeting Minutes</span>
                </a>
                <a href="{{ route('admin.services.index', 'events') }}" class="admin-nav-link {{ request()->routeIs('admin.services.index') && request('type') === 'events' ? 'active' : '' }}">
                    <i class="fas fa-fw fa-calendar"></i>
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
            </div>
        </div>
    @endif
</nav>
