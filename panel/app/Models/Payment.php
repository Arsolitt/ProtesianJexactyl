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


    public static array $validationRules = [
        'user' => 'required|nullable',
        'external_id' => 'required|string',
        'status' => 'required|in:open,paid,canceled',
        'amount' => 'required|numeric|min:0',
        'currency' => 'required|string',
        'method' => 'required|string',
    ];

    protected $casts = [
        'user' => 'string|integer',
        'external_id' => 'string',
        'status' => 'string',
        'amount' => 'integer',
        'currency' => 'string',
        'method' => 'string',
    ];

    protected $fillable = [
        'user',
        'external_id',
        'status',
        'amount',
        'currency',
        'method',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }
}
