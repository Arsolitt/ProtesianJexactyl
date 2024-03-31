<?php

namespace Jexactyl\Http\Controllers\Api\Client;

use Illuminate\Http\JsonResponse;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Requests\Api\Client\ClientApiRequest;
use Jexactyl\Models\Ticket;
use Jexactyl\Models\TicketMessage;
use Jexactyl\Transformers\Api\Client\Tickets\TicketMessageTransformer;
use Jexactyl\Transformers\Api\Client\Tickets\TicketTransformer;

class TicketController extends ClientApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns all of the tickets assigned to a given client.
     */
    public function index(ClientApiRequest $request): array
    {
        return $this->fractal->collection($request->user()->tickets)
            ->transformWith($this->getTransformer(TicketTransformer::class))
            ->toArray();
    }

    /**
     * Views a specific ticket for a client.
     */
    public function view(ClientApiRequest $request, int $id): array
    {
        return $this->fractal->item($request->user()->tickets()->findOrFail($id))
            ->transformWith($this->getTransformer(TicketTransformer::class))
            ->toArray();
    }

    /**
     * Gets the messages associated with a ticket.
     */
    public function viewMessages(ClientApiRequest $request, int $id): array
    {
        if (!$request->user()->tickets()->where('id', $id)->exists()) {
            return [];
        }
        $messages = TicketMessage::where('ticket_id', $id)->get();

        return $this->fractal->collection($messages)
            ->transformWith($this->getTransformer(TicketMessageTransformer::class))
            ->toArray();
    }

    /**
     * Creates a new ticket on the Panel which is accessible by both
     * administrators and the specific client.
     *
     * @throws DisplayException
     */
    public function new(ClientApiRequest $request): JsonResponse
    {
        $user = $request->user()->id;
        $title = $request->input('title');
        $description = $request->input('description');
        $total = Ticket::where('client_id', $user)->count();

        if ($this->settings->get('tickets:max') <= $total) {
            throw new DisplayException('You already have ' . $total . ' tickets open.');
        }

        $model = Ticket::create([
            'client_id' => $user,
            'title' => $title,
            'status' => Ticket::STATUS_PENDING,
            'content' => $description,
        ]);

        TicketMessage::create([
            'user_id' => $user,
            'ticket_id' => $model->id,
            'content' => $description,
        ]);

        return new JsonResponse(['id' => $model->id]);
    }

    /**
     * Creates a new ticket message on the Panel which is accessible
     * by both administrators and the specific client.
     *
     * @throws DisplayException
     */
    public function newMessage(ClientApiRequest $request, int $id): JsonResponse
    {
        $ticket = $request->user()->tickets()->findOrFail($id);

        $ticket->messages()->create([
            'user_id' => $request->user()->id,
            'ticket_id' => $ticket->id,
            'content' => $request->input('description'),
        ]);

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Closes a ticket and deletes the associated messages.
     *
     * @throws DisplayException
     */
    public function close(ClientApiRequest $request, int $id): JsonResponse
    {
        if (!$request->user()->tickets()->where('id', $id)->exists()) {
            return new JsonResponse([], JsonResponse::HTTP_NOT_FOUND);
        }
        $request->user()->tickets()->findOrFail($id)->delete();
        TicketMessage::where('ticket_id', $id)->delete();

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }
}
