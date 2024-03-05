<?php

namespace Jexactyl\Console\Commands;

use Illuminate\Console\Command;

class ImportUsersFromJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-users-from-json';

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

        $fileContents = file_get_contents($filename);
        $users = json_decode($fileContents, true);

//        $usersArray = [];
//        foreach ($users as $userData) {
//            $referralCodes = $userData['referral_codes'] ?? [];
//            unset($userData['referral_codes']);
//
//            $user = $userData;
//            $user['referral_codes'] = $referralCodes;
//
//            $usersArray[] = $user;
//        }

//        \Log::debug($users);

        foreach ($users as $user) {
            $this->line($user['email']);
            $payments = $user['payments'] ?? [];
            foreach ($payments as $payment) {
                $this->line($payment['amount']);
            }
            $codes = $user['referral_codes'] ?? [];
            foreach ($codes as $code) {
                $this->line($code['code']);
            }
        }

        // Выполнить дальнейшую обработку данных массива $usersArray

        $this->info('The data has been read from users.json and saved to an array');
    }
}
