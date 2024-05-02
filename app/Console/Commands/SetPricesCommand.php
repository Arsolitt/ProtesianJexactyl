<?php

namespace Jexactyl\Console\Commands;

use Illuminate\Console\Command;
use Jexactyl\Models\Server;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SetPricesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $servers = Server::all();
        foreach ($servers as $server) {
            try {
                $server->update([
                    'monthly_price' => $server->actualPrice(),
                ]);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        };
        return CommandAlias::SUCCESS;
    }
}
