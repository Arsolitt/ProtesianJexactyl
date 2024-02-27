<?php

namespace Jexactyl\Listeners\Server;

use Jexactyl\Events\Server\Created;
use Jexactyl\Events\User\UpdateCredits;

class CreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Created $event): void
    {
        $user = $event->server->user;
        $server = $event->server;
        // TODO: implement UserUpdateSlots
        $user->update([
            'server_slots' => $user->server_slots - 1,
        ]);
        UpdateCredits::dispatch($user, $server->hourlyPrice(), 'decrement');
    }
}
