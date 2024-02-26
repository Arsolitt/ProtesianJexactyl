<?php

namespace Jexactyl\Contracts\Store;

use Jexactyl\Models\Payment;

interface PaymentGatewayInterface
{
    function handle(Payment $payment): Payment;
}
