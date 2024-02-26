<?php

namespace Jexactyl\Events\Store;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Jexactyl\Models\Payment;

class PaymentPaid
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private Payment $payment)
    {

    }

}
