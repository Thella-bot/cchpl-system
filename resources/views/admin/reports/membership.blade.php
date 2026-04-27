
@extends('layouts.admin')
@section('title', 'Membership Report')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Reports dashboard</a>
            <h1 class="text-3xl font-bold text-gray-800 mt-1">Membership Report</h1>
            <p class="text-gray-500 text-sm mt-1">Generated: {{ now()->format('d F Y, H:i') }}</p>
        </div>
        <a href="{{ route('admin.reports.export.members') }}"
           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm text-gray-700 font-medium">
            Export CSV
        </a>
    </div>

    {{-- ── By category ── --}}
    <div class="bg-white rounded shadow mb-8 overflow-x-auto">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Membership by category</h2>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Suspended</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Expired</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Revenue (M)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($categories as $cat)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $cat['name'] }}</td>
                    <td class="px-6 py-4 text-right text-gray-700">{{ number_format($cat['total']) }}</td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-semibold text-green-700">{{ number_format($cat['approved']) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right text-yellow-600">{{ number_format($cat['pending']) }}</td>
                    <td class="px-6 py-4 text-right text-red-600">{{ number_format($cat['suspended']) }}</td>
                    <td class="px-6 py-4 text-right text-orange-600">{{ number_format($cat['expired']) }}</td>
                    <td class="px-6 py-4 text-right font-semibold text-gray-800">
                        M{{ number_format($cat['revenue'], 2) }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">No data available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── Status summary ── --}}
    <div class="grid grid-cols-3 gap-5 mb-8">
        <div class="bg-white rounded shadow p-5 text-center">
            <p class="text-sm text-gray-500 mb-1">Expired memberships</p>
            <p class="text-3xl font-bold text-orange-700">{{ number_format($expiredCount) }}</p>
            <p class="text-xs text-gray-400 mt-1">Marked by scheduled command</p>
        </div>
        <div class="bg-white rounded shadow p-5 text-center">
            <p class="text-sm text-gray-500 mb-1">Suspended (non-payment 6+ mo.)</p>
            <p class="text-3xl font-bold text-red-700">{{ number_format($suspendedCount) }}</p>
        </div>
        <div class="bg-white rounded shadow p-5 text-center">
            <p class="text-sm text-gray-500 mb-1">Resigned</p>
            <p class="text-3xl font-bold text-gray-600">{{ number_format($resignedCount) }}</p>
        </div>
    </div>

    {{-- ── Expiring within 30 days ── --}}
    <div class="bg-white rounded shadow">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">
                Expiring within 30 days
                <span class="ml-2 text-sm font-normal text-yellow-600">({{ $expiringMembers->count() }} member(s))</span>
            </h2>
        </div>

        @if ($expiringMembers->isEmpty())
            <div class="px-6 py-8 text-center text-gray-400 text-sm">No memberships expiring in the next 30 days.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Member ID</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Days left</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($expiringMembers as $m)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-800">{{ $m->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $m->user->email }}</div>
                            </td>
                            <td class="px-6 py-3 font-mono text-xs text-gray-600">{{ $m->member_id ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $m->category->name }}</td>
                            <td class="px-6 py-3 text-gray-700">{{ $m->expiry_date->format('d M Y') }}</td>
                            <td class="px-6 py-3">
                                @php $days = $m->daysUntilExpiry(); @endphp
                                <span class="font-semibold {{ $days <= 7 ? 'text-red-600' : 'text-yellow-600' }}">
                                    {{ $days }} day(s)
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
