<?php

namespace Jexactyl\Listeners\User;

use Illuminate\Contracts\Queue\ShouldQueue;
use Jexactyl\Events\User\UpdateCredits;

class UpdateCreditsListener implements ShouldQueue
{
    public string $queue = 'standard';

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
    public function handle(UpdateCredits $event): void
    {
        match ($event->action) {
            'increment' => $event->user->increment('credits', $event->credits),
            'decrement' => $event->user->decrement('credits', $event->credits),
            'set' => $event->user->update(['credits' => $event->credits]),
        };
    }
}
