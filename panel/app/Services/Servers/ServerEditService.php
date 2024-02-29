<?php

namespace Jexactyl\Services\Servers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Events\Store\ServerEdit;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Requests\Api\Client\Servers\EditServerRequest;
use Jexactyl\Models\Server;
use Jexactyl\Models\User;
use Jexactyl\Services\Store\LimitsService;

class ServerEditService
{
    public function __construct(
        private SettingsRepositoryInterface $settings,
        private LimitsService               $limitsService
    )
    {
    }

    /**
     * Updates the requested instance with new limits.
     *
     * @throws DisplayException
     */
    public function handle(EditServerRequest $request, Server $server): JsonResponse
    {
        $user = $request->user();
        $resources = $request->input('resources');

        if ($user->id != $server->owner_id) {
            throw new DisplayException('Это не твой сервер, поэтому ты не можешь редактировать его!');
        }


        $this->verify($resources, $server);

        ServerEdit::dispatch($resources, $server);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Ensure that the server is not going past the limits
     * for minimum resources per-container.
     *
     * @throws DisplayException
     */
    protected function verify(array $newResources, Server $server)
    {
        $currentResources = [
            'memory' => $server->memory,
            'disk' => $server->disk,
            'allocations' => $server->allocation_limit,
            'backups' => $server->backup_limit,
            'databases' => $server->database_limit,
        ];

        if ($newResources === $currentResources) {
            throw new DisplayException('НИХУЯ НЕ ПОМЕНЯЛОСЬ');
        }

        $limits = $this->limitsService->getLimits();

        foreach ($newResources as $key => $value) {
            if ($limits['min'][$key] > $value) {
                throw new DisplayException('Значение ' . $key . ' не может быть меньше ' . $limits['min'][$key]);
            }
            if ($limits['max'][$key] < $value) {
                throw new DisplayException('Значение ' . $key . ' не может быть больше ' . $limits['max'][$key]);
            }

            $difference = $value - $currentResources[$key];
            if ($difference > 0) {
                throw new DisplayException('На узле недостаточно ресурсов для апгрейда! Попробуй позже или обратись в поддержку.');
                // TODO: проверка на доступность ресурсов
            }
        }
    }

    /**
     * Gets the minimum value for a specific resource.
     *
     * @throws DisplayException
     */
    protected function toMin(string $resource): int
    {
        return match ($resource) {
            'cpu' => 50,
            'allocation_limit' => 1,
            'disk', 'memory' => 1024,
            'backup_limit', 'database_limit' => 0,
            default => throw new DisplayException('Unable to parse resource type')
        };
    }

    /**
     * Get the requested resource type and transform it
     * so it can be used in a database statement.
     *
     * @throws DisplayException
     */
    protected function toUser(string $resource, User $user): int
    {
        return match ($resource) {
            'cpu' => $user->store_cpu,
            'disk' => $user->store_disk,
            'memory' => $user->store_memory,
            'backup_limit' => $user->store_backups,
            'allocation_limit' => $user->store_ports,
            'database_limit' => $user->store_databases,
            default => throw new DisplayException('Unable to parse resource type')
        };
    }

    /**
     * Get the requested resource type and transform it
     * so it can be used in a database statement.
     *
     * @throws DisplayException
     */
    protected function toServer(string $resource, Server $server): int
    {
        return match ($resource) {
            'cpu' => $server->cpu,
            'disk' => $server->disk,
            'memory' => $server->memory,
            'backup_limit' => $server->backup_limit,
            'database_limit' => $server->database_limit,
            'allocation_limit' => $server->allocation_limit,
            default => throw new DisplayException('Unable to parse resource type')
        };
    }
}
