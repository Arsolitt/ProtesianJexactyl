<?php

namespace Jexactyl\Events\Store;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Jexactyl\Models\Server;

class ServerEdit
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public array $resources, public Server $server)
    {
        //
    }

}
