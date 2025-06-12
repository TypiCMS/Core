<?php

namespace TypiCMS\Modules\Core\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    public function __construct(public readonly string $token) {}

    /** @return string[] */
    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage())
            ->line(__('You are receiving this email because we received a password reset request for your account.'))
            ->action(__('Reset Password'), route(app()->getLocale() . '::password.reset', ['token' => $this->token]))
            ->line(__('If you did not request a password reset, no further action is required.'));
    }
}
