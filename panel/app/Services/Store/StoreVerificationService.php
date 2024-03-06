<?php

namespace Jexactyl\Services\Store;

use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Requests\Api\Client\Store\CreateServerRequest;

class StoreVerificationService
{
    public function __construct(
        private SettingsRepositoryInterface $settings,
        private LimitsService               $limitsService
    )
    {
    }

    /**
     * This service ensures that users cannot create servers, gift
     * resources or edit a servers resource limits if they do not
     * have sufficient resources in their account - or if the requested
     * amount goes over admin-defined limits.
     * @throws DisplayException
     */
    public function handle(CreateServerRequest $request): void
    {
        $this->checkUserCredits($request);
        $this->checkResourceLimits($request);
    }

    /**
     * @throws DisplayException
     */
    private function checkUserCredits(CreateServerRequest $request): void
    {
        $discount = 1 - ($request->user()->totalDiscount() / 100);
        $memory = $request->input('memory') * settings()->get('store:cost:memory');
        $disk = $request->input('disk') * settings()->get('store:cost:disk');
        $allocations = $request->input('ports') * settings()->get('store:cost:allocation');
        $backups = $request->input('backups') * settings()->get('store:cost:backup');
        $databases = $request->input('databases') * settings()->get('store:cost:database');
        $price = ($memory + $disk + $allocations + $backups + $databases) * $discount / 30;
        if ($request->user()->credits < $price) {
            throw new DisplayException('У тебя на балансе недостаточно средств, чтобы создать сервер!');
        }
    }

    /**
     * @throws DisplayException
     */
    private function checkResourceLimits(CreateServerRequest $request): void
    {
        if ($request->user()->server_slots <= 0) {
            throw new DisplayException('У тебя закончились слоты, чтобы создать сервер!');
        }

        $types = ['memory', 'disk', 'allocations', 'backups', 'databases'];

        $limits = $this->limitsService->getLimits();
        
        foreach ($types as $type) {
            $amount = $request->input($type);

            if ($limits['min'][$type] > $request->input($type)) {
                throw new DisplayException('You cannot deploy with ' . $amount . ' ' . $type . ', as an admin has set a limit of ' . $limitMin);
            }

            if ($limits['max'][$type] < $request->input($type)) {
                throw new DisplayException('You cannot deploy with ' . $amount . ' ' . $type . ', as an admin has set a limit of ' . $limitMax);
            }
        }
    }
}
