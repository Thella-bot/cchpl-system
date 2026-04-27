<?php
namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Membership $membership) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $category  = $this->membership->category?->name ?? '—';
        $memberId  = $this->membership->member_id ?? '—';
        $validUntil = $this->membership->expiry_date
            ? '31 March ' . $this->membership->expiry_date->year
            : '31 March ' . now()->addYear()->year;
        $annualFee = 'M' . number_format($this->membership->category?->annual_fee ?? 0, 2);

        return (new MailMessage)
            ->subject('Welcome to CCHPL — Your Membership Is Active')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('On behalf of the Executive Committee and all members of CCHPL, we are delighted to confirm that your membership is now **active**.')
            ->line('---')
            ->line('**Your membership details:**')
            ->line('Member ID: **' . $memberId . '**')
            ->line('Category: ' . $category)
            ->line('Annual fee: ' . $annualFee)
            ->line('Valid until: **' . $validUntil . '**')
            ->line('Voting rights: ' . (($this->membership->category?->voting_rights ?? false) ? 'Yes' : 'No'))
            ->line('---')
            ->line('**Your benefits include:** newsletter and industry updates, member directory access, preferential event rates, members-only job board, certification pathway guidance, and advocacy representation.')
            ->line('**Your obligations:** pay annual fees by 31 March each year, uphold the CCHPL Code of Ethics, and abide by the Constitution and Bylaws.')
            ->line('Your membership certificate has been sent as a separate email. Please keep it for your records.')
            ->action('Go to My Dashboard', url(route('member.dashboard')))
            ->salutation('Ke khotso, ke nala, ke bohlokoa — With peace, plenty, and purpose.' . "\nCCHPL Executive Committee");
    }
}
