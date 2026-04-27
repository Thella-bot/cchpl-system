<?php

namespace App\Notifications;

use App\Models\Resignation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResignationSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Resignation $resignation)
    {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('CCHPL — Resignation Request Received')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('We have received your request to resign from the Council for Culinary and Hospitality Professionals Lesotho.')
            ->line('**Requested Effective Date:** ' . $this->resignation->effective_date->format('d F Y'))
            ->line('Your request is currently pending review by the Secretary. Please note that you remain a member with full rights and obligations until your resignation is formally acknowledged.')
            ->line('You will receive a formal acknowledgement letter shortly.')
            ->salutation(
                'Kind regards,' . "\n" .
                'The Executive Committee'
            );
    }
}