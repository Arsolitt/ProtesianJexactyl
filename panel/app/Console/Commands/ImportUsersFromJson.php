<?php

namespace Jexactyl\Console\Commands;

use Illuminate\Console\Command;
use Jexactyl\Events\Store\PaymentPaid;
use Jexactyl\Events\User\UpdateCredits;
use Jexactyl\Models\Payment;
use Jexactyl\Models\ReferralUses;
use Jexactyl\Models\User;

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
        $filename = storage_path('import/users.json');

        $fileContents = file_get_contents($filename);
        $importedUsers = json_decode($fileContents, true);

        foreach ($importedUsers as $importedUser) {
            try {


                $this->line('Processing: ' . $importedUser['email']);
                $user = User::where('email', '=', $importedUser['email'])->first();
                if (!$user) {
                    continue;
                }
                UpdateCredits::dispatch($user, $importedUser['credits'], 'set');
                $user->referralCodes()->create(['user_id' => $user->id, 'code' => $importedUser['referral_code']]);

                foreach ($importedUser['referrals'] as $email) {
                    $referral = User::where('email', '=', $email)->first();
                    if (!$referral) {
                        continue;
                    }
                    ReferralUses::create([
                        'user_id' => $referral->id,
                        'referrer_id' => $user->id,
                        'code_used' => $importedUser['referral_code'],
                    ]);
                    $referral->update(['referral_code' => $importedUser['referral_code']]);
                }
            } catch (\Exception $e) {
                $this->line($e->getMessage());
            }
        }

        foreach ($importedUsers as $importedUser) {
            $this->line('Processing: '.$importedUser['email']);
            $user = User::where('email', '=', $importedUser['email'])->first();
            if (!$user) {
                continue;
            }


            foreach ($importedUser['payments'] as $paymentData) {
                try {
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'amount' => $paymentData['amount'],
                    'external_id' => $paymentData['payment_id'],
                    'status' => Payment::STATUS_OPEN,
                    'currency' => 'RUB',
                    'gateway' => 'yookassa',
                    'url' => 'imported',
                ]);
                PaymentPaid::dispatch($payment);
                } catch (\Exception $e) {
                    $this->line($e->getMessage());
                }
            }
        }

        $this->info('Data has been imported from users.json');
    }
}
