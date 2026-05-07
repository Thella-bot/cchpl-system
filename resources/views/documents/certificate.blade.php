<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Membership Certificate</title>
    <style>
        @page { margin: 0; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1f2933;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        .outer-border,
        .inner-border {
            position: fixed;
            box-sizing: border-box;
        }

        .outer-border {
            top: 12mm;
            left: 12mm;
            right: 12mm;
            bottom: 12mm;
            border: 5px solid #2446f2;
        }

        .inner-border {
            top: 18mm;
            left: 18mm;
            right: 18mm;
            bottom: 18mm;
            border: 2px solid #06b85f;
        }

        .certificate {
            position: relative;
            margin: 24mm 28mm;
            text-align: center;
        }

        .header { margin-bottom: 18px; }

        .logo-image {
            max-height: 92px;
            margin-bottom: 8px;
        }

        .council-line {
            font-size: 11px;
            letter-spacing: 2.4px;
            text-transform: uppercase;
            color: #06b85f;
            font-weight: bold;
        }

        .title {
            font-family: 'Times New Roman', serif;
            font-size: 40px;
            font-weight: bold;
            color: #2446f2;
            margin: 28px 0 12px 0;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .accent-rule {
            width: 210px;
            height: 3px;
            background: #06b85f;
            margin: 0 auto 24px;
        }

        .certifies {
            font-size: 15px;
            font-style: italic;
            color: #4b5563;
            margin-bottom: 16px;
        }

        .member-name {
            font-family: 'Times New Roman', serif;
            font-size: 36px;
            font-weight: bold;
            border-bottom: 2px solid #f5b22f;
            display: inline-block;
            padding: 0 42px 9px;
            margin-bottom: 18px;
            color: #111827;
        }

        .details {
            font-size: 14px;
            color: #374151;
            margin-top: 24px;
            line-height: 1.6;
        }

        .category {
            font-weight: bold;
            font-size: 18px;
            color: #06b85f;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .footer {
            position: fixed;
            bottom: 30mm;
            left: 34mm;
            right: 34mm;
        }

        table.signatures {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #2446f2;
            margin: 0 auto;
            padding-top: 10px;
            font-size: 12px;
            color: #374151;
        }

        .seal {
            width: 80px;
            height: 80px;
            border: 2px dashed #06b85f;
            border-radius: 50%;
            margin: 0 auto;
            line-height: 80px;
            color: #06b85f;
            font-size: 10px;
        }

        .cert-id {
            position: fixed;
            bottom: 21mm;
            right: 24mm;
            font-size: 10px;
            color: #6b7280;
            background: #ffffff;
            padding: 0 6px;
        }
    </style>
</head>
<body>
    <div class="outer-border"></div>
    <div class="inner-border"></div>

    <div class="certificate">
        <div class="header">
            <img src="{{ public_path('images/logo/cchpl-official-logo.png') }}" alt="CCHPL Logo" class="logo-image">
            <div class="council-line">Maseru, Kingdom of Lesotho</div>
        </div>

        <div class="title">Certificate of Membership</div>
        <div class="accent-rule"></div>

        <div class="certifies">This is to certify that</div>

        <div class="member-name">{{ $user->name }}</div>

        <div class="certifies">is a registered member of the Council in good standing.</div>

        <div class="details">
            Membership Category:<br>
            <span class="category">{{ $category->name ?? 'Unspecified' }}</span>
        </div>

        <div class="details" style="margin-top: 18px;">
            Member ID: <strong>{{ $membership->member_id ?? 'PENDING' }}</strong><br>
            Valid Until: <strong>{{ $membership->expiry_date ? $membership->expiry_date->format('d F Y') : 'N/A' }}</strong>
        </div>
    </div>

    <div class="footer">
        <table class="signatures">
            <tr>
                <td>
                    <div class="signature-line">
                        Chairperson<br>CCHPL Executive Committee
                    </div>
                </td>
                <td>
                    <div class="seal">OFFICIAL SEAL</div>
                </td>
                <td>
                    <div class="signature-line">
                        Secretary General<br>CCHPL Executive Committee
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="cert-id">Ref: CCHPL-MEM-002 | Issued: {{ now()->format('Y-m-d') }}</div>
</body>
</html>
