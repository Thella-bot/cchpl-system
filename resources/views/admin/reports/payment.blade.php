@extends('layouts.admin')
@section('title', 'Payment Report')

@section('content')
<div class="admin-shell-card">
    <div class="mb-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-decoration-none text-muted">&larr; Reports dashboard</a>
            <h1 class="h3 fw-bold mb-1">Payment Report</h1>
            <p class="text-muted mb-0">Generated: {{ now()->format('d F Y, H:i') }}</p>
        </div>
        <a href="{{ route('admin.reports.export.payments') }}" class="btn btn-primary">
            Export CSV
        </a>
    </div>

    {{-- Revenue summary --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div>
                        <div class="stat-label">Total revenue (all time)</div>
                        <div class="stat-value">M{{ number_format($totalRevenue, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <div>
                        <div class="stat-label">M-Pesa revenue</div>
                        <div class="stat-value">M{{ number_format($mpesaTotal, 2) }}</div>
                        @if($totalRevenue > 0)
                            <small class="text-muted">{{ round(($mpesaTotal / $totalRevenue) * 100, 1) }}% of total</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="stat-label">EcoCash revenue</div>
                        <div class="stat-value">M{{ number_format($ecocashTotal, 2) }}</div>
                        @if($totalRevenue > 0)
                            <small class="text-muted">{{ round(($ecocashTotal / $totalRevenue) * 100, 1) }}% of total</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <div class="stat-label">Pending verification</div>
                        <div class="stat-value">{{ number_format($pendingCount) }}</div>
                        <a href="{{ route('admin.payments.index') }}" class="text-decoration-none text-brand-green small">Review &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment issues --}}
    @if ($rejectedCount > 0 || $voidedCount > 0)
    <div class="row g-3 mb-4">
        <div class="col-sm-6">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div>
                        <div class="stat-label">Rejected payments</div>
                        <div class="stat-value text-danger">{{ number_format($rejectedCount) }}</div>
                        <small class="text-muted">Member submitted invalid proof</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="stat-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #6b7280, #4b5563); box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);">
                        <i class="fas fa-void"></i>
                    </div>
                    <div>
                        <div class="stat-label">Voided (abandoned references)</div>
                        <div class="stat-value">{{ number_format($voidedCount) }}</div>
                        <small class="text-muted">No proof uploaded within 48 hours</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Monthly revenue table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Monthly revenue (last 12 months)</div>
        @if ($monthlyRevenue->isEmpty())
            <div class="text-center py-5 text-muted">No verified payments on record.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Month</th>
                            <th class="text-end">Transactions</th>
                            <th class="text-end">M-Pesa (M)</th>
                            <th class="text-end">EcoCash (M)</th>
                            <th class="text-end">Total (M)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($monthlyRevenue as $row)
                        <tr>
                            <td class="fw-semibold">{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('F Y') }}</td>
                            <td class="text-end">{{ number_format($row->count) }}</td>
                            <td class="text-end">M{{ number_format($mpesaMonthly[$row->month]->total ?? 0, 2) }}</td>
                            <td class="text-end">M{{ number_format($ecocashMonthly[$row->month]->total ?? 0, 2) }}</td>
                            <td class="text-end fw-bold">M{{ number_format($row->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td class="fw-bold" colspan="2">12-month total</td>
                            <td class="text-end fw-bold">M{{ number_format($mpesaMonthly->sum('total'), 2) }}</td>
                            <td class="text-end fw-bold">M{{ number_format($ecocashMonthly->sum('total'), 2) }}</td>
                            <td class="text-end fw-bold">M{{ number_format($monthlyRevenue->sum('total'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
