<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $casts = [
        'options' => 'array',
        'stats' => 'array'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
