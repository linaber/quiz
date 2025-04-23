<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGameSession extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'stats' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
