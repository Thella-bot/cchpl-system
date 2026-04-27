<?php
namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Payment $payment,
        protected string  $status,
        protected ?string $notes = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return match ($this->status) {
            'verified' => $this->verifiedMail($notifiable),
            'rejected' => $this->rejectedMail($notifiable),
            default    => $this->genericMail($notifiable),
        };
    }

    private function verifiedMail($notifiable): MailMessage
    {
        $membership = $this->payment->membership;

        return (new MailMessage)
            ->subject('CCHPL — Payment Verified & Membership Active')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Your payment of **M' . number_format($this->payment->amount, 2) . '** has been verified and your CCHPL membership is now active.')
            ->line('Reference: ' . $this->payment->transaction_reference)
            ->line('Receipt No.: ' . ($this->payment->receipt_number ?? '—'))
            ->line('Provider: ' . ucfirst($this->payment->provider))
            ->line('Membership valid until: **' . ($membership->expiry_date ? $membership->expiry_date->format('31 F Y') : '—') . '**')
            ->line('Your official receipt and membership certificate have been sent as separate emails.')
            ->action('View Dashboard', url(route('member.dashboard')))
            ->salutation('CCHPL Treasurer | treasurer@cchpl.org.ls');
    }

    private function rejectedMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('CCHPL — Payment Proof Could Not Be Verified')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Unfortunately we were unable to verify your payment proof for **M' . number_format($this->payment->amount, 2) . '** (Reference: ' . $this->payment->transaction_reference . ').');

        if ($this->notes) {
            $mail->line('**Reason:** ' . $this->notes);
        }

        $mail->line('Please ensure you upload a clear screenshot of the completed transaction, showing the amount, date, merchant/shortcode, and confirmation message.')
            ->action('Resubmit Payment', url(route('payment.initiate')))
            ->line('If you believe this is an error please contact treasurer@cchpl.org.ls with your transaction confirmation.');

        return $mail;
    }

    private function genericMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('CCHPL — Payment Update')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Your payment of M' . number_format($this->payment->amount, 2) . ' has been updated to: **' . ucfirst($this->status) . '**.')
            ->action('View Dashboard', url(route('member.dashboard')));
    }
}
