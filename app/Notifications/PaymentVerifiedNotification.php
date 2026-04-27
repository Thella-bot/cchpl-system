<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Payment $payment)
    {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('CCHPL — Payment Verified')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Your payment of **M ' . number_format($this->payment->amount, 2) . '** has been successfully verified.')
            ->line('Your membership is now active and in good standing.')
            ->line('You can download your official receipt and membership certificate from your dashboard.')
            ->action('Go to Dashboard', route('member.dashboard'))
            ->salutation(
                'Kind regards,' . "\n" .
                'CCHPL Finance Team'
            );
    }
}