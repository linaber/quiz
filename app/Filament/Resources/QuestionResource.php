<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;

use Closure;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use Filament\Forms\Components\Toggle;

use Filament\Forms\Components\TextInput;


use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;
use Illuminate\Validation\ClosureValidationRule;


use Filament\Forms\Components\Actions\ActionButton;
use Filament\Forms\Components\Modal;


use Intervention\Image\Facades\Image;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Format\Audio\Mp3;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

//use Livewire\TemporaryUploadedFile;
//use Intervention\Image\Facades\Image;

//use Intervention\Image\ImageManager;
//use Intervention\Image\Drivers\Gd\Driver; // Или Imagick, если предпочитаете
class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

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
                            ->autofocus(),

                        Select::make('content_type')
                            ->label(__('filament.content_type'))
                            ->options([
                                'text' => 'Текст',
                                'image' => 'Изображение',
                                'audio' => 'Аудио',
                                'video' => 'Видео'
                            ])
                            ->live()
                            ->default('text')
                            ->afterStateUpdated(function ($set) {
                                $set('media_path', null);
                            }),

// IMAGE upload
                        FileUpload::make('media_path')
                            ->label(__('filament.media_path'))
                            ->visible(fn ($get) => $get('content_type') === 'image')
                            ->directory('questions')
                            ->preserveFilenames()
                            ->disk('public')
                            ->image() // Автоматически включает превью для изображений
                            ->imageEditor() // Включает встроенный редактор (обрезание, поворот и т.д.)
                            ->imageEditorAspectRatios(['16:9', '4:3', '1:1']) // Опционально: фиксированные пропорции
                            ->imageResizeTargetWidth(1200) // Опционально: сжатие до 1200px по ширине
                            ->imageResizeTargetHeight(800) // Опционально: сжатие до 800px по высоте
                            ->imagePreviewHeight('200px') // Высота превью в списке
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])
                            ->openable() // Можно открыть в новом окне
                            ->downloadable(),// Можно скачать

// AUDIO upload
                        FileUpload::make('media_path')
                            ->label(__('filament.media_path'))
                            ->visible(fn ($get) => $get('content_type') === 'audio')
                            ->directory('questions')
                            ->preserveFilenames()
                            ->disk('public')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/x-wav', 'audio/x-aiff'])
                            ->maxSize(1024)
                            ->previewable(true) // Включает базовый превью (название файла + иконка)
                            ->openable() // Можно открыть в новой вкладке
                            ->downloadable(), // Можно скачать,

// VIDEO upload
                        FileUpload::make('media_path')
                            ->label(__('filament.media_path'))
                            ->visible(fn ($get) => $get('content_type') === 'video')
                            ->directory('questions')
                            ->preserveFilenames()
                            ->disk('public')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska'])
                            ->maxSize(20000)
                            ->imagePreviewHeight('250px') // Устанавливаем высоту превью
                            ->panelAspectRatio('16:9') // Соотношение сторон панели
                            ->panelLayout('integrated') // Варианты: 'integrated', 'compact', 'circle'
                            ->openable() // Позволяет открывать файл в новом окне
                            ->downloadable() // Добавляет кнопку скачивания
                            ->previewable(true),// Включает превью,



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
                            ->default('text')
                            ->afterStateUpdated(function ($set) {
                                $set('hint_media_path', null);
                            }),

                        TextInput::make('hint_text')
                            ->label(__('filament.text'))
                            ->maxLength(255),

// IMAGE
                        FileUpload::make('hint_media_path')
                            ->label(__('filament.media_path'))
                            ->visible(fn ($get) => $get('hint_content_type') === 'image')
                            ->directory('questions/hints')
                            ->preserveFilenames()
                            ->disk('public')
                            ->image() // Автоматически включает превью для изображений
                            ->imageEditor() // Включает встроенный редактор (обрезание, поворот и т.д.)
                            ->imageEditorAspectRatios(['16:9', '4:3', '1:1']) // Опционально: фиксированные пропорции
                            ->imageResizeTargetWidth(1200) // Опционально: сжатие до 1200px по ширине
                            ->imageResizeTargetHeight(800) // Опционально: сжатие до 800px по высоте
                            ->imagePreviewHeight('200px') // Высота превью в списке
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])
                            ->openable() // Можно открыть в новом окне
                            ->downloadable(),// Можно скачать

// AUDIO
                        FileUpload::make('hint_media_path')
                            ->label(__('filament.media_path'))
                            ->visible(fn ($get) => $get('hint_content_type') === 'audio')
                            ->directory('questions/hints')
                            ->preserveFilenames()
                            ->disk('public')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/x-wav', 'audio/x-aiff'])
                            ->maxSize(1024)
                            ->previewable(true) // Включает базовый превью (название файла + иконка)
                            ->openable() // Можно открыть в новой вкладке
                            ->downloadable(), // Можно скачать,

// VIDEO
                        FileUpload::make('hint_media_path')
                            ->label(__('filament.media_path'))
                            ->visible(fn ($get) => $get('hint_content_type') === 'video')
                            ->directory('questions/hints')
                            ->preserveFilenames()
                            ->disk('public')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska'])
                            ->maxSize(20000)
                            ->imagePreviewHeight('250px') // Устанавливаем высоту превью
                            ->panelAspectRatio('16:9') // Соотношение сторон панели
                            ->panelLayout('integrated') // Варианты: 'integrated', 'compact', 'circle'
                            ->openable() // Позволяет открывать файл в новом окне
                            ->downloadable() // Добавляет кнопку скачивания
                            ->previewable(true),// Включает превью,




                    ]),

                    Tabs\Tab::make('Ответ')->schema([
                        Select::make('answer_type')
                            ->label(__('filament.answer_type'))
                            ->options([
                                'options' => 'Варианты',
                                'input' => 'Текстовый ввод',
                                'image' => 'Изображение',
                                'audio' => 'Аудио',
                                'video' => 'Видео'
                            ])
                            ->default('input')
                            ->required()
                            ->live(),

                        // Текстовые ответы
                        Repeater::make('answers')
                            ->label(__('filament.correct_answers'))
                            ->relationship('answers')
                          //  ->hidden(fn ($get) => $get('answer_type') !== 'input')
                            ->schema([
                                TextInput::make('text')
                                    ->label(__('filament.correct_answer_text'))
                                    ->required(),
                                Toggle::make('is_primary')
                                    ->label('Основной вариант')
                                    ->default(false),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('Добавить синоним')
                            ->minItems(1)
                            ->collapsible()
                            ->rules([
                                new ClosureValidationRule(function ($attribute, $value, $fail) {
                                    if (!is_array($value)) return;
                                    $primaryCount = collect($value)
                                        ->filter(fn ($item) => $item['is_primary'] ?? false)
                                        ->count();
                                    if ($primaryCount !== 1) {
                                        $fail('Требуется ровно один основной ответ.');
                                    }
                                }),
                            ]),

                        // Медиа-ответы



// IMAGE
                        FileUpload::make('correct_answer_media_path')
                            ->label(__('filament.media_path'))
                            ->visible(fn ($get) => $get('answer_type') === 'image')
                            ->directory('questions/answers')
                            ->preserveFilenames()
                            ->disk('public')
                            ->image() // Автоматически включает превью для изображений
                            ->imageEditor() // Включает встроенный редактор (обрезание, поворот и т.д.)
                            ->imageEditorAspectRatios(['16:9', '4:3', '1:1']) // Опционально: фиксированные пропорции
                            ->imageResizeTargetWidth(1200) // Опционально: сжатие до 1200px по ширине
                            ->imageResizeTargetHeight(800) // Опционально: сжатие до 800px по высоте
                            ->imagePreviewHeight('200px') // Высота превью в списке
                            ->maxSize(5120) // 5MB
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])
                            ->openable() // Можно открыть в новом окне
                            ->downloadable(),// Можно скачать

// AUDIO
                        FileUpload::make('correct_answer_media_path')
                            ->label(__('filament.media_path'))
                            ->visible(fn ($get) => $get('answer_type') === 'audio')
                            ->directory('questions/hints')
                            ->preserveFilenames()
                            ->disk('public')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/x-wav', 'audio/x-aiff'])
                            ->maxSize(1024)
                            ->previewable(true) // Включает базовый превью (название файла + иконка)
                            ->openable() // Можно открыть в новой вкладке
                            ->downloadable(), // Можно скачать,

// VIDEO
                        FileUpload::make('correct_answer_media_path')
                            ->label(__('filament.media_path'))
                            ->visible(fn ($get) => $get('answer_types') === 'video')
                            ->directory('questions/hints')
                            ->preserveFilenames()
                            ->disk('public')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska'])
                            ->maxSize(20000)
                            ->imagePreviewHeight('250px') // Устанавливаем высоту превью
                            ->panelAspectRatio('16:9') // Соотношение сторон панели
                            ->panelLayout('integrated') // Варианты: 'integrated', 'compact', 'circle'
                            ->openable() // Позволяет открывать файл в новом окне
                            ->downloadable() // Добавляет кнопку скачивания
                            ->previewable(true),// Включает превью,


                        // Варианты ответов
                        Repeater::make('options')
                            ->label(__('filament.options'))
                            ->addActionLabel(__('filament.add_variant'))
                            ->hidden(fn ($get) => $get('answer_type') !== 'options')
                            ->schema([
                                TextInput::make('text')
                                    ->label(__('filament.text'))
                                    ->required(),

                                FileUpload::make('media_path')
                                    ->label(__('filament.media_path'))
                                    ->directory('answers')
                                    ->preserveFilenames()
                                    ->image()
                                    ->maxSize(5120) // 5MB
                                    ->afterStateUpdated(function ($state, $set) {
                                        if (!$state) return;
                                        $processedFile = self::processImageUpload($state);
                                        if ($processedFile) {
                                            $set('media_path', $processedFile);
                                        }
                                    }),

                                Toggle::make('is_correct')
                                    ->label(__('filament.is_correct'))
                            ])
                    ])
                ])->columnSpanFull()
            ]);
    }

// Методы обработки медиа


    protected static function processUploadedFile(TemporaryUploadedFile $file): ?TemporaryUploadedFile
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return self::processImageUpload($file);
        }
        elseif (in_array($extension, ['mp4', 'mov', 'avi'])) {
            return self::processVideoUpload($file);
        }
        elseif (in_array($extension, ['mp3', 'wav', 'ogg'])) {
            return self::processAudioUpload($file);
        }

        return $file;
    }

    protected static function processImageUpload(TemporaryUploadedFile $file): TemporaryUploadedFile
    {
        $image = Image::make($file->getRealPath());

        // Автоматический ресайз с сохранением пропорций
        $maxWidth = 1200;
        if ($image->width() > $maxWidth) {
            $image->resize($maxWidth, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'img') . '.webp';
        $image->encode('webp', 75)->save($tempPath);

        return TemporaryUploadedFile::createFromLivewire(
            $tempPath,
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp'
        );
    }

    protected static function processVideoUpload(TemporaryUploadedFile $file): TemporaryUploadedFile
    {
        // Требуется установка FFmpeg
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($file->getRealPath());

        $format = new X264();
        $format->setKiloBitrate(1500);

        $tempPath = tempnam(sys_get_temp_dir(), 'video') . '.mp4';
        $video->save($format, $tempPath);

        return TemporaryUploadedFile::createFromLivewire(
            $tempPath,
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.mp4'
        );
    }

    protected static function processAudioUpload(TemporaryUploadedFile $file): TemporaryUploadedFile
    {
        $ffmpeg = FFMpeg::create();
        $audio = $ffmpeg->open($file->getRealPath());

        $format = new Mp3();
        $format->setAudioKiloBitrate(128);

        $tempPath = tempnam(sys_get_temp_dir(), 'audio') . '.mp3';
        $audio->save($format, $tempPath);

        return TemporaryUploadedFile::createFromLivewire(
            $tempPath,
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.mp3'
        );
    }
    protected static function processMedia(TemporaryUploadedFile $file, string $type): ?TemporaryUploadedFile
    {
        try {
            if ($type === 'image') {
                return self::processImage($file);
            } elseif ($type === 'video') {
                return self::processVideo($file);
            } elseif ($type === 'audio') {
                return self::processAudio($file);
            }
        } catch (\Exception $e) {
            logger()->error('Media processing failed: '.$e->getMessage());
        }

        return null;
    }

    protected static function processImage(TemporaryUploadedFile $file): TemporaryUploadedFile
    {
        $image = Image::make($file->getRealPath());

        if ($image->width() > $image->height()) {
            $image->resize(1200, null, fn ($c) => $c->aspectRatio());
        } else {
            $image->resize(null, 1200, fn ($c) => $c->aspectRatio());
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'img') . '.webp';
        $image->encode('webp', 70)->save($tempPath);

        return TemporaryUploadedFile::createFromLivewire(
            $tempPath,
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp'
        );
    }

    protected static function processVideo(TemporaryUploadedFile $file): TemporaryUploadedFile
    {
        // Требуется FFmpeg
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($file->getRealPath());

        $format = new X264();
        $format->setKiloBitrate(1500);

        $tempPath = tempnam(sys_get_temp_dir(), 'video') . '.mp4';
        $video->save($format, $tempPath);

        return TemporaryUploadedFile::createFromLivewire(
            $tempPath,
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.mp4'
        );
    }


    protected static function processAudio($file)
    {
        $ffmpeg = FFMpeg::create();
        $audio = $ffmpeg->open($file->getRealPath());

        $format = new Mp3();
        $format->setAudioKiloBitrate(128); // 128 kbps

        $tempPath = tempnam(sys_get_temp_dir(), 'audio') . '.mp3';
        $audio->save($format, $tempPath);

        file_put_contents($file->getRealPath(), file_get_contents($tempPath));
        unlink($tempPath);

        return $file;
    }




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
