<?php

namespace Jexactyl\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Jexactyl\Contracts\Core\ReceivesEvents;
use Jexactyl\Events\Event;
use Jexactyl\Events\Server\Installed;
use Jexactyl\Models\Server;
use Jexactyl\Models\User;

class ServerInstalled extends Notification implements ShouldQueue, ReceivesEvents
{
    use Queueable;

    public Server $server;

    public User $user;

    /**
     * Handle a direct call to this notification from the server installed event. This is configured
     * in the event service provider.
     */
    public function handle(Event|Installed $event): void
    {
        $event->server->loadMissing('user');

        $this->server = $event->server;
        $this->user = $event->server->user;

        // Since we are calling this notification directly from an event listener we need to fire off the dispatcher
        // to send the email now. Don't use send() or you'll end up firing off two different events.
        Container::getInstance()->make(Dispatcher::class)->sendNow($this->user, $this);
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
        return (new MailMessage())
            ->greeting('Привет ' . $this->user->username . '!')
            ->line('Твой сервер установлен.')
            ->line('Название: ' . $this->server->name)
            ->action('Пора настраивать!', route('index'));
    }
}
