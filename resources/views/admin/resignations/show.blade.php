@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('admin.resignations.index') }}" class="text-decoration-none"><i class="fas fa-arrow-left"></i> Back to List</a>
    </div>

    <div class="row">
        <!-- Resignation Details -->
        <div class="col-md-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Request Details</h5>
                    @if($resignation->status === 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @else
                        <span class="badge bg-success">Acknowledged</span>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 30%">Member:</th>
                            <td>{{ $resignation->user->name }} ({{ $resignation->user->email }})</td>
                        </tr>
                        <tr>
                            <th>Category:</th>
                            <td>{{ $resignation->membership?->category?->name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Requested Date:</th>
                            <td>{{ $resignation->effective_date->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Submission Date:</th>
                            <td>{{ $resignation->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Reason:</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $resignation->reason_code)) }}</td>
                        </tr>
                    </table>
                    
                    <div class="mt-3">
                        <h6>Member Comments:</h6>
                        <div class="p-3 bg-light rounded border">
                            {{ $resignation->reason_notes ?? 'No comments provided.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action / Status Panel -->
        <div class="col-md-5">
            @if($resignation->status === 'pending')
                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Acknowledge Resignation</h5>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">Acknowledging this request will set the membership status to 'Resigned', record the event in the audit log, and send a confirmation email to the member.</p>
                        
                        <form action="{{ route('admin.resignations.acknowledge', $resignation) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="balance_outstanding" class="form-label">Outstanding Balance (M)</label>
                                <input type="number" name="balance_outstanding" id="balance_outstanding"
                                       class="form-control" step="0.01" min="0"
                                       value="{{ $resignation->balance_outstanding ?? 0 }}" required>
                                <div class="form-text">Set to 0 if no balance owed.</div>
                            </div>
                            <div class="mb-3">
                                <label for="acknowledgement_notes" class="form-label">Acknowledgement Notes (Optional)</label>
                                <textarea name="acknowledgement_notes" id="acknowledgement_notes" class="form-control" rows="3" placeholder="Notes for internal record or inclusion in email..."></textarea>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="confirm_acknowledgement" id="confirm_acknowledgement" required>
                                <label class="form-check-label" for="confirm_acknowledgement">
                                    I confirm that this resignation should be processed.
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Process & Send Email</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Processing Info</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Acknowledged By:</strong> {{ $resignation->acknowledgedBy->name ?? 'System' }}</p>
                        <p><strong>Date:</strong> {{ $resignation->acknowledged_at ? $resignation->acknowledged_at->format('d M Y H:i') : 'N/A' }}</p>
                        <p><strong>Notes:</strong> {{ $resignation->acknowledgement_notes ?? 'None' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection