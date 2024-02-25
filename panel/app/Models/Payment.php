<?php

namespace Jexactyl\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
//    use HasFactory;

    public const string STATUS_OPEN = 'open';
    public const string STATUS_PAID = 'paid';
    public const string STATUS_CANCELED = 'canceled';

    protected $guarded = ['id', 'created_at', 'updated_at'];


    public static array $validationRules = [
        'user_id' => 'required|nullable',
        'external_id' => 'nullable|string',
        'status' => 'required|in:open,paid,canceled',
        'amount' => 'required|numeric|min:0',
        'currency' => 'required|string',
        'gateway' => 'required|string',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'external_id' => 'string',
        'status' => 'string',
        'amount' => 'integer',
        'currency' => 'string',
        'gateway' => 'string',
    ];

    protected $fillable = [
        'user_id',
        'external_id',
        'status',
        'amount',
        'currency',
        'gateway',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }
}
