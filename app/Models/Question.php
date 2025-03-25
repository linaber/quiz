<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $casts = [
        'options' => 'array',
        'stats' => 'array',
        'translatable_fields' => 'array',
        'is_multilanguage_compatible' => 'boolean'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function getTranslatable(string $field, string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translatable_fields[$field][$locale] ?? $this->$field;
    }

}
