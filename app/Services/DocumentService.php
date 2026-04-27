<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Service for generating PDF documents using DomPDF.
 * Loads Blade views and returns the PDF instance for streaming/downloading.
 */
class DocumentService
{
    /**
     * Generate Membership Certificate (CCHPL-MEM-002)
     * Format: A4 Landscape
     */
    public static function membershipCertificate(Membership $membership)
    {
        return Pdf::loadView('documents.certificate', [
            'membership' => $membership,
            'user'       => $membership->user,
            'category'   => $membership->category,
        ])->setPaper('a4', 'landscape');
    }

    /**
     * Generate Official Receipt (CCHPL-FIN-003)
     * Format: A4 Portrait
     */
    public static function officialReceipt(Payment $payment)
    {
        return Pdf::loadView('documents.receipt', [
            'payment'    => $payment,
            'membership' => $payment->membership,
            'user'       => $payment->membership->user,
        ])->setPaper('a4', 'portrait');
    }

    /**
     * Generate Welcome Pack (CCHPL-MEM-001)
     * Format: A4 Portrait
     */
    public static function welcomePack(Membership $membership)
    {
        return Pdf::loadView('documents.welcome-pack', [
            'membership' => $membership,
            'user'       => $membership->user,
            'category'   => $membership->category,
        ])->setPaper('a4', 'portrait');
    }

    /**
     * Generate AGM Notice & Agenda (CCHPL-OPS-001)
     * Format: A4 Portrait
     *
     * @param array $data Contains keys like: date, time, venue, agenda_items, etc.
     */
    public static function agmNotice(array $data)
    {
        return Pdf::loadView('documents.agm-notice', [
            'data' => $data
        ])->setPaper('a4', 'portrait');
    }

    /**
     * Generate EC Meeting Minutes (CCHPL-OPS-002)
     * Format: A4 Portrait
     *
     * @param array $data Contains keys like: meetingNo, attendees, minutes, etc.
     */
    public static function ecMinutes(array $data)
    {
        return Pdf::loadView('documents.ec-minutes', [
            'data' => $data
        ])->setPaper('a4', 'portrait');
    }
}