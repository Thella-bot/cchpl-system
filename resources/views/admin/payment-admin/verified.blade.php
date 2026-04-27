@extends('layouts.admin')

@section('title', 'Verified Payments')

@section('content')
<div class="mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold mb-1">Verified Payments</h1>
        <p class="text-muted mb-0">Payments that have been verified and approved.</p>
    </div>
    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Back to Pending
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($payments->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-receipt fa-3x mb-3 text-secondary opacity-50"></i>
                <p class="mb-0 fs-5">No verified payments found.</p>
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
                            <th>Receipt No.</th>
                            <th>Verified</th>
                            <th></th>
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
                                <td class="fw-semibold text-success">M{{ number_format($payment->amount, 2) }}</td>
                                <td>{{ ucfirst($payment->provider) }}</td>
                                <td class="font-monospace small">{{ $payment->receipt_number ?? '—' }}</td>
                                <td class="small text-muted">{{ $payment->verified_at?->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.payments.receipt', $payment->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-receipt me-1"></i>Receipt
                                    </a>
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
