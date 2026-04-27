@extends('layouts.admin')

@section('title', 'Rejected Payments')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold mb-1">Rejected Payments</h1>
        <p class="text-muted mb-0">Payments that were rejected and require re-submission by members.</p>
    </div>
    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Pending
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($payments->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-check-circle fa-3x mb-3 text-secondary opacity-50"></i>
                <p class="mb-0 fs-5">No rejected payments at the moment.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Member</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Provider</th>
                            <th>Reference</th>
                            <th>Rejected On</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-semibold">{{ $payment->membership->user->name }}</div>
                                    <div class="small text-muted">{{ $payment->membership->user->email }}</div>
                                </td>
                                <td class="small">{{ $payment->membership->category->name }}</td>
                                <td class="fw-semibold">M{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ ucfirst($payment->provider) }}</td>
                                <td class="font-monospace small">{{ $payment->transaction_reference }}</td>
                                <td class="small text-muted">{{ $payment->updated_at->format('M d, Y') }}</td>
                                <td class="small text-muted" style="max-width: 200px;">
                                    {{ $payment->verification_notes ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $payments->links() }}</div>
        @endif
    </div>
</div>
@endsection
