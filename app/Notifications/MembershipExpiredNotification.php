<?php

namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class MembershipExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(protected Membership $membership)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your CCHPL Membership Has Expired')
            ->level('warning')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line("We're writing to inform you that your CCHPL membership in the **" . ($this->membership->category?->name ?? 'Uncategorized') . "** category expired on **" . $this->membership->expiry_date->format('d F Y') . "**.")
            ->line('Your access to member benefits, resources, and the use of your professional designation has been suspended.')
            ->line('You can reactivate your membership by logging in and making a renewal payment.')
            ->action('Renew Your Membership', route('payment.initiate'))
            ->line('We hope to see you back soon.');
    }
}