<?php

namespace Jexactyl\Services\Store;

use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Exceptions\Repository\RecordNotFoundException;
use Jexactyl\Exceptions\Service\Deployment\NoViableAllocationException;
use Jexactyl\Exceptions\Service\Deployment\NoViableNodeException;
use Jexactyl\Http\Requests\Api\Client\Store\CreateServerRequest;
use Jexactyl\Models\Allocation;
use Jexactyl\Models\Egg;
use Jexactyl\Models\EggVariable;
use Jexactyl\Models\Nest;
use Jexactyl\Models\Node;
use Jexactyl\Models\Server;
use Jexactyl\Services\Servers\ServerCreationService;
use Throwable;

class StoreCreationService
{
    public function __construct(
        private SettingsRepositoryInterface       $settings,
        private readonly ServerCreationService    $creationService,
        private readonly StoreVerificationService $verifyService
    )
    {
    }

    /**
     * @throws NoViableNodeException
     * @throws Throwable
     * @throws RecordNotFoundException
     * @throws DisplayException
     * @throws NoViableAllocationException
     */
    public function handle(CreateServerRequest $request): Server
    {
        $egg = Egg::find($request->input('egg'));
        $nest = Nest::find($request->input('nest'))->id;
        $node = Node::find($request->input('node'))->id;

        $this->verifyService->handle($request);

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'owner_id' => $request->user()->id,
            'egg_id' => $egg->id,
            'nest_id' => $nest,
            'node_id' => $node,
            'allocation_id' => $this->getAllocation($node),
            'allocation_limit' => $request->input('allocations'),
            'backup_limit' => $request->input('backups'),
            'database_limit' => $request->input('databases'),
            'environment' => [],
            'memory' => $request->input('memory'),
            'disk' => $request->input('disk'),
            'cpu' => $request->input('cpu'),
            'swap' => 0,
            'io' => 500,
            'image' => array_values($egg->docker_images)[0],
            'startup' => $egg->startup,
            'start_on_completion' => false,
        ];


        foreach (EggVariable::where('egg_id', $egg->id)->get() as $var) {
            $key = "v1-{$egg->id}-{$var->env_variable}";
            $data['environment'][$var->env_variable] = $request->get($key, $var->default_value);
        }

        if ($egg->id === 16) {
            if ($request->input('project_id')) {
                $data['environment']['PROJECT_ID'] = (string) $request->input('project_id');
            }

            if ($request->input('version_id')) {
                $data['environment']['VERSION_ID'] = (string) $request->input('version_id');
            }
        }

        return $this->creationService->handle($data);
    }

    /**
     * Gets an allocation for server deployment.
     *
     * @throws NoViableAllocationException
     */
    protected function getAllocation(int $node): int
    {
        $allocation = Allocation::where('node_id', $node)->where('server_id', null)->first();

        if (!$allocation) {
            throw new NoViableAllocationException('На ноде нет свободного места для создания сервера, попробуй позже или напиши в поддержку!');
        }

        return $allocation->id;
    }
}
