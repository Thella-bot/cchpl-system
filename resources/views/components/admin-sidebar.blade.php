<aside>
    <div class="px-3 py-4 text-white">
        <div class="rounded-4 p-3 mb-4" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12);">
            <div class="small text-uppercase fw-semibold mb-2" style="letter-spacing: 0.14em; color: rgba(255, 255, 255, 0.7);">
                Admin Menu
            </div>
            <div class="fw-bold fs-5 mb-1">{{ auth()->user()->name }}</div>
            <div class="small" style="color: rgba(255, 255, 255, 0.72);">
                {{ auth()->user()->isSuperAdmin() ? 'Super Administrator' : 'Administrative Access' }}
            </div>
        </div>

        <nav class="d-flex flex-column gap-2">

            {{-- Super Admin --}}
            @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.dashboard') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.dashboard') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.dashboard') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.dashboard') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-gauge-high"></i>
                    </span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.admins.list') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.admins.*') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.admins.*') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.admins.*') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-users-gear"></i>
                    </span>
                    <span>Manage Admins</span>
                </a>
                <a href="{{ route('admin.audit-log') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.audit-log') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.audit-log') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.audit-log') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-clipboard-list"></i>
                    </span>
                    <span>Audit Log</span>
                </a>
                <a href="{{ route('admin.roles.manage') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.roles.*') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.roles.*') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.roles.*') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-user-shield"></i>
                    </span>
                    <span>Manage Roles</span>
                </a>
            @endif

            {{-- Membership --}}
            @if(auth()->user()->hasAnyRole(['membership_admin', 'super_admin']))
                <div class="pt-4 pb-1 px-2 small fw-semibold text-uppercase" style="letter-spacing: 0.14em; color: rgba(255, 255, 255, 0.58);">
                    Membership
                </div>
                <a href="{{ route('admin.memberships.index') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.memberships.index') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.memberships.index') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.memberships.index') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-hourglass-half"></i>
                    </span>
                    <span>Pending Applications</span>
                </a>
                <a href="{{ route('admin.memberships.list') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.memberships.list') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.memberships.list') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.memberships.list') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <span>All Members</span>
                </a>
                <a href="{{ route('admin.memberships.rejected') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.memberships.rejected') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.memberships.rejected') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.memberships.rejected') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-user-xmark"></i>
                    </span>
                    <span>Rejected Applications</span>
                </a>
            @endif

            {{-- Finance — FIX: was missing finance_admin role check --}}
            @if(auth()->user()->hasAnyRole(['finance_admin', 'super_admin']))
                <div class="pt-4 pb-1 px-2 small fw-semibold text-uppercase" style="letter-spacing: 0.14em; color: rgba(255, 255, 255, 0.58);">
                    Finance
                </div>
                <a href="{{ route('admin.memberships.categories.index') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.memberships.categories.*') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.memberships.categories.*') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.memberships.categories.*') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-money-bill-wave"></i>
                    </span>
                    <span>Membership Fees</span>
                </a>
            @endif
                        
            {{-- Resignations — Secretary queue --}}
            @if(auth()->user()->hasAnyRole(['membership_admin', 'super_admin']))
                @php
                    $pendingResignations = \App\Models\Resignation::where('status', 'pending')->count();
                @endphp
                <div class="pt-4 pb-1 px-2 small fw-semibold text-uppercase" style="letter-spacing: 0.14em; color: rgba(255, 255, 255, 0.58);">
                    Resignations
                </div>
                <a href="{{ route('admin.resignations.index') }}"
                   class="d-flex align-items-center justify-content-between gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.resignations.*') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.resignations.*') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-flex align-items-center gap-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.resignations.*') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                            <i class="fas fa-door-open"></i>
                        </span>
                        <span>Resignation Notices</span>
                    </span>
                    @if($pendingResignations > 0)
                        <span class="d-inline-flex align-items-center justify-content-center rounded-pill px-2 py-1 text-xs fw-bold" style="background: #facc15; color: #3f2f00;">
                            {{ $pendingResignations }}
                        </span>
                    @endif
                </a>
            @endif
            {{-- Payments --}}
            @if(auth()->user()->hasAnyRole(['payment_admin', 'super_admin']))
                <div class="pt-4 pb-1 px-2 small fw-semibold text-uppercase" style="letter-spacing: 0.14em; color: rgba(255, 255, 255, 0.58);">
                    Payments
                </div>
                <a href="{{ route('admin.payments.index') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.payments.index') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.payments.index') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.payments.index') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-wallet"></i>
                    </span>
                    <span>Pending Payments</span>
                </a>
                <a href="{{ route('admin.payments.verified') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.payments.verified') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.payments.verified') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.payments.verified') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-circle-check"></i>
                    </span>
                    <span>Verified Payments</span>
                </a>
                <a href="{{ route('admin.payments.rejected') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.payments.rejected') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.payments.rejected') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.payments.rejected') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-circle-xmark"></i>
                    </span>
                    <span>Rejected Payments</span>
                </a>
            @endif

            {{-- Documents — FIX: was missing entirely --}}
            @if(auth()->user()->hasAnyRole(['super_admin', 'membership_admin', 'payment_admin']))
                <div class="pt-4 pb-1 px-2 small fw-semibold text-uppercase" style="letter-spacing: 0.14em; color: rgba(255, 255, 255, 0.58);">
                    Documents
                </div>
                <a href="{{ route('admin.documents.queue') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.documents.queue') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.documents.queue') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.documents.queue') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-folder-open"></i>
                    </span>
                    <span>Document Queue</span>
                </a>
                <a href="{{ route('admin.documents.compose.agm') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.documents.compose.agm') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.documents.compose.agm') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.documents.compose.agm') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-bullhorn"></i>
                    </span>
                    <span>Compose AGM Notice</span>
                </a>
                <a href="{{ route('admin.documents.compose.minutes') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.documents.compose.minutes') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.documents.compose.minutes') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.documents.compose.minutes') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-file-lines"></i>
                    </span>
                    <span>Compose EC Minutes</span>
                </a>
            @endif

            {{-- Reports --}}
            @if(auth()->user()->hasAnyRole(['reports_admin', 'super_admin']))
                <div class="pt-4 pb-1 px-2 small fw-semibold text-uppercase" style="letter-spacing: 0.14em; color: rgba(255, 255, 255, 0.58);">
                    Reports
                </div>
                <a href="{{ route('admin.reports.index') }}"
                   class="d-flex align-items-center gap-3 px-3 py-3 rounded-4 text-decoration-none small fw-semibold {{ request()->routeIs('admin.reports.index') ? 'text-dark bg-white shadow-sm' : 'text-white' }}"
                   style="{{ request()->routeIs('admin.reports.index') ? '' : 'background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);' }}">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 38px; height: 38px; background: {{ request()->routeIs('admin.reports.index') ? 'rgba(26, 107, 60, 0.12)' : 'rgba(255, 255, 255, 0.12)' }};">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <span>Report Dashboard</span>
                </a>
            @endif

        </nav>
    </div>
</aside>
