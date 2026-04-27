<?php
namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspensionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Membership $membership) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $annualFee    = $this->membership->category->annual_fee;
        $penalty      = round($annualFee * 0.10, 2);
        $totalDue     = $annualFee + $penalty;
        $expiredDate  = $this->membership->expiry_date?->format('d F Y') ?? '—';

        return (new MailMessage)
            ->subject('CCHPL — Membership Suspended: Action Required')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('We regret to inform you that your CCHPL **' . $this->membership->category->name . '** membership has been **suspended** due to non-payment for more than six months.')
            ->line('Your membership expired on **' . $expiredDate . '** and fees have remained outstanding, as defined under Bylaw 1.3 of the CCHPL Bylaws.')
            ->line('---')
            ->line('**To reinstate your membership, the following is payable:**')
            ->line('Annual membership fee: M' . number_format($annualFee, 2))
            ->line('Late payment penalty (10%): M' . number_format($penalty, 2))
            ->line('**Total due: M' . number_format($totalDue, 2) . '**')
            ->action('Pay Now to Reinstate', url(route('payment.initiate')))
            ->line('If you believe this suspension is in error, please contact the Secretary at secretary@cchpl.org.ls immediately.')
            ->salutation('CCHPL Secretary | secretary@cchpl.org.ls');
    }
}
