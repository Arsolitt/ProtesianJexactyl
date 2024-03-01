<?php

namespace Jexactyl\Listeners\Server;

use Illuminate\Contracts\Queue\ShouldQueue;
use Jexactyl\Events\Store\ServerEdit;
use Jexactyl\Events\User\UpdateCredits;

class EditListener implements ShouldQueue
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
    public function handle(ServerEdit $event): void
    {
        $resources = $event->resources;
        $event->server->update([
            'memory' => $resources['memory'],
            'disk' => $resources['disk'],
            'allocation_limit' => $resources['allocations'],
            'backup_limit' => $resources['backups'],
            'database_limit' => $resources['databases'],
        ]);
        UpdateCredits::dispatch($event->server->user, (float)$event->server->monthlyPrice() / 30 / 24, 'decrement');
    }
}
