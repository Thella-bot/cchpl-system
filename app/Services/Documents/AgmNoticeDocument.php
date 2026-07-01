<?php
namespace App\Services\Documents;

use App\Models\DocumentReview;
use App\Services\DocumentService;
use Illuminate\Http\Request;

/**
 * Service class for handling AGM Notice document creation and building.
 *
 * Centralizes AGM Notice logic for maintainability and reusability.
 */
class AgmNoticeDocument
{
    /**
     * Store a new AGM Notice document review from request data.
     *
     * @param Request $request The HTTP request containing AGM notice data.
     * @return DocumentReview The created document review instance.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'date'                => 'required|string|max:50|regex:/^[A-Za-z]+,?\s+\d{1,2}\s+[A-Za-z]+\s+\d{4}$/',
            'time'                => 'required|string|max:20|regex:/^\d{1,2}:\d{2}\s?(AM|PM)?$/',
            'venue'               => 'required|string|max:255',
            'format'              => 'required|in:in-person,hybrid,online',
            'online_link'         => 'nullable|url|max:500',
            'contact_name'        => 'required|string|max:255',
            'contact_email'       => 'required|email|max:255',
            'contact_phone'       => 'required|string|max:30|regex:/^(\+|[0-9])[0-9\s\-\(\)]{7,20}$/',
            'notice_date'         => 'required|string|max:50',
            'issued_by'           => 'required|string|max:255',
            'paid_up_deadline'    => 'required|string|max:50',
            'proxy_deadline'      => 'required|string|max:50',
            'nomination_deadline' => 'required|string|max:50',
            'agm_year'            => 'required|integer|min:2000|max:2100',
        ]);

        // Create a new document review for the AGM notice
        return DocumentReview::create([
            'type'           => DocumentReview::TYPE_AGM_NOTICE,
            'status'         => DocumentReview::STATUS_PENDING_REVIEW,
            'recipient_type' => DocumentReview::RECIPIENT_ALL_PAID_UP,
            'recipient_name' => 'All paid-up members',
            'data'           => [
                'date'               => $data['date'],
                'time'               => $data['time'],
                'venue'              => $data['venue'],
                'format'             => $data['format'],
                'onlineLink'         => $data['online_link'] ?? null,
                'contactName'        => $data['contact_name'],
                'contactEmail'       => $data['contact_email'],
                'contactPhone'       => $data['contact_phone'],
                'noticeDate'         => $data['notice_date'],
                'issuedBy'           => $data['issued_by'],
                'paidUpDeadline'     => $data['paid_up_deadline'],
                'proxyDeadline'      => $data['proxy_deadline'],
                'nominationDeadline' => $data['nomination_deadline'],
                'agmYear'            => (int) $data['agm_year'],
            ],
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Build the AGM Notice PDF document from data.
     *
     * @param array $data Data for the AGM notice.
     * @return \Barryvdh\DomPDF\PDF The generated PDF instance.
     */
    public function build(array $data)
    {
        return DocumentService::agmNotice($data);
    }
}
