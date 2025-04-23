<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','icon','color'];

    const ICONS = [
        // Основные категории
        'film' => ['name' => 'Кино', 'icon' => 'fas fa-film'],
        'book' => ['name' => 'Литература', 'icon' => 'fas fa-book'],
        'music' => ['name' => 'Музыка', 'icon' => 'fas fa-music'],
        'geography' => ['name' => 'География', 'icon' => 'fas fa-globe-americas'],
        'food' => ['name' => 'Еда', 'icon' => 'fas fa-utensils'],
        'science' => ['name' => 'Наука', 'icon' => 'fas fa-atom'],
        'business' => ['name' => 'Бизнес', 'icon' => 'fas fa-briefcase'],
        'games' => ['name' => 'Игры', 'icon' => 'fas fa-gamepad'],
        'health' => ['name' => 'Здоровье', 'icon' => 'fas fa-heartbeat'],
        'technology' => ['name' => 'Технологии', 'icon' => 'fas fa-microchip'],
        'puzzles' => ['name' => 'Головоломки', 'icon' => 'fas fa-puzzle-piece'],
        'history' => ['name' => 'История', 'icon' => 'fas fa-landmark'],
        'fashion' => ['name' => 'Мода', 'icon' => 'fas fa-tshirt'],
        'cars' => ['name' => 'Авто', 'icon' => 'fas fa-car'],
        'finance' => ['name' => 'Финансы', 'icon' => 'fas fa-money-bill-wave'],

        // Дополнительные категории
        'programming' => ['name' => 'Программирование', 'icon' => 'fas fa-code'],
        'physics' => ['name' => 'Физика', 'icon' => 'fas fa-atom'],
        'ecology' => ['name' => 'Экология', 'icon' => 'fas fa-leaf'],
        'art' => ['name' => 'Искусство', 'icon' => 'fas fa-paint-brush'],
        'sport' => ['name' => 'Спорт', 'icon' => 'fas fa-futbol'],
        'space' => ['name' => 'Космос', 'icon' => 'fas fa-rocket'],
        'jewelry' => ['name' => 'Драгоценности', 'icon' => 'fas fa-gem'],
        'theater' => ['name' => 'Театр', 'icon' => 'fas fa-theater-masks'],
        'photography' => ['name' => 'Фотография', 'icon' => 'fas fa-camera-retro'],
        'fantasy' => ['name' => 'Фэнтези', 'icon' => 'fas fa-dragon'],
        'chess' => ['name' => 'Шахматы', 'icon' => 'fas fa-chess'],
        'audio' => ['name' => 'Аудиотехника', 'icon' => 'fas fa-headphones'],
        'sweets' => ['name' => 'Сладости', 'icon' => 'fas fa-cookie-bite'],
        'animals' => ['name' => 'Животные', 'icon' => 'fas fa-paw'],
        'travel' => ['name' => 'Путешествия', 'icon' => 'fas fa-plane'],
        'ai' => ['name' => 'Искусственный интеллект', 'icon' => 'fas fa-robot'],
        'psychology' => ['name' => 'Психология', 'icon' => 'fas fa-brain'],
        'mythology' => ['name' => 'Мифология', 'icon' => 'fas fa-book-dead']
    ];

    const COLORS = [
        'red' => '#ff6b6b',
        'green' => '#51cf66',
        'blue' => '#228be6',
        'yellow' => '#fcc419',
        'purple' => '#ae3ec9',
        'pink' => '#d6336c',
        'indigo' => '#5f3dc4',
        'orange' => '#fd7e14',
        'teal' => '#12b886',
        'cyan' => '#15aabf',
        'gray' => '#868e96'
    ];

    protected static function booted()
    {
        static::creating(function ($category) {
            if (empty($category->color)) {
                $category->color = array_rand(self::COLORS);
            }
            if (empty($category->icon)) {
                $category->icon = $this->getDefaultIconForColor($category->color);
            }
        });
    }

    public function getPreviewAttributes(): array
    {
        return [
            'color' => $this->color ?? '#3b82f6',
            'icon' => $this->icon ?? 'heroicon-o-question-mark-circle',
            'name' => $this->name ?? 'Новая категория'
        ];
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }
}
