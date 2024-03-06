<?php

namespace Jexactyl\Console\Commands\Schedule;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Jexactyl\Models\Server;
use Jexactyl\Services\Servers\ServerDeletionService;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DeleteSuspendedServers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'servers:suspended:clear';

    public function __construct(private ServerDeletionService $deletionService)
    {
        parent::__construct();
    }
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Server::where('status', Server::STATUS_SUSPENDED)
            ->where('delete_on_suspend', '=', 1)
            ->where('suspended_at', '<', Carbon::now()->subDays(30))
            ->chunk(10, function ($servers) {
            foreach ($servers as $server) {
                try {
                    $this->deletionService->handle($server);
                } catch (Exception $ex) {
                    Log::error($ex->getMessage());
                }
            }
        });
        return CommandAlias::SUCCESS;
    }
}
