<?php

namespace Jexactyl\Services\Store;

use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Exceptions\Model\DataValidationException;
use Jexactyl\Http\Requests\Api\Client\Store\PaymentRequest;
use Jexactyl\Models\Payment;
use Jexactyl\Repositories\Eloquent\PaymentRepository;
use Jexactyl\Services\Store\Gateways\YookassaService;
use Symfony\Component\Uid\Ulid;

class PaymentCreationService
{
    public function __construct(
        private SettingsRepositoryInterface $settings,
        private PaymentRepository           $repository,
        private YookassaService             $yookassa
    )
    {

    }

    /**
     * @throws DataValidationException
     */
    public function handle(PaymentRequest $request): Payment
    {
        $data = [
            'id' => Ulid::generate(),
            'user_id' => $request->user()->id,
            'status' => Payment::STATUS_OPEN,
            'amount' => $request->input('amount'),
            'currency' => $this->settings->get('store:currency'),
            'gateway' => $request->input('gateway'),
        ];

        return $this->createModel($this->processGateway($data));

    }

    /**
     * @throws DataValidationException
     */
    private function createModel(array $data): Payment
    {
        $payment = $this->repository->create(array_except($data, ['url']));
        $payment->url = $data['url'];
        return $payment;
    }

    private function processGateway(array $data): array
    {
        return match ($data['gateway']) {
            'yookassa' => $this->yookassa->handle($data),
            default => '',
        };
    }

}
