<?php

namespace Jexactyl\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralPayment extends Model
{
    use HasUlids;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $fillable = [
        'payment_id',
        'user_id',
        'referral_code',
        'payment_amount',
        'reward_percent',
        'reward_amount',
    ];

    public static array $validationRules = [
        'user_id' => 'required|exists:users,id',
        'referral_code' => 'required|string|exists:referral_codes,code',
        'payment_amount' => 'required|numeric',
        'reward_percent' => 'required|numeric',
        'reward_amount' => 'required|numeric',
        'payment_id' => 'required|string|exists:payments,id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id', 'users');
    }

    public function referrer(): User
    {
        return User::findOrFail(ReferralUses::where('code_used', '=', $this->referral_code)->first()->referrer_id);
    }
}
