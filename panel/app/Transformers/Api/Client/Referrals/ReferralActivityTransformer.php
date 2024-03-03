<?php

namespace Jexactyl\Transformers\Api\Client\Referrals;

use Jexactyl\Models\ReferralUses;
use Jexactyl\Models\User;
use Jexactyl\Transformers\Api\Client\BaseClientTransformer;

class ReferralActivityTransformer extends BaseClientTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getResourceName(): string
    {
        return ReferralUses::RESOURCE_NAME;
    }

    /**
     * Transform this model into a representation that can be consumed by a client.
     *
     * @return array
     */
    public function transform(ReferralUses $model)
    {
        $user = User::where('id', $model->user_id)->first();
        return [
            'code' => $model->code_used,
            'user_id' => $model->user_id,
            'created_at' => $model->created_at->toIso8601String(),
            'user_email' => $user ? $user->username : 'Неизвестно',
        ];
    }
}
