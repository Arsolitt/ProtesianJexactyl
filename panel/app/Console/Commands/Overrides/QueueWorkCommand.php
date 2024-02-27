<?php

namespace Jexactyl\Console\Commands\Overrides;

use Illuminate\Queue\Console\WorkCommand;
use Symfony\Component\Console\Command\SignalableCommandInterface;

class QueueWorkCommand extends WorkCommand implements SignalableCommandInterface
{
    public function getSubscribedSignals(): array
    {
        return [SIGTERM];
    }

    public function handleSignal(int $signal): void
    {
        if (SIGTERM === $signal) {
            $this->call('queue:restart');
        }
    }
}
