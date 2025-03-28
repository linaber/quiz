<?php

namespace App\Filament\Resources\UserGameSessionResource\Pages;

use App\Filament\Resources\UserGameSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserGameSession extends EditRecord
{
    protected static string $resource = UserGameSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
