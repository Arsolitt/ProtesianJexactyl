<?php

namespace Jexactyl\Http\Controllers\Api\Webhook;

use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Http\Requests\Api\Notification\YookassaWebhookRequest;
use Jexactyl\Services\Store\Gateways\YookassaWebhookService;

class PaymentWebhookController extends Controller
{
    public function __construct(private YookassaWebhookService $yookassa)
    {

    }

    public function yookassa(YookassaWebhookRequest $request)
    {
//        $IPWhitelist = [
//            "185.71.76.0/27",
//            "185.71.77.0/27",
//            "77.75.153.0/25",
//            "77.75.156.11",
//            "77.75.156.35",
//            "77.75.154.128/25",
//            "2a02:5180::/32",
//            "77.75.154.206",
//            "127.0.0.1",
//            "77.75.153.78",
//        ];
//        if (!in_array($request->header('CF-Connecting-IP'), $IPWhitelist)) {
//            Log::error('IP ' . $request->header('CF-Connecting-IP') . ' not in whitelist');
//            return response('IP is not in whitelist', 403);
//        }
        return $this->yookassa->handle($request);
    }
}
