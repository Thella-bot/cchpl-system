<?php
namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Membership $membership,
        protected string     $status,
        protected ?string    $reason = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return match ($this->status) {
            'approved' => $this->approvedMail($notifiable),
            'rejected' => $this->rejectedMail($notifiable),
            default    => $this->genericMail($notifiable),
        };
    }

    // ── Approved ──────────────────────────────────────────────────────────

    private function approvedMail($notifiable): MailMessage
    {
        $memberId  = $this->membership->member_id ?? 'To be assigned';
        $category  = $this->membership->category?->name ?? 'Unassigned';
        $annualFee = 'M' . number_format($this->membership->category?->annual_fee ?? 0, 2);
        $joiningFee = $this->membership->category?->joining_fee ?? 0;
        $totalDue  = ($this->membership->category?->annual_fee ?? 0) + $joiningFee;

        $mail = (new MailMessage)
            ->subject('Your CCHPL Membership Application Has Been Approved')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('We are pleased to inform you that your application for **' . $category . '** membership with the Council for Culinary and Hospitality Professionals Lesotho has been **approved**.')
            ->line('---')
            ->line('**Your membership details:**')
            ->line('Member ID: **' . $memberId . '**')
            ->line('Category: ' . $category)
            ->line('Annual fee: ' . $annualFee);

        if ($joiningFee > 0) {
            $mail->line('One-time joining fee: M' . number_format($joiningFee, 2));
        }

        $mail->line('**Total due now: M' . number_format($totalDue, 2) . '**')
            ->line('---')
            ->line('**What to do next:**')
            ->line('Please proceed to pay your membership fee via M-Pesa or EcoCash. Your membership will be activated as soon as your payment is verified.')
            ->action('Pay Membership Fee Now', url(route('payment.initiate')))
            ->line('Fees are due by **31 March** each year. Late payment attracts a 10% penalty per the CCHPL Bylaws.')
            ->line('---')
            ->line('If you have any questions please contact the Secretary at secretary@cchpl.org.ls.')
            ->salutation('Ke khotso, ke nala, ke bohlokoa — With peace, plenty, and purpose.' . "\nCCHPL Executive Committee");

        return $mail;
    }

    // ── Rejected ──────────────────────────────────────────────────────────

    private function rejectedMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Update on Your CCHPL Membership Application')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Thank you for your interest in joining the Council for Culinary and Hospitality Professionals Lesotho.')
            ->line('After reviewing your application for **' . ($this->membership->category?->name ?? 'selected') . '** membership, the Membership Committee has been unable to approve it at this time.');

        if ($this->reason) {
            $mail->line('**Reason provided:** ' . $this->reason);
        }

        $mail->line('You are welcome to address the feedback above and reapply. If you believe this decision is in error, you have the right to appeal to the AGM in writing to the Secretary.')
            ->action('View Your Application', url(route('member.dashboard')))
            ->line('For queries please contact secretary@cchpl.org.ls.')
            ->salutation('CCHPL Membership Committee');

        return $mail;
    }

    // ── Generic fallback ──────────────────────────────────────────────────

    private function genericMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update on Your CCHPL Membership Application')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Your membership application status has been updated to: **' . ucfirst($this->status) . '**.')
            ->action('View Dashboard', url(route('member.dashboard')))
            ->salutation('CCHPL Membership Committee');
    }
}
