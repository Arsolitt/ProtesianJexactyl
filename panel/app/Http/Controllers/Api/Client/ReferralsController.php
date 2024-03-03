<?php

namespace Jexactyl\Http\Controllers\Api\Client;

use Illuminate\Http\JsonResponse;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Requests\Api\Client\ClientApiRequest;
use Jexactyl\Models\ReferralUses;
use Jexactyl\Services\Referrals\UseReferralService;
use Jexactyl\Transformers\Api\Client\Referrals\ReferralActivityTransformer;
use Jexactyl\Transformers\Api\Client\Referrals\ReferralCodeTransformer;

class ReferralsController extends ClientApiController
{
    public function __construct(private UseReferralService $useService)
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
        $activity = ReferralUses::where('referrer_id', $request->user()->id)->get();

        return $this->fractal->collection($activity)
            ->transformWith($this->getTransformer(ReferralActivityTransformer::class))
            ->toArray();
    }

    /**
     * Use a referral code.
     *
     * @throws DisplayException
     */
    // TODO: удалить
    public function use(ClientApiRequest $request): JsonResponse
    {
        throw new DisplayException('Тебе сюда не надо!');
        
        if ($request->user()->referral_code) {
            throw new DisplayException('You have already used a referral code.');
        }

        $this->useService->handle($request);

        return new JsonResponse([], JsonResponse::HTTP_NO_CONTENT);
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
}
