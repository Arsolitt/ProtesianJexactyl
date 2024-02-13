<?php

namespace Jexactyl\Listeners\Server;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Jexactyl\Events\Server\Creating;
use Jexactyl\Models\Server;

class CreatingListener
{


    protected Server $server;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Creating $event): bool
    {
        $this->server = $event->server;
        $this->server->monthly_price = $this->actualPrice();
        return true;
    }

    protected function actualPrice(): float
    {
        $discount = 1 - ($this->server->user->totalDiscount() / 100);
        $cpu = $this->server->cpu * settings()->get('store:cost:cpu');
        $ram = $this->server->memory * settings()->get('store:cost:ram');
        $disk = $this->server->disk * settings()->get('store:cost:disk');
        $ports = $this->server->allocation_limit * settings()->get('store:cost:port');
        $backups = $this->server->backup_limit * settings()->get('store:cost:backup');
        $databases = $this->server->database_limit * settings()->get('store:cost:database');
        return (float) ($cpu + $ram + $disk + $ports + $backups + $databases) * $discount;
    }
}
