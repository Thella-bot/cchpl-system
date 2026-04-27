<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'DejaVu Sans', Arial, sans-serif;
    font-size: 9pt;
    color: #111;
  }

  /* ── One receipt block ─────────────────────────── */
  .receipt {
    padding: 8mm 10mm 6mm;
    height: 128mm;
    border: 1.5px solid #222;
    position: relative;
    page-break-inside: avoid;
  }

  /* ── Header row: org name left / receipt no right ── */
  .header-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 3mm;
  }
  .header-table td {
    vertical-align: top;
    padding: 0;
  }
  .header-left {
    width: 70%;
    border: 1.5px solid #222;
    padding: 2.5mm 3mm;
  }
  .header-right {
    width: 30%;
    border: 1.5px solid #222;
    border-left: none;
    padding: 2.5mm 3mm;
    text-align: right;
  }
  .receipt-badge {
    font-size: 13pt;
    font-weight: bold;
    letter-spacing: 1px;
    display: block;
    margin-bottom: 1mm;
  }
  .org-name {
    font-size: 10pt;
    font-weight: bold;
    display: block;
    margin-bottom: 0.5mm;
  }
  .org-sub {
    font-size: 7.5pt;
    color: #444;
  }
  .rec-no-label {
    font-size: 7.5pt;
    color: #444;
    display: block;
    margin-bottom: 1.5mm;
  }
  .rec-no-value {
    font-size: 11pt;
    font-weight: bold;
    display: block;
    margin-bottom: 2mm;
  }
  .date-label { font-size: 7.5pt; color: #444; }
  .date-value { font-size: 9pt; font-weight: bold; }
  .copy-type {
    display: block;
    margin-top: 2mm;
    font-size: 7.5pt;
    font-style: italic;
    color: #555;
  }

  /* ── Body fields ── */
  .fields-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 8.5pt;
  }
  .fields-table td {
    border: 0.5px solid #aaa;
    padding: 1.5mm 2.5mm;
    vertical-align: top;
  }
  .fields-table td.label {
    width: 32%;
    font-weight: bold;
    background: #f7f7f7;
    white-space: nowrap;
  }
  .fields-table td.value {
    width: 68%;
  }
  .amount-row td { border-top: 1.5px solid #222; }
  .amount-label { font-size: 10pt; font-weight: bold; }
  .amount-value {
    font-size: 12pt;
    font-weight: bold;
    letter-spacing: 1px;
  }

  /* ── Footer: sig + stamp ── */
  .footer-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 0;
  }
  .footer-table td {
    border: 0.5px solid #aaa;
    padding: 2mm 3mm;
    vertical-align: top;
  }
  .footer-table td.sig-cell { width: 65%; }
  .footer-table td.stamp-cell {
    width: 35%;
    text-align: center;
    font-style: italic;
    color: #999;
    font-size: 7.5pt;
  }
  .sig-line {
    border-bottom: 1px solid #333;
    width: 55mm;
    margin-top: 6mm;
    margin-bottom: 1mm;
  }

  .small-note {
    font-size: 7pt;
    color: #666;
    font-style: italic;
    margin-top: 1.5mm;
  }

  /* ── Cut line ── */
  .cut-line {
    text-align: center;
    font-size: 7.5pt;
    color: #aaa;
    letter-spacing: 2px;
    margin: 3mm 0;
    border-top: 1px dashed #ccc;
    padding-top: 2mm;
  }
</style>
</head>
<body>

@php
  $copies = [
    ['type' => 'ORIGINAL', 'note' => 'Payer Copy'],
    ['type' => 'DUPLICATE', 'note' => 'CCHPL File Copy'],
  ];
@endphp

@foreach ($copies as $copy)
<div class="receipt">

  {{-- Header --}}
  <table class="header-table">
    <tr>
      <td class="header-left">
        <span class="receipt-badge">OFFICIAL RECEIPT</span>
        <span class="org-name">Council for Culinary and Hospitality Professionals Lesotho</span>
        <span class="org-sub">CCHPL &nbsp;|&nbsp; Maseru, Lesotho</span>
      </td>
      <td class="header-right">
        <span class="rec-no-label">Receipt No.</span>
        <span class="rec-no-value">{{ $receiptNo }}</span>
        <span class="date-label">Date:</span><br>
        <span class="date-value">{{ $date }}</span>
        <span class="copy-type">{{ $copy['type'] }} &mdash; {{ $copy['note'] }}</span>
      </td>
    </tr>
  </table>

  {{-- Fields --}}
  <table class="fields-table">
    <tr>
      <td class="label">Received From</td>
      <td class="value">{{ $receivedFrom }}</td>
    </tr>
    <tr>
      <td class="label">Member ID / Ref</td>
      <td class="value">{{ $memberId }}</td>
    </tr>
    <tr>
      <td class="label">Contact / Phone</td>
      <td class="value">{{ $contact }}</td>
    </tr>
    <tr>
      <td class="label">Payment For</td>
      <td class="value">{{ $paymentFor }}</td>
    </tr>
    <tr>
      <td class="label">Payment Period</td>
      <td class="value">{{ $paymentPeriod }}</td>
    </tr>
    <tr>
      <td class="label">Payment Method</td>
      <td class="value">{{ $paymentMethod }}</td>
    </tr>
    <tr>
      <td class="label">Reference / Transaction No.</td>
      <td class="value">{{ $transactionRef }}</td>
    </tr>
    <tr class="amount-row">
      <td class="label amount-label">AMOUNT RECEIVED</td>
      <td class="value amount-value">M &nbsp;{{ $amount }}</td>
    </tr>
    <tr>
      <td class="label">Amount in Words</td>
      <td class="value">{{ $amountWords }}</td>
    </tr>
    <tr>
      <td class="label">Balance Outstanding (if any)</td>
      <td class="value">M {{ $balance }}</td>
    </tr>
  </table>

  {{-- Footer: sig + stamp --}}
  <table class="footer-table">
    <tr>
      <td class="sig-cell">
        <strong>Received by (Treasurer / Authorised Officer):</strong><br>
        <div class="sig-line"></div>
        <em>Signature &amp; Name</em>
      </td>
      <td class="stamp-cell">
        <strong>Official Stamp</strong><br><br>
        <em>(CCHPL stamp to be<br>affixed here)</em>
      </td>
    </tr>
  </table>

  <div class="small-note">
    This is an official receipt of CCHPL. Please retain for your records. &nbsp; Queries: secretary@cchpl.org.ls
  </div>

</div>

@if ($loop->first)
<div class="cut-line">&#9986; &nbsp; cut here &nbsp; &#9986;</div>
@endif

@endforeach

<div style="text-align:center; font-size:7pt; color:#aaa; margin-top:2mm;">
  CCHPL &nbsp;|&nbsp; Official Receipt Template CCHPL-FIN-003 &nbsp;|&nbsp; Maseru, Kingdom of Lesotho
</div>

</body>
</html>
