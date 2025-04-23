<?php

namespace App\Filament\Widgets;

use App\Filament\Pages\ChangePassword;
use Filament\Actions\Action;
use Filament\Widgets\Widget;
use Filament\Widgets\StatsOverviewWidget;

class ProfileWidget extends StatsOverviewWidget
{
    protected static string $view = 'filament.widgets.profile-widget';

   // protected int|string|array $columnSpan = 'full';
    protected int|string|array $columnSpan = 6;
    protected function getActions(): array
    {
        return [
            Action::make('changePassword')
                ->label('Change Password')
                ->url(ChangePassword::getUrl())
                ->icon('heroicon-o-lock-closed'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->check();
    }

    protected function getViewData(): array
    {
        return [
            'user' => auth()->user()
        ];
    }
}
