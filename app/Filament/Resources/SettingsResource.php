<?php
namespace App\Filament\Resources;

use App\Filament\Resources\SettingsResource\Pages;
use App\Filament\Resources\SettingsResource\RelationManagers;
use App\Models\Setting;
use App\Models\Settings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Actions\Action;



class SettingsResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Настройки';
    protected static ?string $modelLabel = 'Настройка';
    protected static ?string $pluralModelLabel = 'Настройки';


    public static function canDelete($record): bool
    {
        return false; // Запрещает удаление для всех записей этого ресурса
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->label('Ключ настройки')
                    ->disabled(fn ($get) => $form->getRecord() !== null),

                Forms\Components\Textarea::make('value')
                    ->required()
                    ->columnSpanFull()
                    ->label('Значение'),

                Forms\Components\TextInput::make('description')
                    ->maxLength(255)
                    ->label('Описание'),
            ]);
    }




    // Метод для определения действий
    public static function actions(): array
    {
        return [
            // Создаем действие для сохранения
            Action::make('save')
                ->label('Сохранить')
                ->action(fn (Setting $record) => $record->save())  // Сохранение записи
                ->requiresConfirmation()  // Требует подтверждения
                ->modalHeading('Подтверждение сохранения')  // Заголовок модалки
                ->modalDescription('Вы уверены, что хотите сохранить изменения?')  // Описание модалки
                ->modalSubmitButtonLabel('Да, сохранить')  // Кнопка для подтверждения
                ->modalCancelButtonLabel('Отмена')  // Кнопка для отмены
        ];
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->label('Ключ'),

                Tables\Columns\TextColumn::make('value')
                    ->limit(50)
                    ->label('Значение'),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->label('Описание'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Обновлено'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSettings::route('/create'),
            'edit' => Pages\EditSettings::route('/{record}/edit'),
        ];
    }


}
