<?php

namespace Jexactyl\Services\Store;

use Jexactyl\Models\Node;
use Illuminate\Support\Facades\DB;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
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
        $discount = 1 - ($request->user->totalDiscount() / 100);
        $cpu = $request->input('cpu') * settings()->get('store:cost:cpu');
        $ram = $request->input('memory') * settings()->get('store:cost:ram');
        $disk = $request->input('disk') * settings()->get('store:cost:disk');
        $ports = $request->input('ports') * settings()->get('store:cost:port');
        $backups = $request->input('backups') * settings()->get('store:cost:backup');
        $databases = $request->input('databases') * settings()->get('store:cost:database');
        $price = ($cpu + $ram + $disk + $ports + $backups + $databases) * $discount / 30;
        if ($request->user->credits < $price) {
            throw new DisplayException('У тебя на балансе недостаточно средств, чтобы создать сервер!');
        }
    }

    /**
     * @throws DisplayException
     */
    private function checkResourceLimits(CreateServerRequest $request): void
    {
        $prefix = 'store:limit:';
        $types = ['cpu', 'memory', 'disk', 'slot', 'port', 'backup', 'database'];

        foreach ($types as $type) {
            $suffix = '';
            $limit = $this->settings->get($prefix . $type);

            if (in_array($type, ['slot', 'port', 'backup', 'database'])) {
                $suffix = 's';
            }

            $amount = $request->input($type .= $suffix);

            if ($limit < $amount) {
                throw new DisplayException('You cannot deploy with ' . $amount . ' ' . $type . ', as an admin has set a limit of ' . $limit);
            }
        }
    }
}
