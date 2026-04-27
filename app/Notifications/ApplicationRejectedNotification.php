<?php

namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Membership $membership,
        protected ?string $reason = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('CCHPL — Application Status Update')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Thank you for your interest in the Council for Culinary and Hospitality Professionals Lesotho.')
            ->line('After careful review of your application for the **' . ($this->membership->category?->name ?? 'selected category') . '** category, we regret to inform you that we are unable to approve your membership at this time.');

        if ($this->reason) {
            $message->line('**Reason:** ' . $this->reason);
        }

        return $message->line('If you believe this decision was made in error or if you have rectified the issues mentioned, you may contact us or submit a new application.');
    }
}