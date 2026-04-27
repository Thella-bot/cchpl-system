
@extends('layouts.admin')
@section('title', 'Compose EC Meeting Minutes')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">

  <a href="{{ route('admin.documents.queue') }}" class="text-sm text-blue-600 hover:underline">&larr; Back to queue</a>

  <h1 class="text-3xl font-bold text-gray-800 mt-2 mb-2">Compose EC Meeting Minutes</h1>
  <p class="text-gray-500 text-sm mb-6">
    Fill in meeting details below. Use <strong>Preview</strong> to check before saving.
    After saving you can edit the body sections (reports, resolutions, action items) in the review screen.
    <br>Reference: CCHPL-OPS-002 &nbsp;|&nbsp; Per Constitution Clause 7.5 and Bylaws Clause 2.2.
  </p>

  @if ($errors->any())
    <div class="mb-5 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded text-sm">
      <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.documents.store.minutes') }}" id="minutes-form">
    @csrf
    <div class="bg-white rounded shadow p-6 space-y-5">

      <h2 class="text-base font-semibold text-gray-700 border-b pb-2">Meeting reference</h2>
      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Meeting number / Reference *</label>
          <input type="text" name="meeting_no" value="{{ old('meeting_no') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. 2025/03 or 2025-Q1">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Meeting type *</label>
          <select name="meeting_type" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            <option value="regular"   {{ old('meeting_type') === 'regular'   ? 'selected' : '' }}>Regular Quarterly Meeting</option>
            <option value="special"   {{ old('meeting_type') === 'special'   ? 'selected' : '' }}>Special Meeting</option>
            <option value="emergency" {{ old('meeting_type') === 'emergency' ? 'selected' : '' }}>Emergency Meeting</option>
          </select>
        </div>
      </div>

      <h2 class="text-base font-semibold text-gray-700 border-b pb-2 pt-2">Date & venue</h2>
      <div class="grid sm:grid-cols-3 gap-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
          <input type="text" name="date" value="{{ old('date') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. Monday, 14 July 2025">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Start time *</label>
          <input type="text" name="start_time" value="{{ old('start_time') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. 10:00 AM">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">End time *</label>
          <input type="text" name="end_time" value="{{ old('end_time') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. 12:30 PM">
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Venue / Platform *</label>
        <input type="text" name="venue" value="{{ old('venue') }}" required
          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
          placeholder="e.g. CCHPL Secretariat Office, Maseru / Zoom">
      </div>

      <h2 class="text-base font-semibold text-gray-700 border-b pb-2 pt-2">Officers</h2>
      <div class="grid sm:grid-cols-2 gap-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Chairperson *</label>
          <input type="text" name="chairperson" value="{{ old('chairperson') }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
            placeholder="e.g. Mahali Monokoa">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Minutes recorded by (Secretary) *</label>
          <input type="text" name="secretary" value="{{ old('secretary', auth()->user()->name) }}" required
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
      </div>

      <h2 class="text-base font-semibold text-gray-700 border-b pb-2 pt-2">Attendance & quorum</h2>
      <div class="grid sm:grid-cols-3 gap-5">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Total EC members *</label>
          <input type="number" name="total_ec_members" value="{{ old('total_ec_members') }}" required min="1"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Members present *</label>
          <input type="number" name="members_present" value="{{ old('members_present') }}" required min="0"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Quorum required (50%+1) *</label>
          <input type="number" name="quorum_required" value="{{ old('quorum_required') }}" required min="1"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
      </div>

      {{-- Attendance rows --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Attendance list (EC members)</label>
        <div id="attendees-list" class="space-y-2">
          @php $positions = ['President', 'Vice-President', 'Secretary', 'Treasurer', 'EC Member', 'EC Member', 'EC Member']; @endphp
          @foreach ($positions as $i => $pos)
          <div class="grid grid-cols-3 gap-2">
            <input type="text" name="attendees[{{ $i }}][name]" placeholder="Name"
              class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
            <input type="text" name="attendees[{{ $i }}][position]" value="{{ $pos }}"
              class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
            <select name="attendees[{{ $i }}][status]"
              class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
              <option value="present">Present</option>
              <option value="apology">Apology</option>
            </select>
          </div>
          @endforeach
        </div>
      </div>

      <h2 class="text-base font-semibold text-gray-700 border-b pb-2 pt-2">Confirmation</h2>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Date of confirmation meeting *</label>
        <input type="text" name="confirmation_date" value="{{ old('confirmation_date') }}" required
          class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
          placeholder="e.g. Monday, 13 October 2025">
        <p class="text-xs text-gray-400 mt-1">The date of the next EC meeting at which these minutes will be confirmed.</p>
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

{{-- Preview modal (same pattern as AGM) --}}
<div id="preview-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:50; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:8px; width:90vw; max-width:900px; height:90vh; display:flex; flex-direction:column; overflow:hidden;">
    <div style="padding:12px 16px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center;">
      <span style="font-weight:600; color:#374151;">PDF Preview — EC Meeting Minutes</span>
      <button onclick="closePreview()" style="background:#f3f4f6; border:none; border-radius:6px; padding:6px 12px; cursor:pointer;">Close</button>
    </div>
    <iframe id="preview-frame" style="flex:1; border:0;" title="PDF Preview"></iframe>
  </div>
</div>

<script>
function previewDraft() {
  const form = document.getElementById('minutes-form');
  const fd = new FormData(form);

  const attendees = [];
  let i = 0;
  while (fd.get('attendees[' + i + '][name]') !== null) {
    attendees.push({
      name:     fd.get('attendees[' + i + '][name]') || '',
      position: fd.get('attendees[' + i + '][position]') || '',
      status:   fd.get('attendees[' + i + '][status]') || 'present',
    });
    i++;
  }

  const pForm = document.createElement('form');
  pForm.method = 'POST';
  pForm.action = '{{ route("admin.documents.preview-draft") }}';
  pForm.target = 'preview-frame';

  const addHidden = (name, value) => {
    const inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = name; inp.value = value ?? '';
    pForm.appendChild(inp);
  };

  addHidden('_token', '{{ csrf_token() }}');
  addHidden('type', 'ec_minutes');
  addHidden('data[meetingNo]',        fd.get('meeting_no'));
  addHidden('data[meetingType]',      fd.get('meeting_type'));
  addHidden('data[date]',             fd.get('date'));
  addHidden('data[startTime]',        fd.get('start_time'));
  addHidden('data[endTime]',          fd.get('end_time'));
  addHidden('data[venue]',            fd.get('venue'));
  addHidden('data[secretary]',        fd.get('secretary'));
  addHidden('data[chairperson]',      fd.get('chairperson'));
  addHidden('data[totalEcMembers]',   fd.get('total_ec_members'));
  addHidden('data[membersPresent]',   fd.get('members_present'));
  addHidden('data[quorumRequired]',   fd.get('quorum_required'));
  addHidden('data[quorumAchieved]',   parseInt(fd.get('members_present') || 0) >= parseInt(fd.get('quorum_required') || 1) ? '1' : '0');
  addHidden('data[confirmationDate]', fd.get('confirmation_date'));

  attendees.forEach((a, idx) => {
    addHidden('data[attendees][' + idx + '][name]',     a.name);
    addHidden('data[attendees][' + idx + '][position]', a.position);
    addHidden('data[attendees][' + idx + '][status]',   a.status);
  });

  document.body.appendChild(pForm);
  pForm.submit();
  document.body.removeChild(pForm);

  document.getElementById('preview-modal').style.display = 'flex';
}

function closePreview() {
  document.getElementById('preview-modal').style.display = 'none';
  document.getElementById('preview-frame').src = '';
}
</script>
@endsection
