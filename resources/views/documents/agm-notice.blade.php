<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Notice of Annual General Meeting</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11pt; line-height: 1.4; color: #000; }
        .header { text-align: center; margin-bottom: 30px; }
        .org-name { font-weight: bold; font-size: 14pt; text-transform: uppercase; }
        .notice-title { font-weight: bold; font-size: 16pt; margin: 20px 0; text-decoration: underline; }
        
        .details-table { width: 100%; border: 1px solid #000; margin-bottom: 20px; }
        .details-table td { padding: 10px; border: 1px solid #000; vertical-align: top; }
        .label { font-weight: bold; background-color: #f0f0f0; width: 120px; }
        
        .section-title { font-weight: bold; margin-top: 20px; margin-bottom: 10px; text-transform: uppercase; font-size: 12pt; }
        
        ol { margin-left: 0; padding-left: 20px; }
        li { margin-bottom: 5px; }
        
        .footer { margin-top: 50px; font-size: 10pt; border-top: 1px solid #000; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo/cchpl-official-logo.png') }}" alt="CCHPL Logo" style="max-height: 60px; margin-bottom: 10px;">
        <div class="notice-title">NOTICE OF ANNUAL GENERAL MEETING</div>
        <div>Notice is hereby given that the {{ $data['agmYear'] ?? now()->year }} Annual General Meeting of the members will be held as follows:</div>
    </div>

    <table class="details-table" cellspacing="0">
        <tr>
            <td class="label">DATE:</td>
            <td>{{ $data['date'] ?? 'TBD' }}</td>
        </tr>
        <tr>
            <td class="label">TIME:</td>
            <td>{{ $data['time'] ?? 'TBD' }}</td>
        </tr>
        <tr>
            <td class="label">VENUE:</td>
            <td>
                {{ $data['venue'] ?? 'TBD' }}
                @if(isset($data['onlineLink']))
                    <br><em>Online Link: {{ $data['onlineLink'] }}</em>
                @endif
            </td>
        </tr>
        <tr>
            <td class="label">FORMAT:</td>
            <td>{{ ucfirst($data['format'] ?? 'In-Person') }}</td>
        </tr>
    </table>

    <div class="section-title">Agenda</div>
    <ol>
        @if(!empty($data['agendaItems']))
            @foreach($data['agendaItems'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        @else
            <li>Opening and Welcome</li>
            <li>Apologies and Quorum confirmation</li>
            <li>Minutes of the previous AGM</li>
            <li>Matters Arising</li>
            <li>Chairperson's Report</li>
            <li>Financial Report</li>
            <li>Election of Executive Committee (if applicable)</li>
            <li>General Business</li>
            <li>Closure</li>
        @endif
    </ol>

    <div class="section-title">Important Notes</div>
    <ul>
        <li><strong>Paid Up Members:</strong> Only members in good standing (paid up by {{ $data['paidUpDeadline'] ?? 'N/A' }}) are eligible to vote.</li>
        <li><strong>Proxies:</strong> Members unable to attend may appoint a proxy. Forms must be received by {{ $data['proxyDeadline'] ?? 'N/A' }}.</li>
        @if(isset($data['nominationDeadline']))
        <li><strong>Nominations:</strong> Nominations for the Executive Committee must be submitted by {{ $data['nominationDeadline'] }}.</li>
        @endif
    </ul>

    <div class="footer">
        <p><strong>By Order of the Executive Committee</strong></p>
        <p>
            Issued By: {{ $data['issuedBy'] ?? 'Secretary General' }}<br>
            Date: {{ $data['noticeDate'] ?? now()->format('d F Y') }}
        </p>
        <p>Enquiries: {{ $data['contactName'] ?? '' }} | {{ $data['contactEmail'] ?? '' }} | {{ $data['contactPhone'] ?? '' }}</p>
    </div>
</body>
</html>