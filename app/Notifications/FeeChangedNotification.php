<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\MembershipCategory;

class FeeChangedNotification extends Notification
{
    use Queueable;

    protected $category;
    protected $oldFee;

    public function __construct(MembershipCategory $category, $oldFee)
    {
        $this->category = $category;
        $this->oldFee = $oldFee;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Membership Fee Updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The membership fee for "' . $this->category->name . '" has changed.')
            ->line('Old fee: M' . number_format($this->oldFee, 2))
            ->line('New fee: M' . number_format($this->category->annual_fee, 2))
            ->action('View categories', url('/admin/memberships/categories'))
            ->line('Thank you for staying with CCHPL.');
    }
}
