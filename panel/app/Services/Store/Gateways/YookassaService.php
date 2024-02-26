<?php

namespace Jexactyl\Services\Store\Gateways;

use Illuminate\Support\Facades\Http;
use Jexactyl\Contracts\Store\PaymentGatewayInterface;
use Jexactyl\Models\Payment;

class YookassaService implements PaymentGatewayInterface
{

    private string $shopID;

    private string $secretKey;

    public string $apiURL = 'https://api.yookassa.ru/v3/payments/';

    private Payment $payment;

    private array $invoice;

    public function __construct()
    {
        $this->shopID = config('store.gateways.yookassa.shopID');
        $this->secretKey = config('store.gateways.yookassa.secretKey');
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
}
