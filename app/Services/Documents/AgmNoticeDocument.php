<?php
namespace App\Services\Documents;

use App\Models\DocumentReview;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class AgmNoticeDocument
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'date'                => 'required|string',
            'time'                => 'required|string',
            'venue'               => 'required|string',
            'format'              => 'required|in:in-person,hybrid,online',
            'online_link'         => 'nullable|string',
            'contact_name'        => 'required|string',
            'contact_email'       => 'required|email',
            'contact_phone'       => 'required|string',
            'notice_date'         => 'required|string',
            'issued_by'           => 'required|string',
            'paid_up_deadline'    => 'required|string',
            'proxy_deadline'      => 'required|string',
            'nomination_deadline' => 'required|string',
            'agm_year'            => 'required|integer',
        ]);

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

    public function build(array $data)
    {
        return DocumentService::agmNotice($data);
    }
}
