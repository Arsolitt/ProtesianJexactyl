<?php

namespace Jexactyl\Console\Commands;

use Illuminate\Console\Command;
use Jexactyl\Models\Egg;

class InvalidateEggsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:invalidate-eggs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $paper = Egg::where('name', 'Paper')->first();
        $features = $paper->features;
        $features[] = 'spigot';
        $paper->update([
            'features' => $features
        ]);
    }
}
