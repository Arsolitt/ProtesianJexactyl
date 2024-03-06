<?php

namespace Jexactyl\Console\Commands\Schedule;

use Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Jexactyl\Models\Payment;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DeleteOpenPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:open:clear';

    public function __construct()
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
    public function handle(): int
    {
        try {
            Payment::where('status', '=', 'open')->where('updated_at', '<', Carbon::now()->subHours(2))->delete();
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return CommandAlias::FAILURE;
        }
        return CommandAlias::SUCCESS;
    }
}
