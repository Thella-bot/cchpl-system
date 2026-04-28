<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>EC Meeting Minutes</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11pt; line-height: 1.4; color: #000; }
        .header { border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .org { font-weight: bold; font-size: 12pt; text-transform: uppercase; }
        .title { font-weight: bold; font-size: 14pt; margin-top: 5px; }
        
        table.meta { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        table.meta td { padding: 5px; border-bottom: 1px solid #eee; }
        .label { font-weight: bold; width: 140px; }
        
        .section { margin-bottom: 20px; }
        .section-head { background-color: #eee; padding: 5px; font-weight: bold; border-bottom: 1px solid #999; margin-bottom: 10px; }
        
        table.actions { width: 100%; border-collapse: collapse; font-size: 10pt; }
        table.actions th { border: 1px solid #000; padding: 5px; background: #f0f0f0; text-align: left; }
        table.actions td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        
        .footer { margin-top: 40px; font-size: 10pt; page-break-inside: avoid; }
        .sig-box { width: 45%; display: inline-block; vertical-align: top; margin-top: 30px; }
        .sig-line { border-top: 1px solid #000; margin-top: 40px; width: 80%; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo/cchpl-official-logo.png') }}" alt="CCHPL Logo" style="max-height: 50px; margin-bottom: 5px;">
        <div class="org">Council for Culinary and Hospitality Professionals Lesotho</div>
        <div class="title">MINUTES OF THE EXECUTIVE COMMITTEE MEETING</div>
    </div>

    <table class="meta">
        <tr>
            <td class="label">Meeting No:</td> <td>{{ $data['meetingNo'] }}</td>
            <td class="label">Type:</td> <td>{{ ucfirst($data['meetingType']) }}</td>
        </tr>
        <tr>
            <td class="label">Date:</td> <td>{{ $data['date'] }}</td>
            <td class="label">Time:</td> <td>{{ $data['startTime'] }} - {{ $data['endTime'] }}</td>
        </tr>
        <tr>
            <td class="label">Venue:</td> <td colspan="3">{{ $data['venue'] }}</td>
        </tr>
        <tr>
            <td class="label">Chairperson:</td> <td>{{ $data['chairperson'] }}</td>
            <td class="label">Secretary:</td> <td>{{ $data['secretary'] }}</td>
        </tr>
    </table>

    <div class="section">
        <div class="section-head">1. ATTENDANCE</div>
        <p><strong>Present:</strong> {{ $data['membersPresent'] ?? '0' }}/{{ $data['totalEcMembers'] ?? '—' }} (Quorum: {{ ($data['quorumAchieved'] ?? false) ? 'ACHIEVED' : 'NOT ACHIEVED' }})</p>
        <ul>
            @foreach($data['attendees'] ?? [] as $attendee)
                <li>{{ $attendee }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <div class="section-head">2. PROCEEDINGS</div>
        @if(!empty($data['agendaItems']))
            @foreach($data['agendaItems'] as $index => $item)
                <div style="margin-bottom: 10px;">
                    <strong>2.{{ $index + 1 }} {{ $item['title'] ?? 'Agenda Item' }}</strong>
                    <p>{{ $item['discussion'] ?? 'Discussion recorded here...' }}</p>
                    @if(isset($item['decision']))
                        <p><em>Decision: {{ $item['decision'] }}</em></p>
                    @endif
                </div>
            @endforeach
        @else
            <p><em>Minutes content not provided in structured format. See attached detailed notes.</em></p>
        @endif
    </div>

    <div class="section">
        <div class="section-head">3. ACTION ITEMS</div>
        <table class="actions">
            <thead>
                <tr>
                    <th width="15%">Action By</th>
                    <th>Task</th>
                    <th width="20%">Due Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['actionItems'] ?? [] as $action)
                <tr>
                    <td>{{ $action['owner'] ?? 'TBD' }}</td>
                    <td>{{ $action['task'] ?? '' }}</td>
                    <td>{{ $action['deadline'] ?? '' }}</td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align:center">No new action items</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p><strong>Approval of Minutes:</strong></p>
        <p>These minutes were confirmed as a true record of proceedings on: {{ $data['confirmationDate'] ?? '___________' }}</p>

        <div style="width: 100%;">
            <div class="sig-box">
                <div class="sig-line"></div>
                <strong>Chairperson</strong>
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                <strong>Secretary</strong>
            </div>
        </div>
    </div>
</body>
</html>