<?php

namespace Jexactyl\Listeners\User;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Jexactyl\Events\User\LastActivity;

class LastActivityListener implements ShouldQueue
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
    public function handle(LastActivity $event): void
    {
        $user=$event->user;
        $ip=$event->ip;
        if (!Cache::has('last_activity_'.$event->user->id)) {
            Cache::set('last_activity_'.$user->id, $ip, 5 * 60);
            $user->update([
                'last_activity' => Carbon::now(),
                'last_ip' => $ip
            ]);
        }
    }
}
