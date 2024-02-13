<?php

namespace Jexactyl\Listeners\Server;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
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
        $server = $event->server;
        $currentPrice = $server->monthly_price;
        $actualPrice = $server->actualPrice();
        if ($currentPrice !== $actualPrice) {
            Cache::forget('server_monthly_price_' . $server->id);
            Cache::forget('server_daily_price_' . $server->id);
            Cache::forget('server_hourly_price_' . $server->id);
            $event->server->update(['monthly_price' => $actualPrice]);
        }
        return true;
    }
}
