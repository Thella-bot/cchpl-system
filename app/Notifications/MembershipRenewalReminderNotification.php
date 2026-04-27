<?php

namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class MembershipRenewalReminderNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Membership $membership,
        protected int $daysUntilExpiry
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("CCHPL Membership Renewal Reminder - {$this->daysUntilExpiry} Day(s) Remaining")
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line("This is a friendly reminder that your CCHPL membership for the **" . ($this->membership->category?->name ?? 'Unspecified') . "** category is due for renewal soon.")
            ->line("Your membership will expire in **{$this->daysUntilExpiry} day(s)** on **" . $this->membership->expiry_date->format('d F Y') . "**.")
            ->line('To ensure uninterrupted access to your member benefits, please renew your membership at your earliest convenience.')
            ->action('Renew Your Membership Now', route('payment.initiate'))
            ->line('Thank you for being a valued member of our professional community.')
            ->salutation(
                'Kind regards,' . "\n" .
                'CCHPL Membership Services'
            );
    }
}