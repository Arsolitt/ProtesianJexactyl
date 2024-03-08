<?php

namespace Jexactyl\Listeners\Auth;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Jexactyl\Events\Auth\OAuthLogin;
use Jexactyl\Facades\Activity;
use Throwable;

class OAuthLoginListener
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
     * @throws Throwable
     */
    public function handle(OAuthLogin $event): void
    {
        $activity = Activity::withRequestMetadata();
        if ($event->user) {

            $activity = $activity->subject($event->user);
        }

        $activity->event('oAuth:success.'.$event->method)->log();
    }
}
