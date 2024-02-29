<?php

namespace Jexactyl\Services\Store;

use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Requests\Api\Client\Store\CreateServerRequest;

class StoreVerificationService
{
    public function __construct(private SettingsRepositoryInterface $settings)
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
        $prefixMin = 'store:limit:min:';
        $prefixMax = 'store:limit:max:';
        $types = ['memory', 'disk', 'slot', 'allocation', 'backup', 'database'];

        foreach ($types as $type) {
            $suffix = '';
            $limitMin = $this->settings->get($prefixMin . $type);
            $limitMax = $this->settings->get($prefixMax . $type);

            if (in_array($type, ['slot', 'allocation', 'backup', 'database'])) {
                $suffix = 's';
            }

            $amount = $request->input($type .= $suffix);

            if ($limitMin > $amount) {
                throw new DisplayException('You cannot deploy with ' . $amount . ' ' . $type . ', as an admin has set a limit of ' . $limitMin);
            }

            if ($limitMax < $amount) {
                throw new DisplayException('You cannot deploy with ' . $amount . ' ' . $type . ', as an admin has set a limit of ' . $limitMax);
            }
        }
    }
}
