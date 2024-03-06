<?php

namespace Jexactyl\Console\Commands\Schedule;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Jexactyl\Models\Payment;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DeleteCanceledPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:canceled:clear';

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
        try {
            Payment::where('status', '=', 'canceled')->where('updated_at', '<', Carbon::now()->subDays(7))->delete();
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return CommandAlias::FAILURE;
        }
        return CommandAlias::SUCCESS;
    }
}
