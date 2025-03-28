<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','icon','color'];
    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }
}
