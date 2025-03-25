<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            // Контент вопроса
            $table->enum('content_type', ['text', 'image', 'audio', 'video'])->default('text');
            $table->text('question_text');
            $table->string('media_path')->nullable();

            // Ответы
            $table->enum('answer_type', ['options', 'input', 'multimedia'])->default('options');
            $table->json('options')->nullable(); // [{text: "...", is_correct: bool, media_path: "..."}]
            $table->string('correct_answer')->nullable(); // Для типов input/multimedia

            // Подсказки
            $table->text('hint')->nullable();
            $table->float('hint_price')->default(10);

            // Статистика
            $table->float('difficulty_rating')->default(3.0);
            $table->boolean('is_multiplayer_compatible')->default(true);

            $table->timestamps();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('is_multilanguage_compatible')->default(false)->after('is_multiplayer_compatible');
            $table->json('translatable_fields')->nullable()->after('is_multilanguage_compatible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
