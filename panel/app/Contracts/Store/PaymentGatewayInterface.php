<?php

namespace Jexactyl\Contracts\Store;

use Illuminate\Http\Response;
use Jexactyl\Http\Requests\Api\Webhook\WebhookRequest;
use Jexactyl\Models\Payment;

interface PaymentGatewayInterface
{
    function handle(Payment $payment): Payment;

    public function webhook(WebhookRequest $request): Response;
}
