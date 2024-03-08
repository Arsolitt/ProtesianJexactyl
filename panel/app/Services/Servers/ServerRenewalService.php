<?php

namespace Jexactyl\Services\Servers;

use Illuminate\Support\Facades\Log;
use Jexactyl\Events\User\UpdateCredits;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Requests\Api\Client\ClientApiRequest;
use Jexactyl\Models\Server;
use Throwable;

class ServerRenewalService
{
    private SuspensionService $suspensionService;

    /**
     * ServerRenewalService constructor.
     */
    public function __construct(
        SuspensionService $suspensionService,
    ) {
        $this->suspensionService = $suspensionService;
    }

    /**
     * Renews a server.
     *
     * @throws DisplayException
     * @throws Throwable
     */
    public function handle(ClientApiRequest $request, Server $server): Server
    {
        $user = $request->user();
        $cost = $server->dailyPrice();

        if ($server->user->id !== $user->id) {
            throw new DisplayException('Ты не владелец сервера!');
        }

        if ($user->credits < $cost) {
            throw new DisplayException('У тебя недостаточно средств для разморозки сервера!');
        }

        try {
            UpdateCredits::dispatch($user, $server->hourlyPrice(), 'decrement');
            if ($server->status === 'suspended') {
                $this->suspensionService->toggle($server, SuspensionService::ACTION_UNSUSPEND);
            }
        } catch (DisplayException $ex) {
            Log::error($ex->getMessage());
            throw new DisplayException('Что-то пошло не так при разморозке сервера :(');
        }

        return $server->refresh();
    }
}
