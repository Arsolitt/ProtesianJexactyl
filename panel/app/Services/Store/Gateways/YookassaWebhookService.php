<?php

namespace Jexactyl\Services\Store\Gateways;

use Illuminate\Http\Response;
use Jexactyl\Http\Requests\Api\Notification\YookassaWebhookRequest;
use Jexactyl\Models\Payment;
use Jexactyl\Services\Store\PaymentProcessingService;

class YookassaWebhookService
{
    public function __construct(
        private readonly PaymentProcessingService $processingService
    )
    {

    }

    public function handle(YookassaWebhookRequest $request): Response
    {
        $data = $request->all()['object']['metadata'];
        $event = $request->all()['event'];
        $payment = Payment::findOrFail($data['internal_payment_id']);

        if ($payment->status === 'paid') {
            return response('Successfully', 200);
        }

        match ($event) {
            'payment.succeeded' => $this->processingService->paid($payment),
            default => $this->processingService->canceled($payment)
        };

        return response('Successfully', 200);
    }
}
