<?php
namespace App\Services\Documents;

use App\Models\DocumentReview;
use App\Services\DocumentService;
use Illuminate\Http\Request;

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
     * @param Request $request The HTTP request containing EC minutes data.
     * @return DocumentReview The created document review instance.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'meeting_no'        => 'required|string',
            'meeting_type'      => 'required|in:regular,special,emergency',
            'date'              => 'required|string',
            'start_time'        => 'required|string',
            'end_time'          => 'required|string',
            'venue'             => 'required|string',
            'secretary'         => 'required|string',
            'chairperson'       => 'required|string',
            'total_ec_members'  => 'required|integer',
            'members_present'   => 'required|integer',
            'quorum_required'   => 'required|integer',
            'confirmation_date' => 'required|string',
            'attendees'         => 'nullable|array',
            'agenda_items'      => 'nullable|array',
            'action_items'      => 'nullable|array',
        ]);

        // Create a new document review for the EC minutes
        return DocumentReview::create([
            'type'           => DocumentReview::TYPE_EC_MINUTES,
            'status'         => DocumentReview::STATUS_PENDING_REVIEW,
            'recipient_type' => DocumentReview::RECIPIENT_EC_MEMBERS,
            'recipient_name' => 'EC Members',
            'data'           => [
                'meetingNo'        => $data['meeting_no'],
                'meetingType'      => $data['meeting_type'],
                'date'             => $data['date'],
                'startTime'        => $data['start_time'],
                'endTime'          => $data['end_time'],
                'venue'            => $data['venue'],
                'secretary'        => $data['secretary'],
                'chairperson'      => $data['chairperson'],
                'totalEcMembers'   => (int) $data['total_ec_members'],
                'membersPresent'   => (int) $data['members_present'],
                'quorumRequired'   => (int) $data['quorum_required'],
                'quorumAchieved'   => (int) $data['members_present'] >= (int) $data['quorum_required'],
                'confirmationDate' => $data['confirmation_date'],
                'attendees'        => $data['attendees'] ?? [],
                'agendaItems'      => $data['agenda_items'] ?? [],
                'actionItems'      => $data['action_items'] ?? [],
            ],
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Build the EC Minutes PDF document from data.
     *
     * @param array $data Data for the EC minutes.
     * @return \Barryvdh\DomPDF\PDF The generated PDF instance.
     */
    public function build(array $data)
    {
        return DocumentService::ecMinutes($data);
    }
}
