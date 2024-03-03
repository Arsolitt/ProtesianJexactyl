<?php

namespace Jexactyl\Listeners\Referral;

use Illuminate\Contracts\Queue\ShouldQueue;
use Jexactyl\Events\User\RegisteredWithReferrer;
use Jexactyl\Models\ReferralCode;
use Jexactyl\Models\ReferralUses;

class RegisteredListener implements ShouldQueue
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
    public function handle(RegisteredWithReferrer $event): void
    {
        $user = $event->user;
        $referrerId = ReferralCode::where('code', $user->referral_code)->first()->user_id;

        $user->update([
            'referral_code' => $user->referral_code,
        ]);

        ReferralUses::create([
            'user_id' => $user->id,
            'code_used' => $user->referral_code,
            'referrer_id' => $referrerId,
        ]);
    }
}
