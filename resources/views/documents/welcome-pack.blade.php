<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Welcome Pack</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12pt; line-height: 1.5; color: #333; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #2C3E50; padding-bottom: 20px; }
        .logo { font-size: 18pt; font-weight: bold; color: #2C3E50; text-transform: uppercase; }
        .title { font-size: 16pt; font-weight: bold; color: #B08D55; margin-top: 10px; }
        
        .content { margin: 0 40px; }
        .greeting { font-weight: bold; margin-bottom: 20px; }
        
        .box { background: #f4f4f4; border-left: 5px solid #2C3E50; padding: 15px; margin: 20px 0; }
        .box h3 { margin-top: 0; color: #2C3E50; font-size: 14pt; }
        
        ul { margin-bottom: 20px; }
        li { margin-bottom: 5px; }
        
        .signature { margin-top: 50px; }
        .footer { position: fixed; bottom: 30px; left: 0; right: 0; text-align: center; font-size: 9pt; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Council for Culinary and<br>Hospitality Professionals Lesotho</div>
        <div class="title">New Member Welcome Pack</div>
    </div>

    <div class="content">
        <div class="greeting">Dear {{ $user->name }},</div>

        <p>Welcome to the Council for Culinary and Hospitality Professionals Lesotho (CCHPL). We are delighted to confirm your membership and look forward to your active participation in our community.</p>

        <div class="box">
            <h3>Membership Details</h3>
            <table cellpadding="5">
                <tr>
                    <td width="150"><strong>Category:</strong></td>
                    <td>{{ $category->name ?? 'Unspecified' }}</td>
                </tr>
                <tr>
                    <td><strong>Member ID:</strong></td>
                    <td>{{ $membership->member_id ?? 'PENDING' }}</td>
                </tr>
                <tr>
                    <td><strong>Status:</strong></td>
                    <td>Active (Good Standing)</td>
                </tr>
                <tr>
                    <td><strong>Valid Until:</strong></td>
                    <td>{{ $membership->expiry_date ? $membership->expiry_date->format('d F Y') : 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <h3>Getting Started</h3>
        <ul>
            <li><strong>Log in to your portal:</strong> Access your profile and resources at {{ config('app.url') }}</li>
            <li><strong>Use your designation:</strong> You may now use the CCHPL designation on your professional profile.</li>
            <li><strong>Attend Events:</strong> Keep an eye on your email for upcoming workshops and the AGM.</li>
        </ul>

        <h3>Code of Conduct Summary</h3>
        <p>As a member, you agree to uphold the highest standards of professionalism, integrity, and hygiene in the culinary and hospitality industry.</p>

        <div class="signature">
            <p>Sincerely,</p>
            <br>
            <p><strong>The Executive Committee</strong><br>
            Council for Culinary and Hospitality Professionals Lesotho</p>
        </div>
    </div>

    <div class="footer">
        CCHPL-MEM-001 &bull; {{ now()->year }} Edition
    </div>
</body>
</html>