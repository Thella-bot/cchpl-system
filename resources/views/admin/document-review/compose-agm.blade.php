
@extends('layouts.admin')
@section('title', 'Compose AGM Notice & Agenda')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">

  <a href="{{ route('admin.documents.queue') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to queue</a>

  <h1 class="text-3xl font-bold text-gray-800 mt-2 mb-2">Compose AGM Notice & Agenda</h1>
  <p class="text-gray-500 text-sm mb-6">
    Fill in all details below. Use <strong>Preview</strong> to check the PDF before saving.
    After saving, you can edit further in the review screen before sending.
    <br>Reference: CCHPL-OPS-001 &nbsp;|&nbsp; Per Constitution Clause 9.1 &amp; 9.3 and Bylaws Clause 3.
  </p>

  @if ($errors->any())
    <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded text-sm">
      <strong>Please fix the following errors:</strong>
      <ul class="mt-1 list-disc list-inside">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.documents.store.agm') }}" id="agm-form">
    @csrf
    <div class="bg-white rounded shadow p-6 space-y-5">

      <h2 class="text-base font-semibold text-gray-700 border-b pb-2">Meeting details</h2>

      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">AGM Year *</label>
          <input type="number" name="agm_year" value="{{ old('agm_year', date('Y')) }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
            placeholder="{{ date('Y') }}">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date of AGM *</label>
          <input type="text" name="date" value="{{ old('date') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. Saturday, 27 September 2025">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Time *</label>
          <input type="text" name="time" value="{{ old('time') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. 09:00 AM (Registration from 08:30 AM)">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Format *</label>
          <select name="format" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            <option value="in-person"  {{ old('format') === 'in-person'  ? 'selected' : '' }}>In-person</option>
            <option value="hybrid"     {{ old('format') === 'hybrid'     ? 'selected' : '' }}>Hybrid (in-person + online)</option>
            <option value="online"     {{ old('format') === 'online'     ? 'selected' : '' }}>Online only</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Venue (full name and address) *</label>
        <input type="text" name="venue" value="{{ old('venue') }}" required
          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
          placeholder="e.g. LNDC Conference Centre, Kingsway, Maseru 100, Lesotho">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Online link (if applicable)</label>
        <input type="text" name="online_link" value="{{ old('online_link') }}"
          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
          placeholder="Zoom / Teams / Google Meet link and access code">
      </div>

      <h2 class="text-base font-semibold text-gray-700 border-b pb-2 pt-2">Notice details</h2>

      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Notice issued date *</label>
          <input type="text" name="notice_date" value="{{ old('notice_date', now()->format('d F Y')) }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
          <p class="text-xs text-gray-400 mt-1">Must be at least 21 days before AGM date</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Issued by (Secretary name) *</label>
          <input type="text" name="issued_by" value="{{ old('issued_by', auth()->user()->name) }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
      </div>

      <h2 class="text-base font-semibold text-gray-700 border-b pb-2 pt-2">Contact & deadlines</h2>

      <div class="grid sm:grid-cols-3 gap-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Contact name *</label>
          <input type="text" name="contact_name" value="{{ old('contact_name', auth()->user()->name) }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Contact email *</label>
          <input type="email" name="contact_email" value="{{ old('contact_email', 'secretary@cchpl.org.ls') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Contact phone *</label>
          <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
            placeholder="+266 ...">
        </div>
      </div>

      <div class="grid sm:grid-cols-3 gap-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Paid-up deadline (voting eligibility) *</label>
          <input type="text" name="paid_up_deadline" value="{{ old('paid_up_deadline', '31 March ' . date('Y')) }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Proxy submission deadline *</label>
          <input type="text" name="proxy_deadline" value="{{ old('proxy_deadline') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. 48 hours before the AGM">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nomination deadline *</label>
          <input type="text" name="nomination_deadline" value="{{ old('nomination_deadline') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. 7 days before AGM">
        </div>
      </div>

    </div>

    <div class="mt-6 flex flex-wrap gap-3 items-center">
      <button type="button" onclick="previewDraft()"
        class="px-6 py-2.5 bg-blue-600 text-white rounded hover:bg-blue-700 font-medium">
        Preview PDF
      </button>
      <button type="submit"
        class="px-6 py-2.5 bg-green-600 text-white rounded hover:bg-green-700 font-medium">
        Save to review queue
      </button>
      <a href="{{ route('admin.documents.queue') }}" class="text-sm text-gray-500 hover:underline">Cancel</a>
    </div>
  </form>

</div>

{{-- Preview modal --}}
<div id="preview-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:50; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:8px; width:90vw; max-width:900px; height:90vh; display:flex; flex-direction:column; overflow:hidden;">
    <div style="padding:12px 16px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
      <span style="font-weight:600; color:#374151;">PDF Preview — AGM Notice {{ date('Y') }}</span>
      <button onclick="closePreview()" style="background:#f3f4f6; border:none; border-radius:6px; padding:6px 12px; cursor:pointer;">Close</button>
    </div>
    <iframe id="preview-frame" style="flex:1; border:0;" title="PDF Preview"></iframe>
  </div>
</div>

<script>
function getFormData() {
  const form = document.getElementById('agm-form');
  const fd = new FormData(form);
  const data = {};
  fd.forEach((v, k) => { data[k] = v; });
  return data;
}

function previewDraft() {
  const raw = getFormData();
  const payload = {
    type: 'agm_notice',
    data: {
      date:               raw.date || '',
      time:               raw.time || '',
      venue:              raw.venue || '',
      format:             raw.format || 'in-person',
      onlineLink:         raw.online_link || null,
      contactName:        raw.contact_name || '',
      contactEmail:       raw.contact_email || '',
      contactPhone:       raw.contact_phone || '',
      noticeDate:         raw.notice_date || '',
      issuedBy:           raw.issued_by || '',
      paidUpDeadline:     raw.paid_up_deadline || '',
      proxyDeadline:      raw.proxy_deadline || '',
      nominationDeadline: raw.nomination_deadline || '',
      agmYear:            parseInt(raw.agm_year) || new Date().getFullYear(),
    }
  };

  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("admin.documents.preview-draft") }}';
  form.target = 'preview-frame';

  const csrf = document.createElement('input');
  csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
  form.appendChild(csrf);

  const typeInput = document.createElement('input');
  typeInput.type = 'hidden'; typeInput.name = 'type'; typeInput.value = payload.type;
  form.appendChild(typeInput);

  Object.entries(payload.data).forEach(([k, v]) => {
    const i = document.createElement('input');
    i.type = 'hidden'; i.name = 'data[' + k + ']'; i.value = v ?? '';
    form.appendChild(i);
  });

  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);

  document.getElementById('preview-modal').style.display = 'flex';
}

function closePreview() {
  document.getElementById('preview-modal').style.display = 'none';
  document.getElementById('preview-frame').src = '';
}
</script>
@endsection
