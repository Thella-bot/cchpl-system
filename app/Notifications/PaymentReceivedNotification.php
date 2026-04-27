<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
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
            ->subject('CCHPL — Payment Received')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('We have received your payment of **M ' . number_format($this->payment->amount, 2) . '** for **' . $this->payment->purpose . '**.')
            ->line('Your transaction reference is **' . $this->payment->transaction_reference . '**.')
            ->line('The payment is now pending verification by our finance team. You will be notified once the verification is complete.')
            ->salutation(
                'Kind regards,' . "\n" .
                'CCHPL Finance Team'
            );
    }
}
