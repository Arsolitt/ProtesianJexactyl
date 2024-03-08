<?php

namespace Jexactyl\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Jexactyl\Models\User;

class AccountCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user, public ?string $token = null)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(): MailMessage
    {
        $message = (new MailMessage())
            ->greeting('Привет ' . $this->user->name . '!')
            ->line('Ты получил это письмо, потому что зарегистрировался на ' . config('app.name') . '.')
            ->line('Имя пользователя: ' . $this->user->username)
            ->line('Email: ' . $this->user->email);

        if (!is_null($this->token)) {
            return $message->action('Подтвердить аккаунт', url('/auth/password/reset/' . $this->token . '?email=' . urlencode($this->user->email)));
        }

        return $message;
    }
}
