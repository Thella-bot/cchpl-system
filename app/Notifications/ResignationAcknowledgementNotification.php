<?php

namespace App\Notifications;

use App\Models\Resignation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResignationAcknowledgementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Resignation $resignation)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('CCHPL — Resignation Acknowledgement')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('This email serves as formal acknowledgement of your resignation from the Council for Culinary and Hospitality Professionals Lesotho, effective ' . $this->resignation->effective_date->format('d F Y') . '.')
            ->line('Your membership status has been updated to "Resigned". You are no longer entitled to member benefits or the use of the CCHPL professional designation.')
            ->line('We thank you for your past membership and wish you the best in your future endeavors.');

        if (!empty($this->resignation->acknowledgement_notes)) {
            $mailMessage->line('---')
                        ->line('**A note from the Secretary:**')
                        ->line(new \Illuminate\Support\HtmlString(nl2br(e($this->resignation->acknowledgement_notes))));
        }

        if ($this->resignation->balance_outstanding > 0) {
            $mailMessage->line('---')
                        ->line('**Outstanding Balance:** Please note that our records indicate an outstanding balance of M ' . number_format($this->resignation->balance_outstanding, 2) . '. Please contact finance@cchpl.org.ls to arrange for settlement.');
        }

        return $mailMessage->salutation(
            'Sincerely,' . "\n" .
            'The Executive Committee'
        );
    }
}