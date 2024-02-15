<?php

namespace Jexactyl\Listeners\Server;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Jexactyl\Events\Server\Updated;

class UpdatedListener
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
    public function handle(Updated $event): void
    {
        $server = $event->server;
        Cache::forget('server_monthly_price_' . $server->id);
        Cache::forget('server_daily_price_' . $server->id);
        Cache::forget('server_hourly_price_' . $server->id);
    }
}
