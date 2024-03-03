<?php

namespace Jexactyl\Http\Controllers\Api\Client\Servers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
        return $this->fractal->item($server)
            ->transformWith($this->getTransformer(ServerTransformer::class))
            ->addMeta([
                'is_server_owner' => $request->user()->id === $server->owner_id,
                'user_permissions' => $this->permissionsService->handle($server, $request->user()),
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
        // TODO: переделать удаление серверов
        $user = $request->user();

        if ($user->id != $server->owner_id) {
            throw new DisplayException('You are not authorized to perform this action.');
        }

        if ($this->settings->get('server:deletion') != 'true') {
            throw new DisplayException('This feature has been locked by administrators.');
        }

        try {
            $this->deletionService->returnResources(true)->handle($server);
        } catch (DisplayException $ex) {
            throw new DisplayException('Unable to delete the server from the system.');
        }

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
