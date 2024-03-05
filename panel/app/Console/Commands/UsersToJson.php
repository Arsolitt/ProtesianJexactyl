<?php

namespace Jexactyl\Console\Commands;

use Illuminate\Console\Command;
use Jexactyl\Models\Payment;
use Jexactyl\Models\ReferralCode;
use Jexactyl\Models\User;

class UsersToJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:users-to-json';

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
        $filename = storage_path('export/users.json');
        touch($filename);
        $file = fopen($filename, 'w');
        $first = true;

        User::select(['id', 'email', 'credits'])->chunk(100, function ($users) use ($file, &$first) {
            foreach ($users as $user) {
                $data = $user->toArray();
                $this->line('Processing: ' . $user->email);
                $data['referral_codes'] = ReferralCode::where('user_id', '=', $user->id)->select(['code'])->get()->toArray();
                $data['payments'] = Payment::where('user_id', '=', $user->id)->where('status', '=', Payment::STATUS_PAID)->select(['amount', 'currency', 'gateway', 'external_id'])->get()->toArray();

                $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($first) {
                    fwrite($file, '[' . $json);
                    $first = false;
                } else {
                    fwrite($file, ',' . $json);
                }
            }
        });

        fwrite($file, ']');
        fclose($file);

        $this->info('The users have been saved to users.json');
    }
}
