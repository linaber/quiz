<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Пользователей', User::where('role','user')->count())
                ->icon('heroicon-o-users'),

            Stat::make('Категорий', Category::count())
                ->icon('heroicon-o-tag'),

            Stat::make('Вопросов', Question::count())
                ->icon('heroicon-o-question-mark-circle'),

            Stat::make('Админов', User::where('role','admin')->count())
                ->icon('heroicon-o-users'),


//            Stat::make('Мультиязычные', Question::where('is_multilanguage_compatible', true)->count())
//                ->icon('heroicon-o-language')
        ];
    }
}
