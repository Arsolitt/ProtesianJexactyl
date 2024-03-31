<?php

namespace Jexactyl\Http\Controllers\Api\Client\Store;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Exceptions\Repository\RecordNotFoundException;
use Jexactyl\Exceptions\Service\Deployment\NoViableAllocationException;
use Jexactyl\Exceptions\Service\Deployment\NoViableNodeException;
use Jexactyl\Http\Controllers\Api\Client\ClientApiController;
use Jexactyl\Http\Requests\Api\Client\Store\CreateServerRequest;
use Jexactyl\Models\Egg;
use Jexactyl\Models\Nest;
use Jexactyl\Models\Node;
use Jexactyl\Repositories\Eloquent\NodeRepository;
use Jexactyl\Services\Store\StoreCreationService;
use Jexactyl\Transformers\Api\Client\Store\EggTransformer;
use Jexactyl\Transformers\Api\Client\Store\NestTransformer;
use Jexactyl\Transformers\Api\Client\Store\NodeTransformer;
use Throwable;

class ServerController extends ClientApiController
{

    /**
     * ServerController constructor.
     */
    public function __construct(private StoreCreationService $creationService, private NodeRepository $nodeRepo)
    {
        parent::__construct();
    }

    public function nodes(Request $request): array
    {
        $nodes = Node::where('deployable', true)->get();


        return $this->fractal->collection($nodes)
            ->transformWith($this->getTransformer(NodeTransformer::class))
            ->toArray();
    }

    /**
     * Get all available nests for server deployment.
     */
    public function nests(Request $request): array
    {
        $nests = Nest::where('private', false)->get();

        return $this->fractal->collection($nests)
            ->transformWith($this->getTransformer(NestTransformer::class))
            ->toArray();
    }

    /**
     * Get all available eggs for server deployment.
     */
    public function eggs(Request $request): array
    {
        $id = $request->input('id') ?? Nest::where('private', false)->first()->id;
        $eggs = Nest::query()->where('id', $id)->where('private', false)->first()->eggs()->where('private', '=', false)->get();

        return $this->fractal->collection($eggs)
            ->transformWith($this->getTransformer(EggTransformer::class))
            ->toArray();
    }

    /**
     * @throws RecordNotFoundException
     * @throws NoViableAllocationException
     * @throws NoViableNodeException
     * @throws Throwable
     * @throws DisplayException
     */
    public function store(CreateServerRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->verified) {
            throw new DisplayException('Создание сервера недоступно для непроверенных учетных записей!');
        }

        if (Nest::find($request->input('nest'))->private ?? true) {
            throw new DisplayException('Выбранный раздел является приватным и размещение невозможно!');
        }

        if (Egg::find($request->input('egg'))->private ?? true) {
            throw new DisplayException('Выбранный образ является приватным и размещение невозможно!');
        }

        if ($user->server_slots < 1) {
            throw new DisplayException('У тебя недостаточно слотов для создания сервера.');
        }

        $node_id = $this->getPreferredNode($request->input('memory'), $request->input('disk'));

        $request->merge([
            'node' => $node_id,
            'cpu' => 500
        ]);

        $server = $this->creationService->handle($request);

        return new JsonResponse(['id' => $server->uuidShort]);
    }

    /**
     * @throws NoViableNodeException
     */
    protected function getPreferredNode(int $memory, int $disk): int|DisplayException
    {
        $availableNodes = [];
        $nodes = Node::where('deployable', true)->get();
        foreach ($nodes as $node) {
            $stats = $this->nodeRepo->getUsageStats($node);
            if ($this->nodeRepo->getNodeWithResourceUsage($node->id)->isViable($memory, $disk)) {
                $availableNodes[] = [
                    'id' => $node->id,
                    'percent' => $stats['memory']['percent']
                ];
            }
        }

        if (count($availableNodes) < 1) {
            throw new NoViableNodeException('Нет доступных узлов для создания, обратитесь в поддержку!');
        }
        usort($availableNodes, function ($a, $b) {
            return $a['percent'] - $b['percent'];
        });
        return $availableNodes[0]['id'];
    }
}
