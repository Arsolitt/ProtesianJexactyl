<?php

namespace Jexactyl\Services\Store;

use Illuminate\Support\Facades\Log;
use Jexactyl\Models\Payment;

class PaymentProcessingService
{

    public function paid(Payment $payment): void
    {
        // TODO: Add payment processing
        Log::debug('Payment paid', ['payment' => $payment->toArray()]);
    }

    public function canceled(Payment $payment): void
    {
        // TODO: Add payment processing
        Log::debug('Payment canceled', ['payment' => $payment->toArray()]);
    }

}
