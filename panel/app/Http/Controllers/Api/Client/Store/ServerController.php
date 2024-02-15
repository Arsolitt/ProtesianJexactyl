<?php

namespace Jexactyl\Http\Controllers\Api\Client\Store;

use Jexactyl\Models\Egg;
use Jexactyl\Models\Nest;
use Jexactyl\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Repositories\Eloquent\NodeRepository;
use Jexactyl\Services\Store\StoreCreationService;
use Jexactyl\Transformers\Api\Client\Store\EggTransformer;
use Jexactyl\Transformers\Api\Client\Store\NestTransformer;
use Jexactyl\Transformers\Api\Client\Store\NodeTransformer;
use Jexactyl\Http\Controllers\Api\Client\ClientApiController;
use Jexactyl\Http\Requests\Api\Client\Store\CreateServerRequest;
use Jexactyl\Exceptions\Service\Deployment\NoViableNodeException;

class ServerController extends ClientApiController
{
    /**
     * ServerController constructor.
     */
    public function __construct(private StoreCreationService $creationService)
    {
        parent::__construct();
    }

    public function nodes(Request $request): array
    {
        $nodes = Node::where('deployable', true)->get();

        foreach ($nodes as $node) {
            \Log::debug($node->isViable(1000, 1000));
        }

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

    // TODO: добавить сортировку для яичек, чтобы отключать каждое по отдельности
    public function eggs(Request $request): array
    {
        $id = $request->input('id') ?? Nest::where('private', false)->first()->id;
        $eggs = Nest::query()->where('id', $id)->where('private', false)->first()->eggs;

        return $this->fractal->collection($eggs)
            ->transformWith($this->getTransformer(EggTransformer::class))
            ->toArray();
    }

    /**
     * Stores a new server on the Panel.
     * @throws DisplayException
     */
    public function store(CreateServerRequest $request, NodeRepository $repository): JsonResponse
    {
        $user = $request->user();

        if (!$user->verified) {
            throw new DisplayException('Создание сервера недоступно для непроверенных учетных записей!');
        }

        if (Nest::find($request->input('nest'))->private) {
            throw new DisplayException('Выбранный раздел является приватным и размещение невозможно!');
        }

//        if (Egg::find($request->input('egg'))->private) {
//            throw new DisplayException('Выбранный раздел является приватным и размещение невозможно!');
//        }

        if ($user->server_slots < 1) {
            throw new DisplayException('У вас недостаточно слотов для создания сервера.');
        }

        $server = $this->creationService->handle($request);

        return new JsonResponse(['id' => $server->uuidShort]);
    }

    protected function getPreferredNode()
    {

    }
}
