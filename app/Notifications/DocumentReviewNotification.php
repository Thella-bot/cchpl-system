<?php
namespace App\Notifications;

use App\Models\MembershipDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected MembershipDocument $document,
        protected string             $status,
        protected ?string            $reason = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $label = ucfirst($this->status);

        $mail = (new MailMessage)
            ->subject('CCHPL — Document Review Update: ' . $this->document->document_type)
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Your document **"' . $this->document->document_type . '"** has been reviewed.')
            ->line('Status: **' . $label . '**');

        if ($this->status === 'rejected') {
            if ($this->reason) {
                $mail->line('Reason: ' . $this->reason);
            }
            $mail->line('Please upload a corrected version by visiting your dashboard.');
        }

        $mail->action('View Application', url(route('member.dashboard')))
            ->line('If you have questions please contact membership@cchpl.org.ls.')
            ->salutation('CCHPL Membership Committee');

        return $mail;
    }
}
