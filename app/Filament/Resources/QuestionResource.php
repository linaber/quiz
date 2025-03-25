<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('content_type')
                    ->required(),
                Forms\Components\Textarea::make('question_text')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('media_path')
                    ->maxLength(255),
                Forms\Components\TextInput::make('answer_type')
                    ->required(),
                Forms\Components\TextInput::make('options'),
                Forms\Components\TextInput::make('correct_answer')
                    ->maxLength(255),
                Forms\Components\Textarea::make('hint')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('hint_price')
                    ->required()
                    ->numeric()
                    ->default(10),
                Forms\Components\TextInput::make('difficulty_rating')
                    ->required()
                    ->numeric()
                    ->default(3),
                Forms\Components\Toggle::make('is_multiplayer_compatible')
                    ->required(),
                Toggle::make('is_multilanguage_compatible')
                    ->reactive()
                    ->columnSpan(1),

                Fieldset::make('Translations')
                    ->visible(fn ($get) => $get('is_multilanguage_compatible'))
                    ->schema([
                        TextInput::make('translatable_fields.question_text.en')
                            ->label('Question (English)'),
                        TextInput::make('translatable_fields.question_text.ru')
                            ->label('Question (Russian)'),
                        // Добавьте другие языки по необходимости
                    ])
            ]);
    }





    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('content_type'),
                Tables\Columns\TextColumn::make('media_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('answer_type'),
                Tables\Columns\TextColumn::make('correct_answer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hint_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('difficulty_rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_multiplayer_compatible')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
