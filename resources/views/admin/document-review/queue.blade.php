
@extends('layouts.admin')
@section('title', 'Document Review Queue')

@section('content')
<div class="container mx-auto px-4 py-8">

  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-3xl font-bold text-gray-800">Document Review Queue</h1>
      <p class="text-gray-600 mt-1">Review, edit, preview, and approve documents before they are sent to recipients.</p>
    </div>
    <div class="flex gap-3">
      <a href="{{ route('admin.documents.compose.agm') }}"
         class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm font-medium">
        + AGM Notice
      </a>
      <a href="{{ route('admin.documents.compose.minutes') }}"
         class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm font-medium">
        + EC Minutes
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 text-green-700 rounded">
      {{ session('success') }}
    </div>
  @endif

  {{-- Filters --}}
  <form method="GET" class="mb-6 flex flex-wrap gap-3 items-end">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
      <select name="status" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        <option value="">All statuses</option>
        <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Pending review</option>
        <option value="approved"       {{ request('status') === 'approved'       ? 'selected' : '' }}>Approved</option>
        <option value="sent"           {{ request('status') === 'sent'           ? 'selected' : '' }}>Sent</option>
        <option value="cancelled"      {{ request('status') === 'cancelled'      ? 'selected' : '' }}>Cancelled</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Document type</label>
      <select name="type" class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        <option value="">All types</option>
        @foreach (['receipt','welcome_pack','certificate','agm_notice','ec_minutes'] as $t)
          <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>
            {{ \App\Models\DocumentReview::typeLabel($t) }}
          </option>
        @endforeach
      </select>
    </div>
    <button type="submit" class="px-4 py-2 bg-gray-100 border border-gray-300 rounded text-sm hover:bg-gray-200">Filter</button>
    <a href="{{ route('admin.documents.queue') }}" class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Clear</a>
  </form>

  {{-- Queue stats --}}
  @if ($pendingCount > 0)
    <div class="mb-5 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded flex items-center gap-3">
      <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-500 text-white text-sm font-bold">
        {{ $pendingCount }}
      </span>
      <span class="text-yellow-800 font-medium">document(s) are waiting for review before they can be sent.</span>
    </div>
  @endif

  {{-- Table --}}
  <div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Document</th>
          <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
          <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Queued</th>
          <th class="px-5 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Sent</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @forelse ($reviews as $review)
          <tr class="{{ $review->isPendingReview() ? 'bg-yellow-50' : '' }} hover:bg-gray-50">
            <td class="px-5 py-4">
              <div class="font-semibold text-gray-800">{{ $review->typeLabel() }}</div>
              <div class="text-xs text-gray-400">{{ \App\Models\DocumentReview::typeRef($review->type) }}</div>
            </td>
            <td class="px-5 py-4">
              <div class="text-gray-800">{{ $review->recipient_name ?? '—' }}</div>
              <div class="text-xs text-gray-400">{{ $review->recipient_email ?? $review->recipient_type }}</div>
            </td>
            <td class="px-5 py-4">
              <span class="inline-block px-2 py-1 text-xs rounded font-medium {{ $review->statusBadgeClass() }}">
                {{ ucwords(str_replace('_', ' ', $review->status)) }}
              </span>
            </td>
            <td class="px-5 py-4 text-gray-500 whitespace-nowrap">
              {{ $review->created_at->format('d M Y H:i') }}
            </td>
            <td class="px-5 py-4 text-gray-500 whitespace-nowrap">
              {{ $review->sent_at ? $review->sent_at->format('d M Y H:i') : '—' }}
            </td>
            <td class="px-5 py-4 text-right">
              <a href="{{ route('admin.documents.show', $review) }}"
                 class="inline-flex items-center px-3 py-1.5 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                @if ($review->isPendingReview()) Review @elseif ($review->isSent()) View @else Open @endif
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-5 py-10 text-center text-gray-400">
              No documents in the queue matching your filters.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $reviews->withQueryString()->links() }}</div>

</div>
@endsection
