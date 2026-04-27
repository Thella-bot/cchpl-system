<?php

namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('CCHPL — Application Approved')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Congratulations! Your application for CCHPL membership has been **APPROVED**.')
            ->line('To activate your membership, please log in to your dashboard and proceed to the payment section to settle your annual membership fee.')
            ->action('Proceed to Payment', route('payment.initiate'))
            ->line('Once payment is verified, you will receive your official membership certificate and welcome pack.')
            ->salutation(
                'Kind regards,' . "\n" .
                'The Executive Committee'
            );
    }
}