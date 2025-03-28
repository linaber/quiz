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

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // Метод, который возвращает переведенное название для навигации
    public static function getNavigationLabel(): string
    {
        return __('filament.questions'); // Перевод для ключа 'filament.question'
    }

    // Метод для перевода заголовка ресурса (если нужно)
    public static function getLabel(): string
    {
        return __('filament.question');
    }

    // Метод для перевода для множественного числа
    public static function getPluralLabel(): string
    {
        return __('filament.questions');  // Здесь можно добавить перевод для множественного числа
    }

//    public static function form(Form $form): Form
//    {
//        return $form
//            ->schema([
//                Forms\Components\TextInput::make('content_type')
//                    ->required(),
//                Forms\Components\Textarea::make('question_text')
//                    ->required()
//                    ->columnSpanFull(),
//                Forms\Components\TextInput::make('media_path')
//                    ->maxLength(255),
//                Forms\Components\TextInput::make('answer_type')
//                    ->required(),
//                Forms\Components\TextInput::make('options'),
//                Forms\Components\TextInput::make('correct_answer')
//                    ->maxLength(255),
//                Forms\Components\Textarea::make('hint')
//                    ->columnSpanFull(),
//                Forms\Components\TextInput::make('hint_price')
//                    ->required()
//                    ->numeric()
//                    ->default(10),
//                Forms\Components\TextInput::make('difficulty_rating')
//                    ->required()
//                    ->numeric()
//                    ->default(3),
//                Forms\Components\Toggle::make('is_multiplayer_compatible')
//                    ->required(),
//                Toggle::make('is_multilanguage_compatible')
//                    ->reactive()
//                    ->columnSpan(1),
//
//                Fieldset::make('Translations')
//                    ->visible(fn ($get) => $get('is_multilanguage_compatible'))
//                    ->schema([
//                        TextInput::make('translatable_fields.question_text.en')
//                            ->label('Question (English)'),
//                        TextInput::make('translatable_fields.question_text.ru')
//                            ->label('Question (Russian)'),
//                        // Добавьте другие языки по необходимости
//                    ])
//            ]);
//    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('QuestionContent')->tabs([
                    Tabs\Tab::make('Основное')->schema([
                        TextInput::make('title')
                            ->label(__('filament.title'))
                            ->required()
                            ->maxLength(255),

                        Select::make('categories')
                            ->label(__('filament.categories'))
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload(),

                        TextInput::make('difficulty_rating')
                            ->label(__('filament.difficulty_rating'))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(5)
                            ->step(0.1)
                            ->default(3.0),

                        Toggle::make('is_multilanguage_compatible')
                            ->label(__('filament.is_multilanguage_compatible'))
                            ->reactive(),
                    ]),

                    Tabs\Tab::make('Вопрос')->schema([

                        Textarea::make('question_text')
                            ->label(__('filament.question_text'))
                            ->required()
                            ->autofocus() ,

                        Select::make('content_type')
                            ->label(__('filament.content_type'))
                            ->options([
                                'text' => 'Текст',
                                'image' => 'Изображение',
                                'audio' => 'Аудио',
                                'video' => 'Видео'
                            ])
                            ->live()
                            ->default('text'),



                        FileUpload::make('media_path')
                            ->label(__('filament.media_path'))
                            ->directory('questions')
                            ->hidden(fn ($get) => $get('content_type') === 'text')
                    ]),

                    Tabs\Tab::make('Подсказка')->schema([



                        Select::make('hint_content_type')
                            ->label(__('filament.content_type'))
                            ->options([
                                'text' => 'Текст',
                                'image' => 'Изображение',
                                'audio' => 'Аудио',
                                'video' => 'Видео'
                            ])
                            ->live()
                            ->default('text'),


                        TextInput::make('hint_text')
                            ->label(__('filament.text'))
                            ->maxLength(255)
                        ,

                        FileUpload::make('hint_media_path')
                            ->label(__('filament.media_path'))
                            ->directory('questions')
                            ->hidden(fn ($get) => $get('hint_content_type') === 'text'),


                        TextInput::make('hint_price')
                            ->label(__('filament.hint_price'))
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(200)
                            ->step(1)
                            ->default(10),

                    ]),


                    Tabs\Tab::make('Ответ')->schema([
                        Select::make('answer_type')
                            ->label(__('filament.answer_type'))
                            ->options([
                                'options' => 'Варианты',
                                'input' => 'Текстовый ввод',
                                'multimedia' => 'Медиа-ответ'
                            ])
                            ->live(),

                        TextInput::make('correct_answer_text')
                            ->label(__('filament.correct_answer_text'))
                            ->required()
                            ->maxLength(255)
                            ->hidden(fn ($get) => !in_array($get('answer_type'), ['input', 'multimedia'])),


                        FileUpload::make('correct_answer_media_path')
                            ->label(__('filament.correct_answer_media_path'))
                            ->directory('questions')
                            ->hidden(fn ($get) => !in_array($get('answer_type'), ['multimedia'])),


                        Repeater::make('options')
                            ->label(__('filament.options'))
                            ->addActionLabel(__('filament.add_variant'))
                            ->hidden(fn ($get) => $get('answer_type') !== 'options')
                            ->schema([
                                TextInput::make('text')->label(__('filament.text'))->required(),
                                FileUpload::make('media_path')->label(__('filament.media_path'))->directory('answers'),
                                Toggle::make('is_correct')->label(__('filament.is_correct'))
                            ])
                    ])
                ])->columnSpanFull()
            ]);
    }
//    public static function table(Table $table): Table
//    {
//        return $table
//            ->columns([
//                Tables\Columns\TextColumn::make('content_type'),
//                Tables\Columns\TextColumn::make('media_path')
//                    ->searchable(),
//                Tables\Columns\TextColumn::make('answer_type'),
//                Tables\Columns\TextColumn::make('correct_answer')
//                    ->searchable(),
//                Tables\Columns\TextColumn::make('hint_price')
//                    ->numeric()
//                    ->sortable(),
//                Tables\Columns\TextColumn::make('difficulty_rating')
//                    ->numeric()
//                    ->sortable(),
//                Tables\Columns\IconColumn::make('is_multiplayer_compatible')
//                    ->boolean(),
//                Tables\Columns\TextColumn::make('created_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//                Tables\Columns\TextColumn::make('updated_at')
//                    ->dateTime()
//                    ->sortable()
//                    ->toggleable(isToggledHiddenByDefault: true),
//            ])
//            ->filters([
//                //
//            ])
//            ->actions([
//                Tables\Actions\EditAction::make(),
//            ])
//            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
//            ]);
//    }

//    public static function table(Table $table): Table
//    {
//        return $table
//            ->columns([
//                TextColumn::make('title')->searchable(),
//                IconColumn::make('is_multilanguage_compatible')
//                    ->boolean()
//                    ->label('ML'),
//                BadgeColumn::make('content_type')
//                    ->formatStateUsing(fn (string $state): string => match ($state) {
//                        'text' => 'Текст',
//                        'image' => 'Изображение',
//                        'audio' => 'Аудио',
//                        'video' => 'Видео',
//                        default => $state,
//                    })
//                    ->color(fn (string $state): string => match ($state) {
//                        'text' => 'primary',
//                        'image' => 'warning',
//                        'audio' => 'success',
//                        'video' => 'danger',
//                        default => 'gray',
//                    })
//            ])
//            ->filters([
//                SelectFilter::make('content_type')
//                    ->options([
//                        'text' => 'Текст',
//                        'image' => 'Изображение',
//                        'audio' => 'Аудио',
//                        'video' => 'Видео'
//                    ])
//            ])
//            ->actions([
//                Tables\Actions\EditAction::make(),
//            ]);
//    }




    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.title_table'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label(__('filament.categories'))
                    ->badge()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_multilanguage_compatible')
                    ->label(__('filament.is_multilanguage_compatible_table'))
                    ->boolean(),

                Tables\Columns\TextColumn::make('difficulty_rating')
                    ->label(__('filament.difficulty_rating_table'))
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 1)),

                Tables\Columns\TextColumn::make('content_type')
                    ->label(__('filament.content_type'))
                    ->formatStateUsing(fn ($state) => match($state) {
                        'text' => 'Текст',
                        'image' => 'Изображение',
                        'audio' => 'Аудио',
                        'video' => 'Видео',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('answer_type')
                    ->label(__('filament.answer_type'))
                    ->formatStateUsing(fn ($state) => match($state) {
                        'options' => 'Варианты',
                        'input' => 'Текстовый ввод',
                        'multimedia' => 'Медиа-ответ',
                        default => $state,
                    }),

                

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
                Tables\Filters\SelectFilter::make('content_type')
                    ->label(__('filament.content_type'))
                    ->options([
                        'text' => 'Текст',
                        'image' => 'Изображение',
                        'audio' => 'Аудио',
                        'video' => 'Видео',
                    ]),

                Tables\Filters\SelectFilter::make('answer_type')
                    ->label(__('filament.answer_type'))
                    ->options([
                        'options' => 'Варианты',
                        'input' => 'Текстовый ввод',
                        'multimedia' => 'Медиа-ответ',
                    ]),

                Tables\Filters\Filter::make('is_multilanguage_compatible')
                    ->label(__('filament.is_multilanguage_compatible'))
                    ->query(fn (Builder $query) => $query->where('is_multilanguage_compatible', true)),

                Tables\Filters\SelectFilter::make('categories')
                    ->label(__('filament.categories'))
                    ->relationship('categories', 'name')
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
