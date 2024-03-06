<?php

namespace Jexactyl\Http\Controllers\Api\Webhook;

use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Http\Requests\Api\Webhook\WebhookRequest;
use Jexactyl\Services\Store\Gateways\YookassaService;

class WebhookController extends Controller
{
    public function __construct(private YookassaService $yookassa)
    {

    }

    public function payment(WebhookRequest $request, string $gateway)
    {
        return match ($gateway) {
            'yookassa' => $this->yookassa->webhook($request),
            default => response('Unknown gateway', 404)
        };
    }
}
