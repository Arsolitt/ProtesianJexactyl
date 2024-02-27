<?php

namespace Jexactyl\Listeners\Payment;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Jexactyl\Events\Store\PaymentCanceled;

class CanceledListener
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
    public function handle(PaymentCanceled $event): void
    {
        //
    }
}
