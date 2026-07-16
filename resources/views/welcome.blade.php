@extends('layouts.app')

@section('title', 'Welcome to CCHPL')

@section('content')
<div class="d-flex flex-column gap-4">

    {{-- Hero Section --}}
    <section class="card border-0 shadow-sm overflow-hidden site-hero text-white">
        <div class="card-body px-4 px-lg-5 py-5 text-center">
            <h1 class="display-4 fw-bold">Council for Culinary and Hospitality Professionals Lesotho</h1>
            <p class="lead text-white-75 mx-auto" style="max-width: 800px;">
                The professional home for culinary and hospitality practitioners, students, and institutions in Lesotho, dedicated to fostering standards, growth, and service excellence.
            </p>
            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4">
                    <i class="fas fa-user-plus me-2"></i>Join the Council
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4">
                    <i class="fas fa-right-to-bracket me-2"></i>Member Login
                </a>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section id="about" class="card border-0 shadow-sm scroll-mt-6">
        <div class="card-body p-4 p-lg-5">
            <div class="text-center">
                <div class="site-section-title mb-2">About CCHPL</div>
                <h2 class="fw-bold mb-3">A Stronger Professional Voice</h2>
                <p class="text-muted mx-auto" style="max-width: 800px;">
                    CCHPL exists to strengthen the culinary and hospitality profession by providing members with recognition, structure, access to information, and opportunities for professional participation and development.
                </p>
            </div>
            <div class="row g-4 mt-4 text-center">
                <div class="col-md-4">
                    <div class="p-3">
                        <div class="display-4 text-brand-green mb-2"><i class="fas fa-scale-balanced"></i></div>
                        <h3 class="h5 fw-bold">Standards & Governance</h3>
                        <p class="text-muted mb-0">Promoting accountability and fair professional administration.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <div class="display-4 text-brand-green mb-2"><i class="fas fa-graduation-cap"></i></div>
                        <h3 class="h5 fw-bold">Training & Growth</h3>
                        <p class="text-muted mb-0">Creating pathways for development for students and professionals.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3">
                        <div class="display-4 text-brand-green mb-2"><i class="fas fa-handshake"></i></div>
                        <h3 class="h5 fw-bold">Community & Representation</h3>
                        <p class="text-muted mb-0">Connecting members within a visible and supportive industry community.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How to Join & Benefits --}}
    <section id="join" class="card border-0 shadow-sm scroll-mt-6">
        <div class="card-body p-4 p-lg-5">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div class="site-section-title mb-2">Application Process</div>
                    <h2 class="fw-bold mb-3">How to become a member</h2>
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 bg-light rounded d-flex gap-3">
                            <div class="rounded-circle bg-brand-green text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">1</div>
                            <div>
                                <div class="fw-semibold">Create your account</div>
                                <div class="small text-muted">Register on the portal and verify your email address.</div>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded d-flex gap-3">
                            <div class="rounded-circle bg-brand-green text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">2</div>
                            <div>
                                <div class="fw-semibold">Submit your membership application</div>
                                <div class="small text-muted">Choose a category and upload your supporting documents.</div>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded d-flex gap-3">
                            <div class="rounded-circle bg-brand-green text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">3</div>
                            <div>
                                <div class="fw-semibold">Await committee review</div>
                                <div class="small text-muted">Applications are reviewed and the outcome is shared with you by email.</div>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded d-flex gap-3">
                            <div class="rounded-circle bg-brand-green text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px;">4</div>
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
                                    <div class="text-brand-green mb-2"><i class="fas fa-certificate fa-lg"></i></div>
                                    <h3 class="h6 fw-bold">Recognition</h3>
                                    <p class="small text-muted mb-0">Structured membership status and access to official council documents.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <div class="text-brand-green mb-2"><i class="fas fa-users fa-lg"></i></div>
                                    <h3 class="h6 fw-bold">Professional network</h3>
                                    <p class="small text-muted mb-0">Connection to fellow practitioners, administrators, and institutional stakeholders.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <div class="text-brand-green mb-2"><i class="fas fa-file-lines fa-lg"></i></div>
                                    <h3 class="h6 fw-bold">Member services</h3>
                                    <p class="small text-muted mb-0">Access to payment history, receipts, certificates, and profile management tools.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body">
                                    <div class="text-brand-green mb-2"><i class="fas fa-briefcase fa-lg"></i></div>
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

    {{-- Membership Tiers Section --}}
    @if ($categories->isNotEmpty())
        <section id="membership" class="card border-0 shadow-sm scroll-mt-6">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <div class="site-section-title mb-2">Membership Tiers</div>
                    <h2 class="fw-bold mb-3">Choose the right membership</h2>
                    <p class="text-muted mx-auto" style="max-width: 800px;">
                        Transparent annual fees across our membership categories. Select the tier that fits your professional standing when you apply.
                    </p>
                </div>
                <div class="row g-4 justify-content-center">
                    @foreach ($categories as $category)
                        <div class="col-sm-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm pricing-card text-center">
                                <div class="card-body d-flex flex-column p-4">
                                    <h3 class="h5 fw-bold mb-1">{{ $category->name }}</h3>
                                    <div class="pricing-price mb-1">
                                        <span class="fw-bold">M{{ number_format($category->annual_fee, 2) }}</span>
                                        <span class="text-muted small">/year</span>
                                    </div>
                                    @if ($category->joining_fee)
                                        <div class="small text-muted mb-3">Joining fee: M{{ number_format($category->joining_fee, 2) }}</div>
                                    @else
                                        <div class="small text-muted mb-3">&nbsp;</div>
                                    @endif
                                    <ul class="list-unstyled text-start small mb-4 flex-grow-1">
                                        <li class="d-flex align-items-start gap-2 mb-2">
                                            <i class="fas fa-{{ $category->voting_rights ? 'check' : 'xmark' }} mt-1 {{ $category->voting_rights ? 'text-brand-green' : 'text-muted' }}"></i>
                                            <span>{{ $category->voting_rights ? 'Voting rights' : 'No voting rights' }}</span>
                                        </li>
                                        @if ($category->eligibility_criteria)
                                            <li class="d-flex align-items-start gap-2 mb-2">
                                                <i class="fas fa-circle-info mt-1 text-brand-green"></i>
                                                <span>{{ $category->eligibility_criteria }}</span>
                                            </li>
                                        @endif
                                        @if ($category->other_notes)
                                            <li class="d-flex align-items-start gap-2">
                                                <i class="fas fa-circle-info mt-1 text-brand-green"></i>
                                                <span>{{ $category->other_notes }}</span>
                                            </li>
                                        @endif
                                    </ul>
                                    <a href="{{ route('register') }}" class="btn btn-outline-brand-green w-100">
                                        Apply for this tier
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Contact Section --}}
    <section id="contact" class="card border-0 shadow-sm scroll-mt-6">
        <div class="card-body p-4 p-lg-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="site-section-title mb-2">Contact Information</div>
                    <h2 class="fw-bold mb-3">Get in Touch</h2>
                    <p class="text-muted">
                        For any inquiries, please feel free to contact us. We are here to assist you with any questions you may have about the council and our activities.
                    </p>
                    <div class="d-flex flex-column gap-2 mt-4">
                        <div><i class="fas fa-envelope me-2 text-brand-green"></i>admin@cchpl.org.ls</div>
                        <div><i class="fas fa-location-dot me-2 text-brand-green"></i>Maseru, Lesotho</div>
                        <div><i class="fas fa-users-gear me-2 text-brand-green"></i>Culinary and Hospitality Professional Council</div>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <a href="{{ route('register') }}" class="btn btn-brand-green btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Join the Council
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection