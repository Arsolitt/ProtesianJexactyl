<?php

namespace Jexactyl\Console\Commands\Schedule;

use Exception;
use Illuminate\Console\Command;
use Jexactyl\Events\User\UpdateCredits;
use Jexactyl\Models\Server;
use Jexactyl\Repositories\Wings\DaemonServerRepository;
use Jexactyl\Services\Servers\SuspensionService;
use Log;
use Symfony\Component\Console\Command\Command as CommandAlias;


class ChargeCreditsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credits:charge';
    protected $suspensionService;

    public function __construct()
    {
        parent::__construct();
        $this->suspensionService = new SuspensionService(new DaemonServerRepository(app()));
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
    public function handle(): int
    {
        Server::whereNull('status')->chunk(10, function ($servers) {
           foreach ($servers as $server) {
               try {
                   $user = $server->user;
                   $price = $server->hourlyPrice();
                   if ($user->credits >= $price) {
                       UpdateCredits::dispatch($user, $price, 'decrement');
                   } else {
                       $this->suspensionService->toggle($server, SuspensionService::ACTION_SUSPEND);
                   }
               } catch (Exception $e) {
                   Log::error($e->getMessage());
               }
           }
        });

        return CommandAlias::SUCCESS;
    }
}
