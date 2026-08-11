<?php

namespace App\Services\Documents;

use App\Models\DocumentReview;
use App\Services\DocumentService;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Service class for handling EC Minutes document creation and building.
 *
 * Centralizes EC Minutes logic for maintainability and reusability.
 */
class EcMinutesDocument
{
    /**
     * Store a new EC Minutes document review from request data.
     *
     * @param  Request  $request  The HTTP request containing EC minutes data.
     * @return DocumentReview The created document review instance.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'meeting_no' => 'required|string|max:50',
            'meeting_type' => 'required|in:regular,special,emergency',
            'date' => 'required|string|max:50|regex:/^[A-Za-z]+,?\s+\d{1,2}\s+[A-Za-z]+\s+\d{4}$/',
            'start_time' => 'required|string|max:20|regex:/^\d{1,2}:\d{2}\s?(AM|PM)?$/',
            'end_time' => 'required|string|max:20|regex:/^\d{1,2}:\d{2}\s?(AM|PM)?$/',
            'venue' => 'required|string|max:255',
            'secretary' => 'required|string|max:255',
            'chairperson' => 'required|string|max:255',
            'total_ec_members' => 'required|integer|min:3|max:50',
            'members_present' => 'required|integer|min:0',
            'quorum_required' => 'required|integer|min:1',
            'confirmation_date' => 'required|string|max:50',
            'attendees' => 'nullable|array|max:50',
            'attendees.*.name' => 'nullable|string|max:255',
            'attendees.*.position' => 'nullable|string|max:100',
            'agenda_items' => 'nullable|array|max:20',
            'agenda_items.*.title' => 'nullable|string|max:500',
            'agenda_items.*.notes' => 'nullable|string|max:2000',
            'action_items' => 'nullable|array|max:20',
            'action_items.*.description' => 'nullable|string|max:500',
            'action_items.*.owner' => 'nullable|string|max:255',
            'action_items.*.deadline' => 'nullable|date',
        ]);

        // VALIDATION: Verify logical constraints on attendance numbers
        if ($data['members_present'] > $data['total_ec_members']) {
            throw ValidationException::withMessages([
                'members_present' => 'Members present cannot exceed total EC members.',
            ]);
        }

        $quorumThreshold = (int) ceil($data['total_ec_members'] / 2) + 1;
        if ($data['quorum_required'] > $quorumThreshold) {
            throw ValidationException::withMessages([
                'quorum_required' => "Quorum requirement cannot exceed {$quorumThreshold} (50% + 1).",
            ]);
        }

        // Create a new document review for the EC minutes
        return DocumentReview::create([
            'type' => DocumentReview::TYPE_EC_MINUTES,
            'status' => DocumentReview::STATUS_PENDING_REVIEW,
            'recipient_type' => DocumentReview::RECIPIENT_EC_MEMBERS,
            'recipient_name' => 'EC Members',
            'data' => [
                'meetingNo' => $data['meeting_no'],
                'meetingType' => $data['meeting_type'],
                'date' => $data['date'],
                'startTime' => $data['start_time'],
                'endTime' => $data['end_time'],
                'venue' => $data['venue'],
                'secretary' => $data['secretary'],
                'chairperson' => $data['chairperson'],
                'totalEcMembers' => (int) $data['total_ec_members'],
                'membersPresent' => (int) $data['members_present'],
                'quorumRequired' => (int) $data['quorum_required'],
                'quorumAchieved' => (int) $data['members_present'] >= (int) $data['quorum_required'],
                'confirmationDate' => $data['confirmation_date'],
                'attendees' => $data['attendees'] ?? [],
                'agendaItems' => $data['agenda_items'] ?? [],
                'actionItems' => $data['action_items'] ?? [],
            ],
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Build the EC Minutes PDF document from data.
     *
     * @param  array  $data  Data for the EC minutes.
     * @return PDF The generated PDF instance.
     */
    public function build(array $data)
    {
        return DocumentService::ecMinutes($data);
    }
}
