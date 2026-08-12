@extends('layouts.admin')
@section('title', 'Compose EC Meeting Minutes')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.documents.queue') }}" class="text-decoration-none text-muted small">&larr; Back to queue</a>
    <h1 class="h3 fw-bold mb-1">Compose EC Meeting Minutes</h1>
    <p class="text-muted mb-0">
        Fill in meeting details below. Use <strong>Preview</strong> to check before saving.
        After saving you can edit the body sections (reports, resolutions, action items) in the review screen.
        <br>Reference: CCHPL-OPS-002 &nbsp;|&nbsp; Per Constitution Clause 7.5 and Bylaws Clause 2.2.
    </p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="admin-shell-card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.documents.store.minutes') }}" id="minutes-form">
            @csrf

            <h5 class="fw-semibold mb-3 border-bottom pb-2">Meeting reference</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="meeting_no" class="form-label fw-semibold small text-muted">Meeting number / Reference *</label>
                    <input type="text" name="meeting_no" id="meeting_no" value="{{ old('meeting_no') }}" required class="form-control" placeholder="e.g. 2025/03 or 2025-Q1">
                </div>
                <div class="col-md-6">
                    <label for="meeting_type" class="form-label fw-semibold small text-muted">Meeting type *</label>
                    <select name="meeting_type" id="meeting_type" required class="form-select">
                        <option value="regular"   {{ old('meeting_type') === 'regular'   ? 'selected' : '' }}>Regular Quarterly Meeting</option>
                        <option value="special"   {{ old('meeting_type') === 'special'   ? 'selected' : '' }}>Special Meeting</option>
                        <option value="emergency" {{ old('meeting_type') === 'emergency' ? 'selected' : '' }}>Emergency Meeting</option>
                    </select>
                </div>
            </div>

            <h5 class="fw-semibold mb-3 mt-4 border-bottom pb-2">Date & venue</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="date" class="form-label fw-semibold small text-muted">Date *</label>
                    <input type="text" name="date" id="date" value="{{ old('date') }}" required class="form-control" placeholder="e.g. Monday, 14 July 2025">
                </div>
                <div class="col-md-4">
                    <label for="start_time" class="form-label fw-semibold small text-muted">Start time *</label>
                    <input type="text" name="start_time" id="start_time" value="{{ old('start_time') }}" required class="form-control" placeholder="e.g. 10:00 AM">
                </div>
                <div class="col-md-4">
                    <label for="end_time" class="form-label fw-semibold small text-muted">End time *</label>
                    <input type="text" name="end_time" id="end_time" value="{{ old('end_time') }}" required class="form-control" placeholder="e.g. 12:30 PM">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-12">
                    <label for="venue" class="form-label fw-semibold small text-muted">Venue / Platform *</label>
                    <input type="text" name="venue" id="venue" value="{{ old('venue') }}" required class="form-control" placeholder="e.g. CCHPL Secretariat Office, Maseru / Zoom">
                </div>
            </div>

            <h5 class="fw-semibold mb-3 mt-4 border-bottom pb-2">Officers</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="chairperson" class="form-label fw-semibold small text-muted">Chairperson *</label>
                    <input type="text" name="chairperson" id="chairperson" value="{{ old('chairperson') }}" required class="form-control" placeholder="e.g. Mahali Monokoa">
                </div>
                <div class="col-md-6">
                    <label for="secretary" class="form-label fw-semibold small text-muted">Minutes recorded by (Secretary) *</label>
                    <input type="text" name="secretary" id="secretary" value="{{ old('secretary', auth()->user()->name) }}" required class="form-control">
                </div>
            </div>

            <h5 class="fw-semibold mb-3 mt-4 border-bottom pb-2">Attendance & quorum</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="total_ec_members" class="form-label fw-semibold small text-muted">Total EC members *</label>
                    <input type="number" name="total_ec_members" id="total_ec_members" value="{{ old('total_ec_members') }}" required min="1" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="members_present" class="form-label fw-semibold small text-muted">Members present *</label>
                    <input type="number" name="members_present" id="members_present" value="{{ old('members_present') }}" required min="0" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="quorum_required" class="form-label fw-semibold small text-muted">Quorum required (50%+1) *</label>
                    <input type="number" name="quorum_required" id="quorum_required" value="{{ old('quorum_required') }}" required min="1" class="form-control">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label fw-semibold small text-muted">Attendance list (EC members)</label>
                <div id="attendees-list" class="row g-2">
                    @php $positions = ['President', 'Vice-President', 'Secretary', 'Treasurer', 'EC Member', 'EC Member', 'EC Member']; @endphp
                    @foreach ($positions as $i => $pos)
                        <div class="col-12">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="attendees[{{ $i }}][name]" placeholder="Name" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="attendees[{{ $i }}][position]" value="{{ $pos }}" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <select name="attendees[{{ $i }}][status]" class="form-select">
                                        <option value="present">Present</option>
                                        <option value="apology">Apology</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <h5 class="fw-semibold mb-3 mt-4 border-bottom pb-2">Confirmation</h5>
            <div class="row g-3">
                <div class="col-12">
                    <label for="confirmation_date" class="form-label fw-semibold small text-muted">Date of confirmation meeting *</label>
                    <input type="text" name="confirmation_date" id="confirmation_date" value="{{ old('confirmation_date') }}" required class="form-control" placeholder="e.g. Monday, 13 October 2025">
                    <p class="text-muted small mt-1">The date of the next EC meeting at which these minutes will be confirmed.</p>
                </div>
            </div>

            <div class="mt-4 d-flex flex-wrap gap-2">
                <button type="button" onclick="previewDraft()" class="btn btn-primary">Preview PDF</button>
                <button type="submit" class="btn btn-success">Save to review queue</button>
                <a href="{{ route('admin.documents.queue') }}" class="text-decoration-none text-muted small">Cancel</a>
            </div>
        </form>
    </div>
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
