<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $casts = [
        'metadata' => 'array',
        'is_admin' => 'boolean'
    ];

    protected $fillable = [
        'user_id', 'amount', 'type', 'status', 'metadata', 'is_admin'
    ];
}
