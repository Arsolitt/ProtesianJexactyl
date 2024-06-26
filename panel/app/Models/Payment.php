<?php

namespace Jexactyl\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasUlids;

    public $incrementing = false;

    public const string STATUS_OPEN = 'open';
    public const string STATUS_PAID = 'paid';
    public const string STATUS_CANCELED = 'canceled';

    protected $guarded = ['id', 'created_at', 'updated_at'];


    public static array $validationRules = [
        'user_id' => 'required|numeric|min:0',
        'external_id' => 'sometimes|nullable|string',
        'status' => 'required|in:open,paid,canceled',
        'amount' => 'required|numeric|min:0',
        'currency' => 'required|string',
        'gateway' => 'required|string',
        'url' => 'sometimes|nullable|string|max:1024'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'external_id' => 'string',
        'status' => 'string',
        'amount' => 'integer',
        'currency' => 'string',
        'gateway' => 'string',
        'url' => 'string'
    ];

    protected $fillable = [
        'user_id',
        'external_id',
        'status',
        'amount',
        'currency',
        'gateway',
        'url'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
