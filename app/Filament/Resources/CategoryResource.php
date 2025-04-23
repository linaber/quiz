<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;



class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationLabel = 'Категории';
    protected static ?string $modelLabel = 'Категория';
    protected static ?string $pluralModelLabel = 'Категории';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';


    public static function form(Form $form): Form
    {


        return $form->schema([


            Forms\Components\TextInput::make('name')
                ->label('Название категории')
                ->required()
                ->maxLength(255)
                ->columnSpan(2)
                ->live(onBlur: true),




            Forms\Components\Select::make('icon')
                ->label('Иконка')
                ->options(
                    collect(Category::ICONS)
                        ->mapWithKeys(fn ($item) => [$item['icon'] => $item['name']])  // Ключом будет сам класс иконки
                )
                ->searchable()
                ->required()
                ->live(),

Forms\Components\Select::make('color')
    ->label('Цвет')
    ->options(Category::COLORS)
    ->required()
    ->allowHtml()
    ->options(function () {
        return collect(Category::COLORS)
            ->mapWithKeys(fn ($hex, $name) => [
                $hex => new HtmlString(
                    "<span class='flex items-center gap-2'>
                        <span style='background:{$hex}' class='w-4 h-4 rounded-full'></span>
                        {$name}
                    </span>"
                )
            ]);
    })
    ->live(),

Forms\Components\Placeholder::make('preview')
    ->label('Предварительный обзор')
    ->content(function ($get) {
      //  $iconKey = $get('icon') ?? 'film';

        $iconClass = $get('icon') ?? 'fas fa-question-circle';
        $color = $get('color') ?? '#3b82f6';
       // $icon = Category::ICONS[$iconKey]['icon'] ?? 'fas fa-question-circle';
       // $name = Category::ICONS[$iconKey]['name'] ?? 'Категория';
        $name = $get('name') ?? 'Новая категория';

        $textColor = self::getContrastColor($color);

        return new HtmlString(
            <<<HTML
            <div class="flex items-center justify-center gap-4 p-4 rounded-lg max-w-xs mx-auto" style="background:{$color}; color: {$textColor} !important;">
                <i class="{$iconClass} text-white text-2xl" style="color: {$textColor} !important;"></i>
                <span class="text-white font-bold text-xl text-center" style="color: {$textColor} !important;">{$name}</span>
            </div>
            HTML
        );
    })



      ]);



    }

    // Метод для определения контрастного цвета текста
    private static function getContrastColor(string $hexColor): string
    {
        // Удаляем # если есть
        $hexColor = ltrim($hexColor, '#');

        // Конвертируем в RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));

        // Рассчитываем яркость
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        // Возвращаем белый или черный в зависимости от яркости фона
        return ($brightness > 128) ? 'black' : 'white';
    }




    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Название')
                ->searchable(),

            Tables\Columns\ColorColumn::make('color')
                ->label('Цвет')
                ->copyable()
                ->sortable(),



            Tables\Columns\TextColumn::make('icon')
                ->label('Иконка')
                ->html()
                ->formatStateUsing(fn (string $state): string =>
                "<i class='{$state}'></i>"
                ),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Создано')
                ->dateTime()
                ->sortable()
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}





