<?php

namespace Jexactyl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralPayment extends Model
{
    protected $fillable = [
        'payment_id',
        'user_id',
        'referral_code',
        'payment_amount',
        'given_percent',
        'given_amount',
        'referral_code',
    ];

    public static array $validationRules = [
        'user_id' => 'required|exists:users,id',
        'referral_code' => 'required|string|exists:referral_codes,code',
        'payment_amount' => 'required|numeric',
        'given_percent' => 'required|numeric',
        'given_amount' => 'required|numeric',
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
