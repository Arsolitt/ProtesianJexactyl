<?php

namespace Jexactyl\Services\Referrals;

use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Requests\Api\Client\ClientApiRequest;
use Jexactyl\Models\ReferralCode;
use Jexactyl\Models\ReferralUses;
use Jexactyl\Models\User;

class UseReferralService
{
    public function __construct(private SettingsRepositoryInterface $settings)
    {
    }

    /**
     * Process to handle a user using a referral code on
     * their account.
     *
     * @throws DisplayException
     */
    public function handle(ClientApiRequest $request): void
    {
        // TODO: переделать реферальную систему
        throw new DisplayException('Тебе сюда не надо!');
        $user = $request->user();
        $code = $request->input('code');
        $reward = $this->settings->get('referrals:reward', 0);

        $id = ReferralCode::where('code', $code)->first()->user_id;
        $referrer = User::where('id', $id)->first();

        if ($id == $user->id) {
            throw new DisplayException('Ты не можешь использовать свой реферальный код!');
        }

        $user->update([
            'referral_code' => $code,
            'store_balance' => $request->user()->store_balance + $reward,
        ]);

        $referrer->update(['store_balance' => $referrer->store_balance + $reward]);

        ReferralUses::create([
            'user_id' => $user->id,
            'code_used' => $code,
            'referrer_id' => $id,
        ]);
    }
}
