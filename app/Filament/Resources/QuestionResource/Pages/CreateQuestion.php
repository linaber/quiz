<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\QueryException;
use App\Filament\Traits\HandlesCreateRecordException;
use Illuminate\Validation\ValidationException;

class CreateQuestion extends CreateRecord
{
    use HandlesCreateRecordException;
    protected static string $resource = QuestionResource::class;

//
//    protected function mutateFormDataBeforeCreate(array $data): array
//    {
//        if (($data['answer_type'] ?? null) === 'input') {
//            $answers = $data['answers'] ?? [];
//
//            $primaryCount = collect($answers)->filter(fn ($item) => $item['is_primary'] ?? false)->count();
//
//            if ($primaryCount !== 1) {
//                throw \Illuminate\Validation\ValidationException::withMessages([
//                    'answers.0.is_primary' => 'Должен быть выбран ровно один основной ответ.',
//                ]);
//            }
//        }
//
//        return $data;
//    }
//
//    protected function mutateFormDataBeforeSave(array $data): array
//    {
//        return $this->mutateFormDataBeforeCreate($data); // Логика одна и та же
//    }


//    protected function mutateFormDataBeforeCreate(array $data): array
//    {
//        // Проверяем, что answer_type = 'input'
//        if (($data['answer_type'] ?? null) === 'input') {
//            $answers = $data['answers'] ?? [];
//
//            // Проверяем, сколько ответов с is_primary = true
//            $primaryCount = collect($answers)
//                ->filter(fn ($item) => !empty($item['is_primary']))
//                ->count();
//
//            // Если нет основного варианта ответа
//            if ($primaryCount === 0) {
//                throw ValidationException::withMessages([
//                    'answers' => ['Необходимо указать один основной вариант ответа.'],
//                ]);
//            }
//
//            // Если больше одного основного ответа
//            if ($primaryCount > 1) {
//                throw ValidationException::withMessages([
//                    'answers' => ['Допускается только один основной вариант ответа.'],
//                ]);
//            }
//        }
//
//        return $data;
//    }

//    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
//    {
//        return $this->tryCreateRecord(fn() => parent::handleRecordCreation($data));
//    }

//    protected function afterValidate(): void
//    {
//        $data = $this->form->getState();
//
//        if (($data['answer_type'] ?? null) === 'input') {
//            $answers = $data['answers'] ?? [];
//
//            $primaryCount = collect($answers)
//                ->filter(fn ($item) => !empty($item['is_primary']))
//                ->count();
//
//            if ($primaryCount === 0) {
//                $this->addError('answers', 'Необходимо указать один основной вариант ответа.');
//            } elseif ($primaryCount > 1) {
//                $this->addError('answers', 'Допускается только один основной вариант ответа.');
//            }
//        }
//    }

}
