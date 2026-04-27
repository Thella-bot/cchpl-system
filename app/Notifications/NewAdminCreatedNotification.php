<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAdminCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected User $user,
        protected string $plainPassword
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your CCHPL Admin Account Has Been Created')
            ->greeting('Welcome, ' . $this->user->name . '!')
            ->line('A new administrator account has been created for you on the CCHPL System.')
            ->line('You can log in using the following credentials:')
            ->line('**Email:** ' . $this->user->email)
            ->line('**Password:** ' . $this->plainPassword)
            ->line('**Important:** Please change your password immediately after your first login for security reasons.')
            ->action('Login to Admin Panel', route('login'))
            ->salutation(
                'Regards,' . "\n" .
                'CCHPL Super Admin'
            );
    }
}