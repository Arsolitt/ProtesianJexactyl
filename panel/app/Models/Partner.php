<?php

namespace Jexactyl\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partner extends Model
{
    use HasUlids;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $fillable = [
        'user_id',
        'partner_discount',
        'referral_discount',
        'referral_reward',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
