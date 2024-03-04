<?php

namespace Jexactyl\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasUlids;

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
