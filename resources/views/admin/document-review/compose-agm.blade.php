@extends('layouts.admin')
@section('title', 'Compose AGM Notice & Agenda')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.documents.queue') }}" class="text-decoration-none text-muted small">&larr; Back to queue</a>
    <h1 class="h3 fw-bold mb-1">Compose AGM Notice & Agenda</h1>
    <p class="text-muted mb-0">
        Fill in all details below. Use <strong>Preview</strong> to check the PDF before saving.
        After saving, you can edit further in the review screen before sending.
        <br>Reference: CCHPL-OPS-001 &nbsp;|&nbsp; Per Constitution Clause 9.1 &amp; 9.3 and Bylaws Clause 3.
    </p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul class="mt-1 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="admin-shell-card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.documents.store.agm') }}" id="agm-form">
            @csrf

            <h5 class="fw-semibold mb-3 border-bottom pb-2">Meeting details</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="agm_year" class="form-label fw-semibold small text-muted">AGM Year *</label>
                    <input type="number" name="agm_year" id="agm_year" value="{{ old('agm_year', date('Y')) }}" required class="form-control" placeholder="{{ date('Y') }}">
                </div>
                <div class="col-md-6">
                    <label for="date" class="form-label fw-semibold small text-muted">Date of AGM *</label>
                    <input type="text" name="date" id="date" value="{{ old('date') }}" required class="form-control" placeholder="e.g. Saturday, 27 September 2025">
                </div>
                <div class="col-md-6">
                    <label for="time" class="form-label fw-semibold small text-muted">Time *</label>
                    <input type="text" name="time" id="time" value="{{ old('time') }}" required class="form-control" placeholder="e.g. 09:00 AM (Registration from 08:30 AM)">
                </div>
                <div class="col-md-6">
                    <label for="format" class="form-label fw-semibold small text-muted">Format *</label>
                    <select name="format" id="format" required class="form-select">
                        <option value="in-person"  {{ old('format') === 'in-person'  ? 'selected' : '' }}>In-person</option>
                        <option value="hybrid"     {{ old('format') === 'hybrid'     ? 'selected' : '' }}>Hybrid (in-person + online)</option>
                        <option value="online"     {{ old('format') === 'online'     ? 'selected' : '' }}>Online only</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-12">
                    <label for="venue" class="form-label fw-semibold small text-muted">Venue (full name and address) *</label>
                    <input type="text" name="venue" id="venue" value="{{ old('venue') }}" required class="form-control" placeholder="e.g. LNDC Conference Centre, Kingsway, Maseru 100, Lesotho">
                </div>
                <div class="col-12">
                    <label for="online_link" class="form-label fw-semibold small text-muted">Online link (if applicable)</label>
                    <input type="text" name="online_link" id="online_link" value="{{ old('online_link') }}" class="form-control" placeholder="Zoom / Teams / Google Meet link and access code">
                </div>
            </div>

            <h5 class="fw-semibold mb-3 mt-4 border-bottom pb-2">Notice details</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="notice_date" class="form-label fw-semibold small text-muted">Notice issued date *</label>
                    <input type="text" name="notice_date" id="notice_date" value="{{ old('notice_date', now()->format('d F Y')) }}" required class="form-control">
                    <p class="text-muted small mt-1">Must be at least 21 days before AGM date</p>
                </div>
                <div class="col-md-6">
                    <label for="issued_by" class="form-label fw-semibold small text-muted">Issued by (Secretary name) *</label>
                    <input type="text" name="issued_by" id="issued_by" value="{{ old('issued_by', auth()->user()->name) }}" required class="form-control">
                </div>
            </div>

            <h5 class="fw-semibold mb-3 mt-4 border-bottom pb-2">Contact & deadlines</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="contact_name" class="form-label fw-semibold small text-muted">Contact name *</label>
                    <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name', auth()->user()->name) }}" required class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="contact_email" class="form-label fw-semibold small text-muted">Contact email *</label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email', 'secretary@cchpl.org.ls') }}" required class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="contact_phone" class="form-label fw-semibold small text-muted">Contact phone *</label>
                    <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}" required class="form-control" placeholder="+266 ...">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label for="paid_up_deadline" class="form-label fw-semibold small text-muted">Paid-up deadline (voting eligibility) *</label>
                    <input type="text" name="paid_up_deadline" id="paid_up_deadline" value="{{ old('paid_up_deadline', '31 March ' . date('Y')) }}" required class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="proxy_deadline" class="form-label fw-semibold small text-muted">Proxy submission deadline *</label>
                    <input type="text" name="proxy_deadline" id="proxy_deadline" value="{{ old('proxy_deadline') }}" required class="form-control" placeholder="e.g. 48 hours before the AGM">
                </div>
                <div class="col-md-4">
                    <label for="nomination_deadline" class="form-label fw-semibold small text-muted">Nomination deadline *</label>
                    <input type="text" name="nomination_deadline" id="nomination_deadline" value="{{ old('nomination_deadline') }}" required class="form-control" placeholder="e.g. 7 days before AGM">
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
