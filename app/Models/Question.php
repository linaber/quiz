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
    protected $guarded = [];
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function getTranslatable(string $field, string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translatable_fields[$field][$locale] ?? $this->$field;
    }

    public function updateDifficulty()
    {
        if ($this->times_answered > 0) {
            $baseSuccessRate = $this->times_correct / $this->times_answered;

            // Корректировка с учётом подсказок
            $hintPenalty = 0;
            if ($this->times_hint_used > 0) {
                $hintSuccessRate = $this->times_correct_with_hint / $this->times_hint_used;
                $hintPenalty = ($hintSuccessRate - $baseSuccessRate) * 0.5;
            }

            $adjustedRate = min(1, max(0, $baseSuccessRate - $hintPenalty));
            $this->difficulty_rating = round(5 * (1 - $adjustedRate), 1);
            $this->save();
        }
    }
}
