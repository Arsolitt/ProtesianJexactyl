<?php

namespace Jexactyl\Listeners\Server;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
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
        $server = $event->server;
        $server->monthly_price = $server->actualPrice();
        return true;
    }
}
