<div class="row g-4">
    {{-- Page Header --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-brand-green">
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

    {{-- Guidance Column --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-info-circle text-muted me-2"></i>Application Guidance
            </div>
            <div class="card-body">
                <div class="vstack gap-4">
                    <div class="p-3 bg-light rounded">
                        <h6 class="fw-semibold mb-2 text-dark">Application Steps</h6>
                        <ol class="list-group list-group-numbered list-group-flush">
                            <li class="list-group-item bg-transparent border-0 px-0">
                                <span class="fw-semibold">Select a Category:</span>
                                <small class="d-block text-muted">Choose the membership type that best fits your professional standing.</small>
                            </li>
                            <li class="list-group-item bg-transparent border-0 px-0">
                                <span class="fw-semibold">Upload Documents:</span>
                                <small class="d-block text-muted">Provide your CV, qualifications, and proof of employment or study.</small>
                            </li>
                            <li class="list-group-item bg-transparent border-0 px-0">
                                <span class="fw-semibold">Submit & Wait:</span>
                                <small class="d-block text-muted">The Membership Committee will review your application and respond within 60 days.</small>
                            </li>
                        </ol>
                    </div>

                    <div class="p-3 bg-light rounded">
                        <h6 class="fw-semibold mb-2 text-dark">What Happens Next?</h6>
                        <ul class="small text-muted mb-0 ps-3">
                            <li>Your documents are reviewed by the Membership Committee.</li>
                            <li>You will receive a status update by email.</li>
                            <li>Once approved, you can proceed to payment.</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning small mb-0">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Ensure all uploaded documents are clear and legible to avoid delays.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Column --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex align-items-center justify-content-between">
                <span><i class="fas fa-pen-to-square text-muted me-2"></i>Application Details</span>
                <span class="badge bg-light text-dark border">Member Intake</span>
            </div>
            <div class="card-body">
                @if (session()->has('message'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>{{ session('message') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="submit" class="vstack gap-3">
                    <div>
                        <label class="form-label fw-semibold small text-muted">Select Membership Category</label>
                        <select wire:model.live="selected_category_id" class="form-select form-select-lg @error('selected_category_id') is-invalid @enderror">
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

@section('footer')
@endsection
                        @endif
                    </div>

                    {{-- File Inputs --}}
                    <div class="vstack gap-3">
                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-file-alt fa-fw text-muted"></i></span>
                            <input type="file" wire:model="cv_file" accept=".pdf,.doc,.docx"
                                   class="form-control @error('cv_file') is-invalid @enderror" placeholder="Upload CV / Resume">
                        </div>
                        @error('cv_file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div wire:loading wire:target="cv_file" class="form-text text-muted small">Uploading...</div>
                        @if ($this->cv_file && !$errors->has('cv_file'))
                            <div class="form-text text-success small">Selected: {{ $this->cv_file->getClientOriginalName() }}</div>
                        @endif

                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-certificate fa-fw text-muted"></i></span>
                            <input type="file" wire:model="certificates_file" accept=".pdf,.jpg,.jpeg,.png"
                                   class="form-control @error('certificates_file') is-invalid @enderror" placeholder="Upload Certificates">
                        </div>
                        @error('certificates_file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div wire:loading wire:target="certificates_file" class="form-text text-muted small">Uploading...</div>
                        @if ($this->certificates_file && !$errors->has('certificates_file'))
                            <div class="form-text text-success small">Selected: {{ $this->certificates_file->getClientOriginalName() }}</div>
                        @endif

                        <div class="input-group input-group-lg">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-briefcase fa-fw text-muted"></i></span>
                            <input type="file" wire:model="employment_letter_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="form-control @error('employment_letter_file') is-invalid @enderror" placeholder="Employment Letter / Student Proof">
                        </div>
                        @error('employment_letter_file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div wire:loading wire:target="employment_letter_file" class="form-text text-muted small">Uploading...</div>
                        @if ($this->employment_letter_file && !$errors->has('employment_letter_file'))
                            <div class="form-text text-success small">Selected: {{ $this->employment_letter_file->getClientOriginalName() }}</div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex flex-column flex-sm-row-reverse gap-2 pt-2">
                        <button type="submit" class="btn btn-brand-green btn-lg px-4" @disabled($categories->isEmpty()) wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-paper-plane me-2"></i>Submit Application</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i>Submitting...</span>
                        </button>
                        <a href="{{ route('member.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>