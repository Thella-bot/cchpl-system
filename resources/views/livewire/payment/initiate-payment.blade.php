<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%);">
            <div class="card-body py-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-credit-card text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bold mb-1">Initiate Payment</h2>
                        <p class="text-white text-opacity-75 mb-0 small">
                            Generate payment instructions, use the reference provided, and submit your payment for verification.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-wallet text-muted me-2"></i>Payment Guidance
            </div>
            <div class="card-body">
                <div class="p-3 bg-light rounded mb-3">
                    <small class="text-muted d-block mb-1">How this works</small>
                    <div class="fw-semibold">Generate your instructions first, then use the same reference when completing the payment.</div>
                </div>

                <div class="d-flex flex-column gap-2">
                    <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px;">
                            <i class="fas fa-list-check text-success"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Choose a membership</div>
                            <div class="text-muted small">Only memberships eligible for payment appear here.</div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px;">
                            <i class="fas fa-mobile-screen-button text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Generate instructions</div>
                            <div class="text-muted small">A unique reference is created for M-Pesa or EcoCash.</div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px;">
                            <i class="fas fa-hourglass-half text-warning"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Wait for verification</div>
                            <div class="text-muted small">Finance will review and confirm the payment after submission.</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info py-2 mt-3 mb-0 small">
                    <i class="fas fa-info-circle me-1"></i>
                    Use the generated reference exactly as shown so your payment can be matched quickly.
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="fas fa-money-check-dollar text-muted me-2"></i>Payment Details</span>
                <span class="badge bg-light text-dark border">Finance Workflow</span>
            </div>

            <div class="card-body">
                @if(session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(session()->has('message'))
                    <div class="alert alert-success">{{ session('message') }}</div>
                @endif

                @if ($memberships->isEmpty())
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-circle-info fa-2x mb-3 opacity-50"></i>
                        <p class="mb-2 fw-semibold">No eligible memberships available for payment.</p>
                        <p class="small mb-3">You need an approved, suspended, or expired membership before initiating a payment.</p>
                        <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                        </a>
                    </div>
                @else
                    <form wire:submit.prevent="submit" class="d-flex flex-column gap-3">
                        <div>
                            <label for="membershipId" class="form-label fw-semibold small text-muted">Membership</label>
                            <select id="membershipId" wire:model.live="membershipId" class="form-select form-select-lg @error('membershipId') is-invalid @enderror">
                                @foreach ($memberships as $membership)
                                    <option value="{{ $membership->id }}">
                                        {{ $membership->category->name }} · {{ ucfirst($membership->status) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('membershipId')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label for="amount" class="form-label fw-semibold small text-muted">Amount (M)</label>
                            <input id="amount" type="number" step="0.01" min="0.01"
                                   wire:model="amount" class="form-control form-control-lg @error('amount') is-invalid @enderror" required>
                            @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label for="provider" class="form-label fw-semibold small text-muted">Payment Provider</label>
                            <select id="provider" wire:model="provider" class="form-select form-select-lg @error('provider') is-invalid @enderror">
                                <option value="">Select provider</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="ecocash">EcoCash</option>
                            </select>
                            @error('provider')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label for="purpose" class="form-label fw-semibold small text-muted">Purpose</label>
                            <select id="purpose" wire:model="purpose" class="form-select form-select-lg @error('purpose') is-invalid @enderror">
                                <option value="">Select purpose</option>
                                @foreach ($purposeOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('purpose')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="pt-2">
                            <button type="button" wire:click="generateInstructions" class="btn btn-outline-primary btn-lg px-4">
                                <i class="fas fa-list-ol me-2"></i>Generate Payment Instructions
                            </button>
                        </div>

                        @if ($showInstructions)
                            <div class="card bg-light border mb-0">
                                <div class="card-header fw-semibold">
                                    <i class="fas fa-file-lines me-1"></i>Payment Instructions
                                </div>
                                <div class="card-body">
                                    <pre class="mb-3 small" style="white-space: pre-wrap;">{{ $paymentInstructions }}</pre>
                                    <div class="p-3 bg-white rounded border">
                                        <small class="text-muted d-block mb-1">Reference</small>
                                        <span class="fw-bold font-monospace">{{ $reference }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 pt-2">
                                <button type="submit" class="btn btn-success btn-lg px-4">
                                    <i class="fas fa-check me-2"></i>Confirm Payment Initiation
                                </button>
                                <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        @endif
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
