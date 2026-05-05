<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class DocumentService
{

public static function membershipCertificate(Membership $membership)
    {
        return Pdf::loadView('documents.certificate', [
            'membership' => $membership,
            'user'       => $membership->user,
            'category'   => $membership->category,
        ])->setPaper('a4', 'landscape');
    }

public static function officialReceipt(Payment $payment)
    {
        return Pdf::loadView('documents.receipt', [
            'payment'    => $payment,
            'membership' => $payment->membership,
            'user'       => $payment->membership->user,
        ])->setPaper('a4', 'portrait');
    }

public static function welcomePack(Membership $membership)
    {
        return Pdf::loadView('documents.welcome-pack', [
            'membership' => $membership,
            'user'       => $membership->user,
            'category'   => $membership->category,
        ])->setPaper('a4', 'portrait');
    }

public static function agmNotice(array $data)
    {
        return Pdf::loadView('documents.agm-notice', [
            'data' => $data
        ])->setPaper('a4', 'portrait');
    }

public static function ecMinutes(array $data)
    {
        return Pdf::loadView('documents.ec-minutes', [
            'data' => $data
        ])->setPaper('a4', 'portrait');
    }

public static function sendToMember(
        Membership $membership,
        string $documentType,
        ?Payment $payment = null,
        string $subject = ''
    ): void {
        $user = $membership->user;

if (!$user || !$user->email) {
            throw new \RuntimeException("Member #{$membership->id} has no valid email address.");
        }

$pdf = match ($documentType) {
            'certificate'  => self::membershipCertificate($membership),
            'welcome_pack' => self::welcomePack($membership),
            'receipt'      => $payment
                ? self::officialReceipt($payment)
                : throw new \InvalidArgumentException('Payment is required for receipt documents.'),
            default => throw new \InvalidArgumentException("Unsupported document type: {$documentType}"),
        };

$filename = match ($documentType) {
            'certificate'  => "cchpl-certificate-{$membership->member_id}.pdf",
            'welcome_pack' => "cchpl-welcome-pack-{$membership->member_id}.pdf",
            'receipt'      => "cchpl-receipt-{$payment->receipt_number}.pdf",
        };

$defaultSubject = match ($documentType) {
            'certificate'  => 'Your CCHPL Membership Certificate',
            'welcome_pack' => 'Welcome to CCHPL — Your Member Pack',
            'receipt'      => 'Your CCHPL Payment Receipt',
        };

$emailSubject = $subject ?: $defaultSubject;

$tmpPath = storage_path("app/tmp/{$filename}");
        if (!is_dir(dirname($tmpPath))) {
            mkdir(dirname($tmpPath), 0755, true);
        }

$pdf->save($tmpPath);

try {
            Mail::send([], [], function ($message) use ($user, $emailSubject, $tmpPath, $filename) {
                $message->to($user->email, $user->name)
                    ->subject("CCHPL — {$emailSubject}")
                    ->text("Dear {$user->name},\n\nPlease find your {$emailSubject} attached.\n\nKind regards,\nCCHPL Secretary\nsecretary@cchpl.org.ls")
                    ->attach($tmpPath, ['as' => $filename, 'mime' => 'application/pdf']);
            });
        } finally {
            @unlink($tmpPath);
        }
    }
}

