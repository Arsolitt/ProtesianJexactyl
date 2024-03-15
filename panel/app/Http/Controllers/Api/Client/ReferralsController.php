<?php

namespace Jexactyl\Http\Controllers\Api\Client;

use Illuminate\Http\JsonResponse;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Requests\Api\Client\ClientApiRequest;
use Jexactyl\Models\ReferralPayment;
use Jexactyl\Models\ReferralUses;
use Jexactyl\Transformers\Api\Client\Referrals\ReferralActivityTransformer;
use Jexactyl\Transformers\Api\Client\Referrals\ReferralCodeTransformer;

class ReferralsController extends ClientApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns all of the referral codes that exist for the given client.
     */
    public function index(ClientApiRequest $request): array
    {
        return $this->fractal->collection($request->user()->referralCodes)
            ->transformWith($this->getTransformer(ReferralCodeTransformer::class))
            ->toArray();
    }

    /**
     * Returns all of the referral code uses.
     */
    public function activity(ClientApiRequest $request): array
    {
        $activity = ReferralUses::where('referrer_id', $request->user()->id)->limit(20)->orderBy('created_at', 'desc')->get();

        return $this->fractal->collection($activity)
            ->transformWith($this->getTransformer(ReferralActivityTransformer::class))
            ->toArray();
    }

    /**
     * Store a new referral code for a user's account.
     *
     * @throws DisplayException
     */
    public function store(ClientApiRequest $request): array
    {
        if ($request->user()->referralCodes->count() >= 5) {
            throw new DisplayException('У тебя не может быть больше 5 реферальных кодов!');
        }

        $code = $request->user()->referralCodes()->create([
            'user_id' => $request->user()->id,
            'code' => $this->generate(),
        ]);

        return $this->fractal->item($code)
            ->transformWith($this->getTransformer(ReferralCodeTransformer::class))
            ->toArray();
    }

    /**
     * Deletes a referral code.
     */
    public function delete(ClientApiRequest $request, string $code): JsonResponse
    {
        /** @var \Jexactyl\Models\ReferralCode $code */
        $referralCode = $request->user()->referralCodes()
            ->where('code', $code)
            ->firstOrFail();

        $referralCode->delete();

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * Returns a string used for creating
     * referral codes for the Panel.
     */
    public function generate(): string
    {
        $chars = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle($chars), 0, 16);
    }

    public function stats(ClientApiRequest $request)
    {
        $user = $request->user();
        $codes = $user->referralCodes()->get();
        $amount = 0;
        $referrals = ReferralUses::where('referrer_id', $user->id)->count();
        foreach ($codes as $code) {
            $amount += ReferralPayment::where('referral_code', $code->code)->sum('reward_amount');
        }

        return [
          'total_revenue' => $amount,
          'total_referrals' => $referrals
        ];
    }
}
