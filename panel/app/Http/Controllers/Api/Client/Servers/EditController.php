<?php

namespace Jexactyl\Http\Controllers\Api\Client\Servers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Controllers\Api\Client\ClientApiController;
use Jexactyl\Http\Requests\Api\Client\Servers\EditServerRequest;
use Jexactyl\Models\Server;
use Jexactyl\Services\Servers\ServerEditService;

class EditController extends ClientApiController
{
    /**
     * PowerController constructor.
     */
    public function __construct(private ServerEditService $editService)
    {
        parent::__construct();
    }

    /**
     * Edit a server's resource limits.
     *
     * @throws DisplayException
     */
    public function index(EditServerRequest $request, Server $server): JsonResponse
    {
        throw new DisplayException('Тесты');
        if ($this->settings->get('server:editing') != 'true') {
            throw new DisplayException('Редактирование серверов отключено админом!');
        }

        if ($request->user()->id != $server->owner_id) {
            throw new DisplayException('Это не твой сервер, поэтому ты не можешь редактировать его!');
        }

        $this->editService->handle($request, $server);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
