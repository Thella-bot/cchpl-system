<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'DejaVu Serif', Georgia, serif;
    background: #fff;
    color: #1a1a1a;
  }

  /* Outer border frame */
  .outer-frame {
    position: absolute;
    top: 10mm; left: 10mm; right: 10mm; bottom: 10mm;
    border: 3px solid #8B6914;
  }
  .inner-frame {
    position: absolute;
    top: 14mm; left: 14mm; right: 14mm; bottom: 14mm;
    border: 1px solid #8B6914;
  }

  .certificate {
    padding: 20mm 22mm 16mm;
    text-align: center;
    min-height: 190mm;
    position: relative;
  }

  /* Corner ornaments */
  .corner {
    position: absolute;
    width: 10mm; height: 10mm;
    font-size: 22pt;
    color: #8B6914;
    line-height: 1;
  }
  .corner.tl { top: 16mm; left: 16mm; }
  .corner.tr { top: 16mm; right: 16mm; text-align: right; }
  .corner.bl { bottom: 16mm; left: 16mm; }
  .corner.br { bottom: 16mm; right: 16mm; text-align: right; }

  .org-header {
    font-size: 9pt;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #5a4010;
    margin-bottom: 2mm;
  }

  .divider-gold {
    width: 80mm;
    height: 1.5px;
    background: #8B6914;
    margin: 3mm auto;
  }
  .divider-thin {
    width: 50mm;
    height: 0.5px;
    background: #8B6914;
    margin: 2mm auto;
  }

  .cert-title {
    font-size: 28pt;
    font-weight: bold;
    letter-spacing: 4px;
    text-transform: uppercase;
    color: #3a2a00;
    margin: 4mm 0 2mm;
  }

  .cert-subtitle {
    font-size: 10pt;
    letter-spacing: 1.5px;
    color: #5a4010;
    font-style: italic;
    margin-bottom: 6mm;
  }

  .certifies-text {
    font-size: 11pt;
    color: #444;
    font-style: italic;
    margin-bottom: 3mm;
  }

  .member-name {
    font-size: 30pt;
    font-weight: bold;
    color: #1a1a00;
    letter-spacing: 1px;
    font-family: 'DejaVu Serif', Georgia, serif;
    border-bottom: 1.5px solid #8B6914;
    display: inline-block;
    padding: 0 10mm 2mm;
    margin: 2mm 0 4mm;
  }

  .recognised-text {
    font-size: 10pt;
    color: #444;
    font-style: italic;
    margin-bottom: 2mm;
  }

  .category-text {
    font-size: 15pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #3a2a00;
    margin-bottom: 2mm;
  }

  .org-name-text {
    font-size: 10pt;
    color: #444;
    font-style: italic;
    margin-bottom: 2mm;
  }

  .pledge-text {
    font-size: 9pt;
    color: #555;
    font-style: italic;
    margin-bottom: 6mm;
  }

  /* Details table */
  .details-table {
    margin: 0 auto 6mm;
    border-collapse: collapse;
    font-size: 9pt;
  }
  .details-table td {
    padding: 1.5mm 4mm;
    text-align: left;
    border-bottom: 0.5px solid #ddd;
  }
  .details-table td.label {
    color: #666;
    font-style: italic;
    white-space: nowrap;
    padding-right: 6mm;
  }
  .details-table td.value {
    font-weight: bold;
    color: #1a1a1a;
  }

  /* Signature row */
  .sig-row {
    display: table;
    width: 100%;
    margin-top: 6mm;
  }
  .sig-cell {
    display: table-cell;
    width: 50%;
    text-align: center;
    vertical-align: bottom;
    padding: 0 8mm;
  }
  .sig-line {
    border-top: 1px solid #333;
    margin: 0 auto 2mm;
    width: 55mm;
  }
  .sig-name {
    font-size: 8pt;
    font-weight: bold;
    color: #222;
  }
  .sig-title {
    font-size: 7pt;
    color: #666;
    font-style: italic;
  }

  .footer-ref {
    position: absolute;
    bottom: 17mm;
    left: 0; right: 0;
    text-align: center;
    font-size: 7pt;
    color: #999;
    letter-spacing: 1px;
  }

  .stamp-box {
    position: absolute;
    bottom: 18mm;
    right: 22mm;
    width: 28mm; height: 28mm;
    border: 1px dashed #bbb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .stamp-text {
    font-size: 7pt;
    color: #bbb;
    text-align: center;
    font-style: italic;
  }
</style>
</head>
<body>

<div class="outer-frame"></div>
<div class="inner-frame"></div>

<div class="corner tl">&#10022;</div>
<div class="corner tr">&#10022;</div>
<div class="corner bl">&#10022;</div>
<div class="corner br">&#10022;</div>

<div class="certificate">

  <div class="org-header">Council for Culinary and Hospitality Professionals Lesotho</div>
  <div class="org-header" style="font-size:8pt; letter-spacing:3px; color:#8B6914;">CCHPL &nbsp;|&nbsp; Maseru, Kingdom of Lesotho</div>

  <div class="divider-gold"></div>

  <div class="cert-title">Certificate of Membership</div>

  <div class="divider-thin"></div>
  <div class="cert-subtitle">Uniting &nbsp;|&nbsp; Elevating &nbsp;|&nbsp; Preserving — Lesotho's Culinary and Hospitality Profession</div>

  <div class="certifies-text">This certifies that</div>

  <div class="member-name">{{ $memberName }}</div>

  <div class="recognised-text" style="margin-top:4mm;">is hereby recognised as a</div>
  <div class="category-text">{{ $category }}</div>
  <div class="org-name-text">of the Council for Culinary and Hospitality Professionals Lesotho</div>

  <div class="pledge-text">having met the criteria for membership and pledged to uphold the CCHPL Code of Ethics.</div>

  <div class="divider-thin"></div>

  <table class="details-table">
    <tr>
      <td class="label">Member ID</td>
      <td class="value">{{ $memberId }}</td>
      <td style="width:12mm;"></td>
      <td class="label">Valid From</td>
      <td class="value">{{ $validFrom }}</td>
    </tr>
    <tr>
      <td class="label">Date Issued</td>
      <td class="value">{{ $dateIssued }}</td>
      <td></td>
      <td class="label">Valid Until</td>
      <td class="value">{{ $validUntil }}</td>
    </tr>
  </table>

  <div class="sig-row">
    <div class="sig-cell">
      <div class="sig-line"></div>
      <div class="sig-name">Mahali Monokoa</div>
      <div class="sig-title">President, CCHPL</div>
    </div>
    <div class="sig-cell">
      <div class="sig-line"></div>
      <div class="sig-name">Secretary</div>
      <div class="sig-title">CCHPL</div>
    </div>
  </div>

  <div class="footer-ref">CCHPL &nbsp;|&nbsp; Membership Certificate CCHPL-MEM-002 &nbsp;|&nbsp; Maseru, Kingdom of Lesotho</div>

</div>

</body>
</html>
