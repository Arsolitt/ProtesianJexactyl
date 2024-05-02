<?php

namespace Jexactyl\Console\Commands\Overrides;

use Illuminate\Console\Scheduling\ScheduleWorkCommand;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\SignalableCommandInterface;

class ScheduleWorkCommandExtend extends ScheduleWorkCommand implements SignalableCommandInterface
{
    public function getSubscribedSignals(): array
    {
        return [SIGTERM];
    }

    public function handleSignal(int $signal): void
    {
        Log::debug($signal);
        if (SIGTERM === $signal) {
            $this->call('schedule:interrupt');
        }
    }
}
