<?php

namespace Jexactyl\Listeners\Referral;

use Illuminate\Contracts\Queue\ShouldQueue;
use Jexactyl\Events\Store\PaymentPaid;
use Jexactyl\Events\User\UpdateCredits;
use Jexactyl\Models\ReferralPayment;

class GiveRewardListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentPaid $event): void
    {
        if (!(settings()->get('referrals:enabled') === 'true')) {
            return;
        }
        $payment = $event->payment;
        $referrer = $payment->user->referrer();
        if (!$referrer) {
            return;
        }
        $amount = $payment->amount;
        $percent = $referrer->referralReward();
        $reward = $amount * $percent / 100;
        ReferralPayment::create([
            'payment_id' => $payment->id,
            'user_id' => $payment->user->id,
            'referral_code' => $payment->user->referral_code,
            'payment_amount' => $amount,
            'reward_percent' => $percent,
            'reward_amount' => $reward,
        ]);
        UpdateCredits::dispatch($referrer, $reward, 'increment');
    }
}
