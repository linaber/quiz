<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Traits\HandlesCreateRecordException;
use Illuminate\Database\Eloquent\Model;

class EditQuestion extends EditRecord
{

    use HandlesCreateRecordException;

    protected static string $resource = QuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return $this->tryCreateRecord(fn() => parent::handleRecordUpdate($record, $data));
    }


//    protected function getFormActions(): array
//    {
//        return [
//            Actions\Action::make('save')
//                ->label('Сохранить')
//                ->submit('save'),
//
//            Actions\Action::make('cancel')
//                ->label('Отмена')
//                ->url($this->getResource()::getUrl('index'))
//        ];
//    }
}
