<?php

namespace Jexactyl\Services\Store\Gateways;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jexactyl\Contracts\Store\PaymentGatewayInterface;
use Jexactyl\Http\Requests\Api\Webhook\WebhookRequest;
use Jexactyl\Models\Payment;
use Jexactyl\Services\Store\PaymentProcessingService;

class YookassaService implements PaymentGatewayInterface
{

    private string $shopID;

    private string $secretKey;

    public string $apiURL = 'https://api.yookassa.ru/v3/payments/';

    private Payment $payment;

    private array $invoice;

    private PaymentProcessingService $processingService;

    public function __construct()
    {
        $this->shopID = config('store.gateways.yookassa.shopID');
        $this->secretKey = config('store.gateways.yookassa.secretKey');
        $this->processingService = new PaymentProcessingService;
    }

    public function handle(Payment $payment): Payment
    {
        $this->payment = $payment;
        return $this->createInvoice()->createPayment()->payment;
    }

    private function createInvoice(): YookassaService
    {
        $this->invoice = [
            'amount' => [
                'value' => $this->payment->amount,
                'currency' => $this->payment->currency,
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => route('index'),
            ],
            'capture' => true,
            'description' => 'Пополнение баланса ProtesiaN Host',
            "metadata" => [
                'user_id' => $this->payment->user_id,
                'internal_payment_id' => $this->payment->id,
            ],
            "receipt" => [
                "customer" => [
                    "full_name" => $this->payment->user->name,
                    "email" => $this->payment->user->email,
                ],
                "items" => [
                    [
                        "description" => "Пополнение баланса ProtesiaN Host",
                        "quantity" => "1.00",
                        "amount" => [
                            "value" => $this->payment->amount,
                            "currency" => $this->payment->currency,
                        ],
                        "vat_code" => 1,
                        "payment_mode" => "full_payment",
                        "payment_subject" => "service",
                    ]
                ]
            ],
        ];
        return $this;
    }

    private function createPayment(): YookassaService
    {
        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "Idempotence-Key" => $this->payment->id,
            $this->shopID => $this->secretKey,
        ])->withBasicAuth($this->shopID, $this->secretKey)->post($this->apiURL, $this->invoice);

        $this->payment->url = $response['confirmation']['confirmation_url'];
        $this->payment->external_id = $response['id'];

        return $this;
    }

    public function webhook(WebhookRequest $request): Response
    {
        if (!$this->checkWebhookSource($request)) {
            return response('IP is not in whitelist', 403);
        }

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

    private function checkWebhookSource(WebhookRequest $request): bool
    {
        $whitelist = $this->getIPRange();

//        if (!in_array($request->header('CF-Connecting-IP'), $whitelist)) {
//            Log::error('IP ' . $request->header('CF-Connecting-IP') . ' not in whitelist');
//            return false;
//        }

        if (!in_array($request->ip(), $whitelist)) {
            Log::error('IP ' . $request->ip() . ' not in whitelist');
            return false;
        }
        return true;
    }

    private function getIPRange(): array
    {
        $IPWhitelist = [
            "185.71.76.0/27",
            "185.71.77.0/27",
            "77.75.153.0/25",
            "77.75.156.11",
            "77.75.156.35",
            "77.75.154.128/25",
            "77.75.154.206",
            "127.0.0.1",
            "77.75.153.78",
            "10.147.19.237",
        ];
        $expandedIPs = [];
        foreach ($IPWhitelist as $item) {
            if (str_contains($item, '/')) {
                list($ip, $cidr) = explode('/', $item);
                $start = ip2long($ip);
                $end = $start + pow(2, (32 - $cidr)) - 1;
                for ($i = $start; $i <= $end; $i++) {
                    $expandedIPs[] = long2ip($i);
                }
            } else {
                $expandedIPs[] = $item;
            }
        }
        return $expandedIPs;
    }
}
