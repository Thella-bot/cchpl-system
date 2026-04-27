
@extends('layouts.admin')
@section('title', 'Review: ' . $review->typeLabel())

@section('content')
<div class="container mx-auto px-4 py-8">

  {{-- Header --}}
  <div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
      <a href="{{ route('admin.documents.queue') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to queue</a>
      <h1 class="text-3xl font-bold text-gray-800 mt-1">{{ $review->typeLabel() }}</h1>
      <p class="text-gray-500 text-sm mt-1">
        Ref: {{ \App\Models\DocumentReview::typeRef($review->type) }} &nbsp;|&nbsp;
        Queued: {{ $review->created_at->format('d M Y H:i') }}
        @if($review->creator) by {{ $review->creator->name }} @endif
      </p>
    </div>
    <span class="self-start inline-block px-3 py-1.5 text-sm rounded font-medium {{ $review->statusBadgeClass() }}">
      {{ ucwords(str_replace('_', ' ', $review->status)) }}
    </span>
  </div>

  @if (session('success'))
    <div class="mb-5 p-4 bg-green-50 border-l-4 border-green-600 text-green-700 rounded">{{ session('success') }}</div>
  @endif
  @if (session('error'))
    <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-600 text-red-700 rounded">{{ session('error') }}</div>
  @endif

  <div class="grid lg:grid-cols-2 gap-8">

    {{-- LEFT: Edit fields --}}
    <div>
      <div class="bg-white rounded shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-gray-800">Document data</h2>
          @if (!$review->isSent() && !$review->isCancelled())
            <span class="text-xs text-gray-400">Edit any field, then re-preview before sending</span>
          @endif
        </div>

        @if ($review->isSent() || $review->isCancelled())
          {{-- Read-only view --}}
          <div class="space-y-3">
            @foreach ($review->data as $key => $value)
              @if (!is_array($value))
              <div class="flex gap-4 text-sm border-b pb-2">
                <span class="w-40 text-gray-500 font-medium shrink-0">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                <span class="text-gray-800">{{ $value }}</span>
              </div>
              @endif
            @endforeach
          </div>
        @else
          {{-- Editable form --}}
          <form method="POST" action="{{ route('admin.documents.update', $review) }}" id="edit-form">
            @csrf @method('PUT')
            <div class="space-y-4">
              @foreach ($review->data as $key => $value)
                @if (!is_array($value))
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ ucwords(str_replace(['_', 'Id', 'No'], [' ', ' ID', ' No.'], $key)) }}
                  </label>
                  @if (strlen((string)$value) > 80)
                    <textarea name="data[{{ $key }}]" rows="3"
                      class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">{{ $value }}</textarea>
                  @else
                    <input type="text" name="data[{{ $key }}]" value="{{ $value }}"
                      class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                  @endif
                </div>
                @endif
              @endforeach

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reviewer notes (internal only)</label>
                <textarea name="reviewer_notes" rows="2"
                  class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
                  placeholder="Optional notes visible only to admins">{{ $review->reviewer_notes }}</textarea>
              </div>
            </div>
            <div class="mt-5 flex gap-3">
              <button type="submit"
                class="px-5 py-2 bg-gray-700 text-white rounded hover:bg-gray-800 text-sm font-medium">
                Save changes
              </button>
              <button type="button" onclick="refreshPreview()"
                class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
                Save + refresh preview
              </button>
            </div>
          </form>
        @endif
      </div>

      {{-- Recipient info --}}
      <div class="bg-white rounded shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Recipient</h2>
        <div class="text-sm space-y-1">
          <div><span class="text-gray-500 w-32 inline-block">Name:</span> {{ $review->recipient_name ?? '—' }}</div>
          <div><span class="text-gray-500 w-32 inline-block">Email:</span> {{ $review->recipient_email ?? $review->recipient_type }}</div>
          <div><span class="text-gray-500 w-32 inline-block">Send to:</span>
            @if ($review->recipient_type === 'all_paid_up_members') All paid-up members
            @elseif ($review->recipient_type === 'ec_members') EC members
            @else {{ $review->recipient_name ?? $review->recipient_email }}
            @endif
          </div>
        </div>
      </div>

      {{-- Action buttons --}}
      @if (!$review->isSent() && !$review->isCancelled())
      <div class="bg-white rounded shadow p-6 space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Actions</h2>

        {{-- Approve --}}
        @if ($review->isPendingReview())
        <form method="POST" action="{{ route('admin.documents.approve', $review) }}">
          @csrf
          <p class="text-sm text-gray-600 mb-3">
            Mark this document as reviewed and correct. It will move to <strong>Approved</strong> status
            but will NOT be sent yet — you confirm sending separately.
          </p>
          <button type="submit"
            class="w-full px-5 py-2.5 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
            Approve document
          </button>
        </form>
        @endif

        {{-- Send --}}
        <form method="POST" action="{{ route('admin.documents.send', $review) }}"
              onsubmit="return confirm('Send this document to {{ addslashes($review->recipient_name ?? $review->recipient_type) }}? This cannot be undone.')">
          @csrf
          <p class="text-sm text-gray-600 mb-3">
            Generate the final PDF and email it to the recipient(s). Make sure you have previewed and approved it first.
          </p>
          <label class="flex items-start gap-2 mb-3 cursor-pointer">
            <input type="checkbox" name="confirm_send" value="1" required
              class="mt-0.5 h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
            <span class="text-sm text-gray-700">
              I have previewed this document and confirm the content is correct and ready to send.
            </span>
          </label>
          <button type="submit"
            class="w-full px-5 py-2.5 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
            Send document
          </button>
        </form>

        {{-- Cancel --}}
        <details class="mt-2">
          <summary class="text-sm text-red-600 cursor-pointer hover:underline">Cancel this document</summary>
          <form method="POST" action="{{ route('admin.documents.cancel', $review) }}" class="mt-3">
            @csrf
            <label class="block text-sm font-medium text-gray-700 mb-1">Reason for cancellation</label>
            <input type="text" name="cancellation_reason" required
              class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-3 focus:ring-2 focus:ring-red-500"
              placeholder="e.g. Duplicate document, wrong member, etc.">
            <button type="submit"
              class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
              Confirm cancellation
            </button>
          </form>
        </details>
      </div>
      @endif

      @if ($review->isSent())
      <div class="bg-green-50 border border-green-200 rounded p-5">
        <div class="font-semibold text-green-800 mb-1">Document sent</div>
        <div class="text-sm text-green-700">
          Sent {{ $review->sent_at->format('d M Y \a\t H:i') }}
          @if($review->sender) by {{ $review->sender->name }} @endif
        </div>
      </div>
      @endif

      @if ($review->isCancelled())
      <div class="bg-red-50 border border-red-200 rounded p-5">
        <div class="font-semibold text-red-800 mb-1">Document cancelled</div>
        <div class="text-sm text-red-700">{{ $review->cancellation_reason }}</div>
      </div>
      @endif

    </div>

    {{-- RIGHT: PDF Preview --}}
    <div>
      <div class="bg-white rounded shadow overflow-hidden sticky top-6">
        <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
          <h2 class="text-sm font-semibold text-gray-700">PDF Preview</h2>
          <div class="flex gap-2">
            <button onclick="refreshPreview()"
              class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
              Refresh
            </button>
            <a href="{{ route('admin.documents.preview', $review) }}" target="_blank"
              class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
              Open in new tab
            </a>
          </div>
        </div>

        <div id="preview-container" class="relative" style="height: 700px;">
          <div id="preview-loading"
            class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm bg-white z-10"
            style="display:none!important">
            Loading preview...
          </div>
          <iframe
            id="preview-frame"
            src="{{ route('admin.documents.preview', $review) }}"
            class="w-full h-full border-0"
            title="Document preview">
          </iframe>
        </div>

        <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 text-xs text-gray-400">
          This is a live preview. Save your changes then click Refresh to see the updated PDF.
        </div>
      </div>
    </div>

  </div>
</div>

<script>
function refreshPreview() {
  const form = document.getElementById('edit-form');
  if (form) {
    const submitBtn = form.querySelector('[type=submit]');
    if (submitBtn) submitBtn.click();
  }

  const frame = document.getElementById('preview-frame');
  if (frame) {
    const loading = document.getElementById('preview-loading');
    if (loading) loading.style.display = 'flex';
    frame.onload = () => { if (loading) loading.style.display = 'none'; };
    frame.src = frame.src.split('?')[0] + '?t=' + Date.now();
  }
}
</script>
@endsection
