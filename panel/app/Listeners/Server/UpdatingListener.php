<?php

namespace Jexactyl\Listeners\Server;

use Jexactyl\Events\Server\Updating;

class UpdatingListener
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
    public function handle(Updating $event): bool
    {
        $event->server->monthly_price = $event->server->actualPrice();
        return true;
    }
}
