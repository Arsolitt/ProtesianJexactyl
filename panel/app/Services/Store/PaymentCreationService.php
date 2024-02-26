<?php

namespace Jexactyl\Services\Store;

use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Contracts\Store\PaymentGatewayInterface;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Exceptions\Model\DataValidationException;
use Jexactyl\Http\Requests\Api\Client\Store\PaymentRequest;
use Jexactyl\Models\Payment;
use Jexactyl\Repositories\Eloquent\PaymentRepository;
use Jexactyl\Services\Store\Gateways\YookassaService;

class PaymentCreationService
{

    private Payment $payment;

    private PaymentGatewayInterface $gaService;

    public function __construct(
        private SettingsRepositoryInterface $settings,
        private PaymentRepository           $repository
    )
    {

    }

    /**
     * @throws DataValidationException
     * @throws DisplayException
     */
    public function handle(PaymentRequest $request): Payment
    {
        $data = [
            'user_id' => $request->user()->id,
            'status' => Payment::STATUS_OPEN,
            'amount' => $request->input('amount'),
            'currency' => $this->settings->get('store:currency'),
            'gateway' => $request->input('gateway'),
        ];

        return $this->createModel($data)->bindGateway()->processPayment()->payment;
    }

    /**
     * @throws DataValidationException
     */
    private function createModel(array $data): self
    {
        $this->payment = $this->repository->create($data);
        return $this;
    }

    /**
     * @throws DisplayException
     */
    private function bindGateway(): self
    {
        $this->gaService = match ($this->payment->gateway) {
            'yookassa' => new YookassaService(),
            default => $this->paymentFailed('Неизвестный платёжный шлюз: ' . $this->payment->gateway),
        };
        return $this;
    }

    private function processPayment(): self
    {
        $this->payment = $this->gaService->handle($this->payment);
        $this->payment->save();
        return $this;
    }

    /**
     * @throws DisplayException
     */
    private function paymentFailed(string $msg = 'Ошибка при проведении платежа'): self
    {
        // TODO: monthly delete all canceled payments
        $this->payment->update(['status' => Payment::STATUS_CANCELED]);
        throw new DisplayException($msg);
    }

}
