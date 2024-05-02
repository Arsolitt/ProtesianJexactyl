<?php

namespace Jexactyl\Listeners\Server;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
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
        $server = $event->server;
        $user = $server->user;
        Cache::forget('server_monthly_price_' . $server->id);
        Cache::forget('server_daily_price_' . $server->id);
        Cache::forget('server_hourly_price_' . $server->id);
        $user->update([
            'server_slots' => $user->server_slots + 1,
        ]);
    }
}
