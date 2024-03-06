<?php

namespace Jexactyl\Services\Store;

use Jexactyl\Events\Store\PaymentCanceled;
use Jexactyl\Events\Store\PaymentPaid;
use Jexactyl\Events\User\UpdateCredits;
use Jexactyl\Models\Payment;

class PaymentProcessingService
{

    public function paid(Payment $payment): void
    {
        PaymentPaid::dispatch($payment);
        UpdateCredits::dispatch($payment->user, $payment->amount, 'increment');
    }

    public function canceled(Payment $payment): void
    {
        PaymentCanceled::dispatch($payment);
    }

}
