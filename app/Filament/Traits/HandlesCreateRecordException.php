<?php

namespace App\Filament\Traits;

use Filament\Notifications\Notification;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

trait HandlesCreateRecordException
{
    protected function tryCreateRecord(callable $callback)
    {
        try {
            return $callback();
        } catch (QueryException $e) {
            Notification::make()
                ->title('Ошибка при сохранении')
                ->body('Что-то пошло не так. Убедитесь, что все обязательные поля заполнены. Подробности: ' . $e->getMessage())
                ->danger()
                ->persistent()
                ->send();

            // Пример: подсказываем, какое именно поле может быть проблемным
            throw ValidationException::withMessages([
                'answer_type' => ['Это поле обязательно для заполнения.'],
            ]);
        }
    }
}
