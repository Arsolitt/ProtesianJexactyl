<?php

namespace Jexactyl\Listeners\Server;

use Jexactyl\Events\Server\Creating;

class CreatingListener
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
    public function handle(Creating $event): bool
    {
        $event->server->monthly_price = $event->server->actualPrice();
        return true;
    }
}
