<div class="row g-4">
    {{-- Page Header --}}
    <div class="col-12">
        <div class="stat-card animate-fade-in" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%); border: none;">
            <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                     style="width: 72px; height: 72px; flex-shrink: 0;">
                    <i class="fas fa-credit-card text-white fs-1"></i>
                </div>
                <div class="text-center text-md-start">
                    <h1 class="text-white fw-bold mb-2 fs-2">Make a Payment</h1>
                    <p class="text-white text-opacity-90 mb-0 lead">
                        Choose how you would like to pay: upload proof of an existing payment, or pay instantly via M-Pesa or EcoCash.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if ($memberships->isEmpty())
        <div class="col-12">
            <div class="admin-shell-card text-center py-5 text-muted">
                <i class="fas fa-circle-info fa-2x mb-3 opacity-50"></i>
                <p class="mb-2 fw-semibold">No eligible memberships available for payment.</p>
                <p class="small mb-3">You need an approved, suspended, or expired membership before initiating a payment.</p>
                <a href="{{ route('member.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        </div>
    @else
        <form wire:submit.prevent="submit" class="col-12">
            {{-- Payment Type Selection --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted">Payment Method</label>
                    <div class="card border-2 cursor-pointer h-100 @if($paymentType === 'manual') border-success @else border-transparent @endif"
                         style="cursor: pointer;"
                         wire:click="$set('paymentType', 'manual')">
                        <div class="card-body text-center py-4">
                            <div class="stat-icon mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.5rem;">
                                <i class="fas fa-upload"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Upload Payment Proof</h5>
                            <p class="text-muted small mb-0">I have already paid and want to submit proof for verification.</p>
                            @if($paymentType === 'manual')
                                <span class="badge bg-success mt-2">Selected</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-muted">Payment Method</label>
                    <div class="card border-2 cursor-pointer h-100 @if($paymentType === 'api') border-success @else border-transparent @endif"
                         style="cursor: pointer;"
                         wire:click="$set('paymentType', 'api')">
                        <div class="card-body text-center py-4">
                            <div class="stat-icon mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Pay Instantly</h5>
                            <p class="text-muted small mb-0">Pay now via M-Pesa STK Push or EcoCash.</p>
                            @if($paymentType === 'api')
                                <span class="badge bg-success mt-2">Selected</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @error('paymentType')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

            <div class="row g-4">
                {{-- Membership & Amount --}}
                <div class="col-lg-5">
                    <div class="admin-shell-card h-100">
                        <h5 class="fw-semibold mb-3">
                            <i class="fas fa-id-card text-muted me-2"></i>Membership Details
                        </h5>
                        <div class="mb-3">
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

                        <div class="mb-3">
                            <label for="amount" class="form-label fw-semibold small text-muted">Amount (M)</label>
                            <input id="amount" type="number" step="0.01" min="0.01"
                                   wire:model="amount" class="form-control form-control-lg @error('amount') is-invalid @enderror" required>
                            @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="provider" class="form-label fw-semibold small text-muted">Payment Provider</label>
                            <select id="provider" wire:model="provider" class="form-select form-select-lg @error('provider') is-invalid @enderror">
                                <option value="">Select provider</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="ecocash">EcoCash</option>
                            </select>
                            @error('provider')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="purpose" class="form-label fw-semibold small text-muted">Purpose</label>
                            <select id="purpose" wire:model="purpose" class="form-select form-select-lg @error('purpose') is-invalid @enderror">
                                <option value="">Select purpose</option>
                                @foreach ($purposeOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('purpose')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Payment Action Panel --}}
                <div class="col-lg-7">
                    <div class="admin-shell-card h-100">
                        @if ($paymentType === 'manual')
                            <h5 class="fw-semibold mb-3">
                                <i class="fas fa-file-upload text-muted me-2"></i>Upload Payment Proof
                            </h5>
                            <p class="text-muted small mb-4">
                                Upload a screenshot or photo of your payment confirmation. Our finance team will review it within 2 business days.
                            </p>

                            <div class="mb-3">
                                <label for="proofFile" class="form-label fw-semibold small text-muted">Payment Proof</label>
                                <input id="proofFile" type="file" accept="image/jpeg,image/png"
                                       wire:model="proofFile" class="form-control form-control-lg @error('proofFile') is-invalid @enderror">
                                <div class="form-text small text-muted">JPG or PNG, max 5 MB. Make sure the reference and amount are clearly visible.</div>
                                @error('proofFile')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="pt-2">
                                <button type="button" wire:click="generateInstructions" class="btn btn-outline-primary btn-lg px-4">
                                    <i class="fas fa-list-ol me-2"></i>Generate Payment Instructions
                                </button>
                            </div>

                            @if ($showInstructions)
                                <div class="card bg-light border mb-0 mt-3">
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

                                <div class="d-flex flex-column flex-sm-row-reverse gap-2 pt-3 mt-3">
                                    <button type="submit" class="btn btn-primary btn-lg px-4">
                                        <i class="fas fa-check me-2"></i>Submit Payment Proof
                                    </button>
                                    <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                                        Cancel
                                    </a>
                                </div>
                            @endif
                        @else
                            <h5 class="fw-semibold mb-3">
                                <i class="fas fa-bolt text-warning me-2"></i>Instant Payment
                            </h5>
                            <p class="text-muted small mb-4">
                                You will receive a prompt on your phone to authorize the payment. Ensure your phone is nearby and has sufficient funds.
                            </p>

                            @if ($paymentInitiated && $stkResponse)
                                <div class="alert alert-success border-0">
                                    <i class="fas fa-check-circle me-1"></i>
                                    <strong>Payment initiated!</strong> Please check your phone to complete the transaction.
                                    @if(isset($stkResponse['CheckoutRequestID']))
                                        <div class="small mt-2 text-muted">Request ID: {{ $stkResponse['CheckoutRequestID'] }}</div>
                                    @endif
                                </div>
                            @else
                                <div class="d-grid gap-2">
                                    <button type="button" wire:click="initiateApiPayment" class="btn btn-primary btn-lg px-4">
                                        <i class="fas fa-paper-plane me-2"></i>Pay Now
                                    </button>
                                    <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                                        Cancel
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>