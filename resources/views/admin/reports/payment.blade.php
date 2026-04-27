@extends('layouts.admin')
@section('title', 'Payment Report')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Reports dashboard</a>
            <h1 class="text-3xl font-bold text-gray-800 mt-1">Payment Report</h1>
            <p class="text-gray-500 text-sm mt-1">Generated: {{ now()->format('d F Y, H:i') }}</p>
        </div>
        <a href="{{ route('admin.reports.export.payments') }}"
           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm text-gray-700 font-medium">
            Export CSV
        </a>
    </div>

    {{-- ── Revenue summary ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded shadow p-5">
            <p class="text-sm text-gray-500">Total revenue (all time)</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">M{{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded shadow p-5">
            <p class="text-sm text-gray-500">M-Pesa revenue</p>
            <p class="text-2xl font-bold text-green-700 mt-1">M{{ number_format($mpesaTotal, 2) }}</p>
            @if($totalRevenue > 0)
                <p class="text-xs text-gray-400 mt-1">{{ round(($mpesaTotal / $totalRevenue) * 100, 1) }}% of total</p>
            @endif
        </div>
        <div class="bg-white rounded shadow p-5">
            <p class="text-sm text-gray-500">EcoCash revenue</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">M{{ number_format($ecocashTotal, 2) }}</p>
            @if($totalRevenue > 0)
                <p class="text-xs text-gray-400 mt-1">{{ round(($ecocashTotal / $totalRevenue) * 100, 1) }}% of total</p>
            @endif
        </div>
        <div class="bg-white rounded shadow p-5">
            <p class="text-sm text-gray-500">Pending verification</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($pendingCount) }}</p>
            <a href="{{ route('admin.payments.index') }}" class="text-xs text-blue-600 hover:underline">Review →</a>
        </div>
    </div>

    {{-- ── Payment issues ── --}}
    @if ($rejectedCount > 0 || $voidedCount > 0)
    <div class="grid grid-cols-2 gap-5 mb-8">
        <div class="bg-white rounded shadow p-5 border-l-4 border-red-300">
            <p class="text-sm text-gray-500">Rejected payments</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($rejectedCount) }}</p>
            <p class="text-xs text-gray-400 mt-1">Member submitted invalid proof</p>
        </div>
        <div class="bg-white rounded shadow p-5 border-l-4 border-gray-300">
            <p class="text-sm text-gray-500">Voided (abandoned references)</p>
            <p class="text-2xl font-bold text-gray-500 mt-1">{{ number_format($voidedCount) }}</p>
            <p class="text-xs text-gray-400 mt-1">No proof uploaded within 48 hours</p>
        </div>
    </div>
    @endif

    {{-- ── Monthly revenue table ── --}}
    <div class="bg-white rounded shadow mb-8">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Monthly revenue (last 12 months)</h2>
        </div>
        @if ($monthlyRevenue->isEmpty())
            <div class="px-6 py-8 text-center text-gray-400 text-sm">No verified payments on record.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Transactions</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">M-Pesa (M)</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">EcoCash (M)</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Total (M)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($monthlyRevenue as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-800">
                                {{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('F Y') }}
                            </td>
                            <td class="px-6 py-3 text-right text-gray-600">{{ number_format($row->count) }}</td>
                            <td class="px-6 py-3 text-right text-gray-600">
                                M{{ number_format($mpesaMonthly[$row->month]->total ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-3 text-right text-gray-600">
                                M{{ number_format($ecocashMonthly[$row->month]->total ?? 0, 2) }}
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-gray-800">
                                M{{ number_format($row->total, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                        <tr>
                            <td class="px-6 py-3 font-bold text-gray-700" colspan="2">12-month total</td>
                            <td class="px-6 py-3 text-right font-bold text-gray-700">
                                M{{ number_format($mpesaMonthly->sum('total'), 2) }}
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-gray-700">
                                M{{ number_format($ecocashMonthly->sum('total'), 2) }}
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-gray-800">
                                M{{ number_format($monthlyRevenue->sum('total'), 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
