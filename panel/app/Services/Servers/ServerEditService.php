<?php

namespace Jexactyl\Services\Servers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Events\Store\ServerEdit;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Exceptions\Service\Deployment\NoViableAllocationException;
use Jexactyl\Exceptions\Service\Deployment\NoViableNodeException;
use Jexactyl\Http\Requests\Api\Client\Servers\EditServerRequest;
use Jexactyl\Models\Allocation;
use Jexactyl\Models\Node;
use Jexactyl\Models\Server;
use Jexactyl\Repositories\Eloquent\NodeRepository;
use Jexactyl\Services\Store\LimitsService;

class ServerEditService
{
    public function __construct(
        private SettingsRepositoryInterface $settings,
        private LimitsService               $limitsService,
        private NodeRepository              $nodeRepo
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
    protected function verify(array $newResources, Server $server): void
    {
        $currentResources = [
            'memory' => $server->memory,
            'disk' => $server->disk,
            'allocations' => $server->allocation_limit,
            'backups' => $server->backup_limit,
            'databases' => $server->database_limit,
        ];

        if ($newResources === $currentResources) {
            throw new DisplayException('Старые характеристики совпадают с новыми. Никаких изменений не требуется.');
        }

        $limits = $this->limitsService->getLimits();
        $difference = [];

        foreach ($newResources as $key => $value) {
            if ($limits['min'][$key] > $value) {
                throw new DisplayException('Значение ' . $key . ' не может быть меньше ' . $limits['min'][$key]);
            }
            if ($limits['max'][$key] < $value) {
                throw new DisplayException('Значение ' . $key . ' не может быть больше ' . $limits['max'][$key]);
            }

            $difference[$key] = max($value - $currentResources[$key], 0);
        }

        $allocations = Allocation::where('node_id', $server->node_id)->where('server_id', null)->count();

        if ($allocations < $difference['allocations']) {
            throw new NoViableAllocationException('На узле недостаточно портов для апгрейда! Попробуй позже или напиши в поддержку.');
        }

        $node = Node::findOrFail($server->node_id);
        if (!$this->nodeRepo->getNodeWithResourceUsage($node->id)->isViable($difference['memory'], $difference['disk'])) {
            throw new noViableNodeException('На узле недостаточно ресурсов для апгрейда! Попробуй позже или обратись в поддержку.');
        }

        $server->fill([
            'memory' => $newResources['memory'],
            'disk' => $newResources['disk'],
            'allocation_limit' => $newResources['allocations'],
            'backup_limit' => $newResources['backups'],
            'database_limit' => $newResources['databases'],
        ]);
        if ($server->user->credits < $server->actualPrice() / 30) {
            throw new noViableNodeException('У тебя недостаточно средств для апгрейда сервера! Пополни баланс или умерь аппетиты!');
        }
        $server->refresh();
    }
}
