<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Пользователи';
    protected static ?string $modelLabel = 'Пользователь';
    protected static ?string $pluralModelLabel = 'Пользователи';

    public static function canDelete($record): bool
    {
        return false; // Запрещает удаление для всех записей этого ресурса
    }



    public static function form(Form $form): Form
    {

        $tempPassword = Str::random(12);
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Имя')->required(),
                TextInput::make('email')
                    ->label('Е-майл')->email()->required(),
                TextInput::make('balance')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->label('Баланс (cents)'),

                Toggle::make('is_banned')
                    ->label('Активен')
                    ->onColor('danger')
                    ->offColor('success'),


                Select::make('role')
                    ->label('Роль')
                    ->required()
                    ->options([
                     //   'user' => 'Пользователь',
                        'admin' => 'Администратор',
                    ])
                    ->default('admin')
                    ->live(),


                TextInput::make('password')
                    ->label('Временный пароль (только для админов)')
                    ->default($tempPassword)
                    ->readOnly()
                    ->hidden(fn ($get, $operation) => $get('role') !== 'admin' || $operation !== 'create')
                    ->helperText('Сообщите этот пароль администратору. При первом входе потребуется сменить.')
                     ->dehydrated(true)
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)), // Хешируем пароль перед отправкой


            ]);
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Если временный пароль был передан в форме, устанавливаем его в поле password
        if (isset($data['temp_password_display']) && !empty($data['temp_password_display'])) {
            $data['password'] = Hash::make($data['temp_password_display']); // Хешируем пароль перед отправкой
        }
        $data['mutated']='yes';
        return $data;
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->label('Имя')->searchable(),
                TextColumn::make('email')->label('Е-майл')->searchable(),
                TextColumn::make('balance')
                    ->label('Баланс')
                    ->money('EUR', divideBy: 100),
                TextColumn::make('role')->label('Роль')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'user' => 'gray',
                    }),
                IconColumn::make('is_banned')
                    ->label('Статус')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-check')
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->actions([

            ])

            ->filters([
              //  Tables\Filters\TrashedFilter::make(), // Для мягкого удаления
            ]);
    }



//    protected function mutateFormDataBeforeCreate(array $data): array
//    {
//        // 1. Дебаг (удалите после проверки)
//        Log::debug('Данные перед обработкой:', $data);
//
//        // 2. Генерация пароля
//    //    $plainPassword = $data['temp_password'] ?? Str::random(12);
//        $plainPassword = $data['temp_password'] ;
//        if (empty($plainPassword)) {
//            throw new \Exception('Пароль не может быть пустым');
//        }
//
//        // 3. Хеширование
//        $data['password'] = Hash::make($plainPassword);
//        $data['email_verified_at'] = now();
//
//        // 4. Возврат
//        return $data;
//    }

//    protected function mutateFormDataBeforeCreate(array $data): array
//    {
//
//        dd($data);
//        if ($data['role'] === 'admin') {
//            $data['password'] = Hash::make($data['temp_password']);
//            $data['force_password_change'] = true;
//        }
//
//        return $data;
//    }




    protected function getCreatedNotification(): ?Notification
    {
        $tempPassword = request()->input('password');

        return Notification::make()
            ->success()
            ->title('Пользователь создан')
            ->body($tempPassword
                ? "Временный пароль: $tempPassword"
                : 'Учётная запись создана');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }


}
