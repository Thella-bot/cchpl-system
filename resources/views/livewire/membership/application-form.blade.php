<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1a6b3c 0%, #2d9b5a 100%);">
            <div class="card-body py-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                         style="width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-file-signature text-white fs-4"></i>
                    </div>
                    <div>
                        <h2 class="text-white fw-bold mb-1">Membership Application</h2>
                        <p class="text-white text-opacity-75 mb-0 small">
                            Submit your details and supporting documents for CCHPL membership review.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-id-card text-muted me-2"></i>Application Guidance
            </div>
            <div class="card-body">
                <div class="p-3 bg-light rounded mb-3">
                    <small class="text-muted d-block mb-1">Before you submit</small>
                    <div class="fw-semibold">Choose the category that best matches your professional standing.</div>
                </div>

                <div class="d-flex flex-column gap-2">
                    <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px;">
                            <i class="fas fa-list-check text-success"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Select a category</div>
                            <div class="text-muted small">Each category has a different fee and eligibility requirement.</div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px;">
                            <i class="fas fa-file-upload text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Upload your documents</div>
                            <div class="text-muted small">Provide your CV, qualifications, and proof of employment or study.</div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width: 42px; height: 42px;">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">Wait for review</div>
                            <div class="text-muted small">The Membership Committee will communicate the decision within 60 days.</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-primary bg-opacity-10 rounded">
                    <h6 class="fw-semibold mb-2 text-primary">What happens next?</h6>
                    <ul class="small text-muted mb-0 ps-3">
                        <li>Your documents are reviewed by the Membership Committee.</li>
                        <li>You receive a status update by email.</li>
                        <li>Once approved, you can proceed to payment.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="fas fa-pen-to-square text-muted me-2"></i>Application Details</span>
                <span class="badge bg-light text-dark border">Member Intake</span>
            </div>
            <div class="card-body">
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="submit" class="d-flex flex-column gap-3">
                    <div>
                        <label class="form-label fw-semibold small text-muted">Select Membership Category</label>
                        <select wire:model="selected_category_id" class="form-select form-select-lg @error('selected_category_id') is-invalid @enderror">
                            <option value="">-- Choose a category --</option>
                            @forelse($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }} (M{{ number_format($cat->annual_fee, 2) }}/year)</option>
                            @empty
                                <option value="" disabled>No membership categories available yet</option>
                            @endforelse
                        </select>
                        @error('selected_category_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @if($categories->isEmpty())
                            <div class="form-text text-danger">
                                Membership categories have not been seeded yet.
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="form-label fw-semibold small text-muted">Upload CV / Resume</label>
                        <input type="file" wire:model="cv_file" accept=".pdf,.doc,.docx"
                               class="form-control form-control-lg @error('cv_file') is-invalid @enderror">
                        @error('cv_file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @if ($this->cv_file)
                            <div class="form-text text-success">Selected: {{ $this->cv_file->getClientOriginalName() }}</div>
                        @endif
                    </div>

                    <div>
                        <label class="form-label fw-semibold small text-muted">Upload Certificates / Qualifications</label>
                        <input type="file" wire:model="certificates_file" accept=".pdf,.jpg,.jpeg,.png"
                               class="form-control form-control-lg @error('certificates_file') is-invalid @enderror">
                        @error('certificates_file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @if ($this->certificates_file)
                            <div class="form-text text-success">Selected: {{ $this->certificates_file->getClientOriginalName() }}</div>
                        @endif
                    </div>

                    <div>
                        <label class="form-label fw-semibold small text-muted">Employment Letter / Student Proof</label>
                        <input type="file" wire:model="employment_letter_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                               class="form-control form-control-lg @error('employment_letter_file') is-invalid @enderror">
                        @error('employment_letter_file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @if ($this->employment_letter_file)
                            <div class="form-text text-success">Selected: {{ $this->employment_letter_file->getClientOriginalName() }}</div>
                        @endif
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 pt-2">
                        <button type="submit" class="btn btn-success btn-lg px-4" @disabled($categories->isEmpty())>
                            <i class="fas fa-paper-plane me-2"></i>Submit Application
                        </button>
                        <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
