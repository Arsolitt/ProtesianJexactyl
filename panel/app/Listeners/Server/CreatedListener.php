<?php

namespace Jexactyl\Listeners\Server;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Jexactyl\Events\Server\Created;

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
        $user->update([
            'credits' => $user->credits - $server->hourlyPrice(),
            'server_slots' => $user->server_slots - 1,
        ]);
    }
}
