<?php

namespace Jexactyl\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Jexactyl\Console\Commands\Maintenance\CleanServiceBackupFilesCommand;
use Jexactyl\Console\Commands\Maintenance\PruneOrphanedBackupsCommand;
use Jexactyl\Console\Commands\Schedule\AnalyticsCollectionCommand;
use Jexactyl\Console\Commands\Schedule\AnalyticsReviewCommand;
use Jexactyl\Console\Commands\Schedule\ChargeCreditsCommand;
use Jexactyl\Console\Commands\Schedule\DeleteCanceledPayments;
use Jexactyl\Console\Commands\Schedule\DeleteOpenPayments;
use Jexactyl\Console\Commands\Schedule\DeleteSuspendedServers;
use Jexactyl\Console\Commands\Schedule\ProcessRunnableCommand;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        # aboba
    }

    // Refer to: https://github.com/illuminate/console/blob/master/Scheduling/ManagesFrequencies.php
    // for time frequencies in terms of running commands, e.g. |->everyThirtyMinutes();|

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Execute scheduled commands for servers every minute, as if there was a normal cron running.
        $schedule->command(ProcessRunnableCommand::class)->everyMinute()->withoutOverlapping();
        $schedule->command(CleanServiceBackupFilesCommand::class)->daily();

        if (config('backups.prune_age')) {
            // Every 30 minutes, run the backup pruning command so that any abandoned backups can be deleted.
            $schedule->command(PruneOrphanedBackupsCommand::class)->everyThirtyMinutes();
        }

        // Run analysis commands to collect and process data.
        $schedule->command(AnalyticsCollectionCommand::class)->everyFifteenMinutes();
        $schedule->command(AnalyticsReviewCommand::class)->everyThreeHours();

        $schedule->command(ChargeCreditsCommand::class)->hourly();
        $schedule->command(DeleteOpenPayments::class)->hourly();
        $schedule->command(DeleteCanceledPayments::class)->daily();
        $schedule->command(DeleteSuspendedServers::class)->hourly();
    }
}
