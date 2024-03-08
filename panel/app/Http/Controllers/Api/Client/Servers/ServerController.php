<?php

namespace Jexactyl\Http\Controllers\Api\Client\Servers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Jexactyl\Events\User\LastActivity;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Controllers\Api\Client\ClientApiController;
use Jexactyl\Http\Requests\Api\Client\Servers\DeleteServerRequest;
use Jexactyl\Http\Requests\Api\Client\Servers\GetServerRequest;
use Jexactyl\Http\Requests\Api\Client\Servers\UpdateBackgroundRequest;
use Jexactyl\Models\Server;
use Jexactyl\Services\Servers\GetUserPermissionsService;
use Jexactyl\Services\Servers\ServerDeletionService;
use Jexactyl\Transformers\Api\Client\ServerTransformer;
use Throwable;

class ServerController extends ClientApiController
{
    /**
     * ServerController constructor.
     */
    public function __construct(private GetUserPermissionsService $permissionsService, private ServerDeletionService $deletionService)
    {
        parent::__construct();
    }

    /**
     * Transform an individual server into a response that can be consumed by a
     * client using the API.
     */
    public function index(GetServerRequest $request, Server $server): array
    {
        $user = $request->user();
        LastActivity::dispatch($user, $request->header('CF-Connecting-IP') ?? $request->ip());
        return $this->fractal->item($server)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->addMeta([
                'is_server_owner' => $user->id === $server->owner_id,
                'user_permissions' => $this->permissionsService->handle($server, $user),
            ])
            ->toArray();
    }

    /**
     * Updates the background image for a server.
     */
    public function updateBackground(UpdateBackgroundRequest $request, Server $server): JsonResponse
    {
        $url = $request->input('bg');
        $server->update(['bg' => $url]);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Deletes the requested server via the API and
     * returns the resources to the authenticated user.
     *
     * @throws DisplayException|Throwable
     */
    public function delete(DeleteServerRequest $request, Server $server): JsonResponse
    {
        $user = $request->user();

        if ($user->id != $server->owner_id) {
            throw new DisplayException('Ты не владелец сервера!');
        }

        if ($this->settings->get('server:deletion') != 'true') {
            throw new DisplayException('Админ отключил удаление серверов!');
        }

        try {
            $this->deletionService->returnResources(true)->handle($server);
        } catch (DisplayException $ex) {
            throw new DisplayException('Что-то пошло не так :(');
        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
