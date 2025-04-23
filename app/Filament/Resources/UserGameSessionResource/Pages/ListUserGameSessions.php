<?php

namespace App\Filament\Resources\UserGameSessionResource\Pages;

use App\Filament\Resources\UserGameSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserGameSessions extends ListRecords
{
    protected static string $resource = UserGameSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
