<?php
namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipExpiryReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Membership $membership) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $days       = $this->membership->daysUntilExpiry();
        $expiryDate = $this->membership->expiry_date->format('d F Y');
        $annualFee  = $this->membership->category->annual_fee;
        $penalty    = round($annualFee * 0.10, 2);

        return (new MailMessage)
            ->subject('CCHPL — Membership Renewal Reminder')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Your CCHPL **' . $this->membership->category->name . '** membership is due to expire on **' . $expiryDate . '** — that is in **' . $days . ' day(s)**.')
            ->line('To keep your membership active and avoid a late penalty, please renew before **31 March**.')
            ->line('Annual fee: M' . number_format($annualFee, 2))
            ->line('Late payment penalty (if paid after 31 March): M' . number_format($penalty, 2) . ' (10%)')
            ->action('Renew Now', url(route('payment.initiate')))
            ->line('If you have already made payment, please allow 24 hours for our team to verify your proof.')
            ->salutation('CCHPL Secretary | secretary@cchpl.org.ls');
    }
}
