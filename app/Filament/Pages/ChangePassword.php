<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class ChangePassword extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static string $view = 'filament.pages.change-password';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Смена пароля';

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Изменить пароль')
                    ->description('Для безопастности пароль должен быть сложным и содержать минимум одну большую букву, одну маленькую, цифру, спецсимвол.')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Текущий пароль')
                            ->password()
                            ->required()
                            ->currentPassword(),

                        TextInput::make('new_password')
                            ->label('Новый пароль')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->rules(['confirmed'])
                            ->autocomplete('new-password'),

                        TextInput::make('new_password_confirmation')
                            ->label('Новый пароль еще раз')
                            ->password()
                            ->required()
                            ->autocomplete('new-password'),
                    ])
                    ->columns(1)
            ])
            ->statePath('data');
    }

    public function changePassword(): void
    {
        $state = $this->form->getState();
        $user = auth()->user();

        $user->update([
            'password' => Hash::make($state['new_password']),
            'force_password_change' => false,
        ]);

        Notification::make()
            ->title('Пароль был успешно изменен!')
            ->success()
            ->send();

        $this->redirect('/admin');
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Forms\Components\Actions\Action::make('changePassword')
                ->label('Обновить пароль')
                ->submit('changePassword'),
        ];
    }
}
