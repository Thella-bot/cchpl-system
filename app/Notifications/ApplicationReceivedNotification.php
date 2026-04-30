<?php

namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Membership $membership)
    {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('CCHPL — Application Received')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Thank you for your application for membership to the Council for Culinary and Hospitality Professionals Lesotho (CCHPL).')
            ->line('Your application for the **' . $this->membership->category->name . '** category has been received and is now pending review by the Membership Committee.')
            ->line('You will be notified of the outcome within 60 days.')
            ->salutation(
                'Kind regards,' . "\n" .
                'The CCHPL Membership Committee'
            );
    }
}

