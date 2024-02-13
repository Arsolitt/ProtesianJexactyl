<?php

namespace Jexactyl\Listeners\Server;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Jexactyl\Events\Server\Deleted;

class DeletedListener
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
    public function handle(Deleted $event): void
    {
        $user = $event->server->user;
        $user->update([
            'server_slots' => $user->server_slots + 1,
        ]);
    }
}
