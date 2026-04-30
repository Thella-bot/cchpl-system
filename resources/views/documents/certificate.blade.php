<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Membership Certificate</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; margin: 0; padding: 0; }
        .border-pattern {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            border: 10px solid #B08D55; margin: 10px;
            padding: 5px;
        }
        .inner-border {
            border: 2px solid #2C3E50; height: 100%;
            padding: 40px; box-sizing: border-box; text-align: center;
        }
        .header { margin-bottom: 30px; }
        .logo { font-size: 30px; font-weight: bold; color: #2C3E50; letter-spacing: 2px; text-transform: uppercase; }
        .sub-logo { font-size: 12px; letter-spacing: 4px; color: #B08D55; margin-top: 5px; }
        
        .title {
            font-family: 'Times New Roman', serif;
            font-size: 42px; font-weight: bold; color: #B08D55; margin: 40px 0 20px 0;
            text-transform: uppercase;
        }
        .certifies { font-size: 16px; font-style: italic; color: #555; margin-bottom: 20px; }
        
        .member-name {
            font-family: 'Times New Roman', serif;
            font-size: 36px; font-weight: bold; border-bottom: 1px solid #ddd;
            display: inline-block; padding: 0 40px 10px 40px; margin-bottom: 20px;
            color: #2C3E50;
        }
        
        .details { font-size: 14px; color: #444; margin-top: 30px; line-height: 1.6; }
        .category { font-weight: bold; font-size: 18px; color: #2C3E50; text-transform: uppercase; }
        
        .footer { position: absolute; bottom: 60px; left: 60px; right: 60px; }
        .signature-line { width: 200px; border-top: 1px solid #333; margin: 0 auto; padding-top: 10px; font-size: 12px; }
        table.signatures { width: 100%; text-align: center; margin-top: 50px; }
        
        .cert-id { position: absolute; bottom: 20px; right: 20px; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="border-pattern">
        <div class="inner-border">
            <div class="header">
                <img src="{{ public_path('images/logo/cchpl-official-logo.png') }}" alt="CCHPL Logo" style="max-height: 90px; margin-bottom: 10px;">
            </div>

            <div class="title">Certificate of Membership</div>
            
            <div class="certifies">This is to certify that</div>
            
            <div class="member-name">{{ $user->name }}</div>
            
            <div class="certifies">is a registered member of the Council in good standing.</div>

            <div class="details">
                Membership Category:<br>
                <span class="category">{{ $category->name ?? 'Unspecified' }}</span>
            </div>

            <div class="details" style="margin-top: 20px;">
                Member ID: <strong>{{ $membership->member_id ?? 'PENDING' }}</strong><br>
                Valid Until: <strong>{{ $membership->expiry_date ? $membership->expiry_date->format('d F Y') : 'N/A' }}</strong>
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
                            <!-- Optional Seal Place -->
                            <div style="width: 80px; height: 80px; border: 2px dashed #ddd; border-radius: 50%; margin: 0 auto; line-height: 80px; color: #ddd; font-size: 10px;">
                                OFFICIAL SEAL
                            </div>
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
        </div>
    </div>
</body>
</html>
