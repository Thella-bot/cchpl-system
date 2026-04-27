@extends('layouts.app')

@section('title', 'Submit Resignation')

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #7f1d1d 0%, #dc2626 100%);">
            <div class="card-body py-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-door-open text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bold mb-1">Submit Resignation</h2>
                        <p class="text-white text-opacity-75 mb-0 small">
                            Notify CCHPL of your intention to resign from your membership and provide the necessary details.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-file-signature text-muted me-2"></i>Resignation Summary
            </div>
            <div class="card-body">
                <div class="p-3 bg-light rounded mb-3">
                    <small class="text-muted d-block mb-1">Membership Category</small>
                    <div class="fw-bold fs-5">{{ $membership->category->name }}</div>
                </div>

                <div class="alert alert-info small mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    The Secretary will acknowledge your resignation within 14 days in line with the CCHPL Bylaws.
                </div>

                @if ($balance > 0)
                    <div class="alert alert-warning small mb-3">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Outstanding Balance:</strong> M{{ number_format($balance, 2) }}
                        <div class="text-muted mt-1">Any outstanding fees will still need to be settled.</div>
                    </div>
                @endif

                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">Current Status</dt>
                    <dd class="col-7">{{ ucfirst($membership->status) }}</dd>

                    <dt class="col-5 text-muted">Annual Fee</dt>
                    <dd class="col-7">M{{ number_format($membership->category->annual_fee, 2) }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="fas fa-pen-to-square text-muted me-2"></i>Resignation Details</span>
                <span class="badge bg-light text-dark border">Formal Notice</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('member.resign.store') }}" class="d-flex flex-column gap-3">
                    @csrf

                    <div>
                        <label for="effective_date" class="form-label fw-semibold small text-muted">Effective Date</label>
                        <input id="effective_date" type="date" class="form-control form-control-lg @error('effective_date') is-invalid @enderror"
                               name="effective_date" value="{{ old('effective_date') }}" required min="{{ date('Y-m-d') }}">
                        @error('effective_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="reason_code" class="form-label fw-semibold small text-muted">Reason for Resignation</label>
                        <select id="reason_code" class="form-select form-select-lg @error('reason_code') is-invalid @enderror" name="reason_code">
                            <option value="">Select a reason (optional)</option>
                            @foreach ($reasonCodes as $code => $label)
                                <option value="{{ $code }}" {{ old('reason_code') === $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('reason_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="reason_notes" class="form-label fw-semibold small text-muted">Additional Notes</label>
                        <textarea id="reason_notes" class="form-control @error('reason_notes') is-invalid @enderror"
                                  name="reason_notes" rows="4" placeholder="Optional comments...">{{ old('reason_notes') }}</textarea>
                        @error('reason_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="p-3 bg-light rounded">
                        <div class="form-check">
                            <input class="form-check-input @error('confirm') is-invalid @enderror"
                                   type="checkbox" name="confirm" id="confirm" value="1" required>
                            <label class="form-check-label small" for="confirm">
                                I confirm that I understand my membership benefits will cease on the effective date.
                            </label>
                            @error('confirm')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 pt-2">
                        <button type="submit" class="btn btn-danger btn-lg px-4">
                            <i class="fas fa-paper-plane me-2"></i>Submit Resignation
                        </button>
                        <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
