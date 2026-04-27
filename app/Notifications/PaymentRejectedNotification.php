<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification implements ShouldQueue
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
        $message = (new MailMessage)
            ->subject('CCHPL — Payment Verification Failed')
            ->level('error')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('We were unable to verify your payment for reference **' . $this->payment->transaction_reference . '**.');

        if ($this->payment->verification_notes) {
            $message->line('**Reason:** ' . $this->payment->verification_notes);
        }

        return $message->line('Please check your transaction details and try uploading the proof again, or contact us if you believe this is a mistake.')
            ->action('View Payment Details', route('payment.initiate'));
    }
}