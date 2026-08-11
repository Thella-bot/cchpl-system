<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentService
{
    /**
     * Generate a membership certificate PDF for a given membership.
     *
     * @param  Membership  $membership  The membership instance.
     * @return \Barryvdh\DomPDF\PDF The generated PDF instance.
     */
    public static function membershipCertificate(Membership $membership)
    {
        return Pdf::loadView('documents.certificate', [
            'membership' => $membership,
            'user' => $membership->user,
            'category' => $membership->category,
        ])->setPaper('a4', 'landscape');
    }

    /**
     * Generate an official receipt PDF for a given payment.
     *
     * @param  Payment  $payment  The payment instance.
     * @return \Barryvdh\DomPDF\PDF The generated PDF instance.
     */
    public static function officialReceipt(Payment $payment)
    {
        return Pdf::loadView('documents.receipt', [
            'payment' => $payment,
            'membership' => $payment->membership,
            'user' => $payment->membership->user,
        ])->setPaper('a4', 'portrait');
    }

    /**
     * Generate a welcome pack PDF for a given membership.
     *
     * @param  Membership  $membership  The membership instance.
     * @return \Barryvdh\DomPDF\PDF The generated PDF instance.
     */
    public static function welcomePack(Membership $membership)
    {
        return Pdf::loadView('documents.welcome-pack', [
            'membership' => $membership,
            'user' => $membership->user,
            'category' => $membership->category,
        ])->setPaper('a4', 'portrait');
    }

    /**
     * Generate an AGM notice PDF from provided data.
     *
     * @param  array  $data  Data for the AGM notice.
     * @return \Barryvdh\DomPDF\PDF The generated PDF instance.
     */
    public static function agmNotice(array $data)
    {
        return Pdf::loadView('documents.agm-notice', [
            'data' => $data,
        ])->setPaper('a4', 'portrait');
    }

    /**
     * Generate EC minutes PDF from provided data.
     *
     * @param  array  $data  Data for the EC minutes.
     * @return \Barryvdh\DomPDF\PDF The generated PDF instance.
     */
    public static function ecMinutes(array $data)
    {
        return Pdf::loadView('documents.ec-minutes', [
            'data' => $data,
        ])->setPaper('a4', 'portrait');
    }

    /**
     * Send a generated document to a member via email.
     *
     * @param  Membership  $membership  The membership instance.
     * @param  string  $documentType  The type of document to send.
     * @param  Payment|null  $payment  Optional payment instance.
     * @param  string  $subject  Optional email subject.
     */
    public static function sendToMember(
        Membership $membership,
        string $documentType,
        ?Payment $payment = null,
        string $subject = ''
    ): void {
        // Implementation here
    }
}
