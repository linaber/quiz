<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Question;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Всего вопросов', Question::count())
                ->icon('heroicon-o-question-mark-circle'),

            Stat::make('Категории', Category::count())
                ->icon('heroicon-o-tag'),

            Stat::make('Мультиязычные', Question::where('is_multilanguage_compatible', true)->count())
                ->icon('heroicon-o-language')
        ];
    }
}
