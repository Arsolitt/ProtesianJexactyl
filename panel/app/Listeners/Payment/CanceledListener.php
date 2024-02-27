<?php

namespace Jexactyl\Listeners\Payment;

use Illuminate\Contracts\Queue\ShouldQueue;
use Jexactyl\Events\Store\PaymentCanceled;

class CanceledListener implements ShouldQueue
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
    public function handle(PaymentCanceled $event): void
    {
        $event->payment->update([
            'status' => 'canceled',
        ]);
    }
}
