@extends('layouts.app')

@section('title', 'CCHPL Lesotho')

@section('content')
<style>
    .site-hero {
        background:
            radial-gradient(circle at top left, rgba(255, 255, 255, 0.18), transparent 38%),
            linear-gradient(135deg, #123b28 0%, #1a6b3c 45%, #2d9b5a 100%);
    }
    .site-glass-card {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(6px);
    }
    .site-section-title {
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: #1a6b3c;
        font-weight: 700;
    }
</style>

<div class="d-flex flex-column gap-4">
    <section class="card border-0 shadow-sm overflow-hidden site-hero">
        <div class="card-body px-4 px-lg-5 py-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <div class="d-inline-flex align-items-center rounded-pill px-3 py-2 mb-3 text-white small site-glass-card">
                        Professional Council for Culinary and Hospitality Excellence in Lesotho
                    </div>
                    <h1 class="display-5 fw-bold text-white mb-3">Council for Culinary and Hospitality Professionals Lesotho</h1>
                    <p class="lead text-white text-opacity-75 mb-4">
                        A professional home for culinary and hospitality practitioners, students, and institutions committed to standards, growth, and service in Lesotho.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-user-plus me-2"></i>Join the Council
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-right-to-bracket me-2"></i>Member Login
                        </a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg site-glass-card text-white">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3">What We Support</h2>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Professional standards</div>
                                        <div class="small text-white text-opacity-75">Promoting ethical conduct, competence, and credibility across the profession.</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="fas fa-people-group"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Member development</div>
                                        <div class="small text-white text-opacity-75">Supporting training, mentorship, networking, and career advancement.</div>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">Industry collaboration</div>
                                        <div class="small text-white text-opacity-75">Connecting professionals, institutions, employers, and partners in hospitality.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="site-section-title mb-2">About CCHPL</div>
                    <h2 class="fw-bold mb-3">A stronger professional voice for culinary and hospitality practice</h2>
                    <p class="text-muted mb-0">
                        CCHPL exists to strengthen the profession by supporting members with recognition, structure, access to information, and opportunities for professional participation.
                    </p>
                </div>
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <div class="text-success mb-2"><i class="fas fa-scale-balanced fa-lg"></i></div>
                                    <h3 class="h6 fw-bold">Standards and governance</h3>
                                    <p class="small text-muted mb-0">Encouraging accountability, structured review, and fair professional administration.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <div class="text-primary mb-2"><i class="fas fa-graduation-cap fa-lg"></i></div>
                                    <h3 class="h6 fw-bold">Training and growth</h3>
                                    <p class="small text-muted mb-0">Creating a pathway for students, trainees, and experienced professionals to develop.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <div class="text-warning mb-2"><i class="fas fa-handshake fa-lg"></i></div>
                                    <h3 class="h6 fw-bold">Community and representation</h3>
                                    <p class="small text-muted mb-0">Helping members participate in a visible and connected industry community.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3 mb-4">
                <div>
                    <div class="site-section-title mb-2">Membership Categories</div>
                    <h2 class="fw-bold mb-1">Find the category that fits your journey</h2>
                    <p class="text-muted mb-0">Public-facing categories available for application through the member portal.</p>
                </div>
                <a href="{{ route('register') }}" class="btn btn-success">
                    <i class="fas fa-user-plus me-2"></i>Create Account to Apply
                </a>
            </div>

            <div class="row g-3">
                @forelse ($categories as $category)
                    <div class="col-md-6 col-xl-3">
                        <div class="card h-100 border-0 bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h3 class="h5 fw-bold mb-0">{{ $category->name }}</h3>
                                    <span class="badge {{ $category->voting_rights ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $category->voting_rights ? 'Voting' : 'Non-voting' }}
                                    </span>
                                </div>
                                <div class="p-3 rounded bg-white border mb-3">
                                    <small class="text-muted d-block">Annual Fee</small>
                                    <div class="fw-bold fs-5">M{{ number_format($category->annual_fee, 2) }}</div>
                                </div>
                                <p class="small text-muted mb-2"><strong>Eligibility:</strong> {{ $category->eligibility_criteria }}</p>
                                <p class="small text-muted mb-0">{{ $category->other_notes }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            Membership categories are not available yet. Please seed the membership categories to display them here.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="site-section-title mb-2">Application Process</div>
                    <h2 class="fw-bold mb-3">How to become a member</h2>
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 bg-light rounded d-flex gap-3">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">1</div>
                            <div>
                                <div class="fw-semibold">Create your account</div>
                                <div class="small text-muted">Register on the portal and verify your email address.</div>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded d-flex gap-3">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">2</div>
                            <div>
                                <div class="fw-semibold">Submit your membership application</div>
                                <div class="small text-muted">Choose a category and upload your supporting documents.</div>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded d-flex gap-3">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">3</div>
                            <div>
                                <div class="fw-semibold">Await committee review</div>
                                <div class="small text-muted">Applications are reviewed and the outcome is shared with you by email.</div>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded d-flex gap-3">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">4</div>
                            <div>
                                <div class="fw-semibold">Initiate payment after approval</div>
                                <div class="small text-muted">Use the portal to generate payment instructions and complete the process.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="site-section-title mb-2">Member Benefits</div>
                    <h2 class="fw-bold mb-3">What members gain</h2>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <div class="text-success mb-2"><i class="fas fa-certificate fa-lg"></i></div>
                                    <h3 class="h6 fw-bold">Recognition</h3>
                                    <p class="small text-muted mb-0">Structured membership status and access to official council documents.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <div class="text-primary mb-2"><i class="fas fa-users fa-lg"></i></div>
                                    <h3 class="h6 fw-bold">Professional network</h3>
                                    <p class="small text-muted mb-0">Connection to fellow practitioners, administrators, and institutional stakeholders.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <div class="text-warning mb-2"><i class="fas fa-file-lines fa-lg"></i></div>
                                    <h3 class="h6 fw-bold">Member services</h3>
                                    <p class="small text-muted mb-0">Access to payment history, receipts, certificates, and profile management tools.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <div class="text-danger mb-2"><i class="fas fa-briefcase fa-lg"></i></div>
                                    <h3 class="h6 fw-bold">Professional visibility</h3>
                                    <p class="small text-muted mb-0">A clearer pathway to participation and standing within the hospitality profession.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="card border-0 shadow-sm">
        <div class="card-body p-4 p-lg-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="site-section-title mb-2">Governance and Information</div>
                    <h2 class="fw-bold mb-3">Built around structured administration</h2>
                    <p class="text-muted">
                        The platform supports document review, payments administration, membership oversight, reporting, and official communication workflows through role-based administration.
                    </p>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded h-100">
                                <div class="fw-semibold mb-1">Administration</div>
                                <div class="small text-muted">Super admin, membership, payment, finance, and reports roles.</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded h-100">
                                <div class="fw-semibold mb-1">Document workflows</div>
                                <div class="small text-muted">Support for AGM notices, EC minutes, review queues, and member documents.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 text-white shadow-sm" style="background: linear-gradient(135deg, #113826 0%, #174b32 100%);">
                        <div class="card-body p-4">
                            <h2 class="h4 fw-bold mb-3">Contact and Next Step</h2>
                            <p class="text-white text-opacity-75 small">
                                Ready to participate in the council? Create your portal account and begin your application.
                            </p>
                            <div class="d-flex flex-column gap-2 mb-4">
                                <div><i class="fas fa-envelope me-2"></i>admin@cchpl.org.ls</div>
                                <div><i class="fas fa-location-dot me-2"></i>Maseru, Lesotho</div>
                                <div><i class="fas fa-users-gear me-2"></i>Culinary and Hospitality Professional Council</div>
                            </div>
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <a href="{{ route('register') }}" class="btn btn-light">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </a>
                                <a href="{{ route('login') }}" class="btn btn-outline-light">
                                    <i class="fas fa-right-to-bracket me-2"></i>Login
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
