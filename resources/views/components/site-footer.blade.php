<footer class="site-footer mt-5">
    <div class="site-footer-topbar"></div>
    <div class="container py-5">
        <div class="row g-4 g-lg-5">
            {{-- Brand & Mission --}}
            <div class="col-lg-4">
                <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-3">
                    <img src="{{ asset('images/logo/cchpl-alt-logo.png') }}" alt="CCHPL Logo" height="40">
                    <span class="site-footer-brand">CCHPL</span>
                </a>
                <p class="site-footer-text mb-3">
                    The professional home for culinary and hospitality practitioners, students, and institutions in Lesotho — fostering standards, growth, and service excellence.
                </p>
                <div class="d-flex gap-2">
                    <a href="mailto:admin@cchpl.org.ls" class="site-footer-social" aria-label="Email us">
                        <i class="fas fa-envelope"></i>
                    </a>
                    <a href="{{ route('register') }}" class="site-footer-social" aria-label="Join the Council">
                        <i class="fas fa-user-plus"></i>
                    </a>
                    <a href="{{ route('member.dashboard') }}" class="site-footer-social" aria-label="Member dashboard">
                        <i class="fas fa-right-to-bracket"></i>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="col-6 col-lg-3">
                <h6 class="site-footer-heading">Explore</h6>
                <ul class="list-unstyled site-footer-links mb-0">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><a href="#about">About CCHPL</a></li>
                    <li><a href="#join">How to Join</a></li>
                    <li><a href="#membership">Membership Tiers</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>

            {{-- Member Links --}}
            <div class="col-6 col-lg-2">
                <h6 class="site-footer-heading">Members</h6>
                <ul class="list-unstyled site-footer-links mb-0">
                    @guest
                        <li><a href="{{ route('login') }}">Member Login</a></li>
                        <li><a href="{{ route('register') }}">Register</a></li>
                    @else
                        <li><a href="{{ route('member.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('member.profile') }}">My Profile</a></li>
                        @if (Auth::user()->isAdmin())
                            <li><a href="{{ auth()->user()->adminHome() }}">Admin Panel</a></li>
                        @endif
                    @endguest
                    <li><a href="{{ route('membership.apply') }}">Apply for Membership</a></li>
                </ul>
            </div>

            {{-- Contact --}}
            <div class="col-lg-3">
                <h6 class="site-footer-heading">Get in Touch</h6>
                <ul class="list-unstyled site-footer-contact mb-0">
                    <li>
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:admin@cchpl.org.ls">admin@cchpl.org.ls</a>
                    </li>
                    <li>
                        <i class="fas fa-location-dot me-2"></i>
                        <span>Maseru, Lesotho</span>
                    </li>
                    <li>
                        <i class="fas fa-users-gear me-2"></i>
                        <span>Culinary &amp; Hospitality Professional Council</span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="site-footer-divider">

        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
            <small class="site-footer-text mb-0">
                &copy; {{ date('Y') }} Council for Culinary and Hospitality Professionals Lesotho. All rights reserved.
            </small>
            <small class="site-footer-text mb-0">
                Committed to professional standards in culinary &amp; hospitality.
            </small>
        </div>
    </div>
</footer>
