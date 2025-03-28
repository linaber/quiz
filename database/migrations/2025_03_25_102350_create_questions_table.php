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
            $table->string('title')->nullable();
            // Контент вопроса
            $table->text('question_text');
            $table->enum('content_type', ['text', 'image', 'audio', 'video'])->default('text');
            $table->string('media_path')->nullable();

            // Ответы
            $table->enum('answer_type', ['options', 'input', 'multimedia'])->default('options');
            $table->json('options')->nullable(); // [{text: "...", is_correct: bool, media_path: "..."}]
            $table->string('correct_answer_text')->nullable();
            $table->string('correct_answer_media_path')->nullable();

            // Подсказки

            $table->enum('hint_content_type', ['text', 'image', 'audio', 'video'])->default('text');
            $table->text('hint_text')->nullable();
            $table->string('hint_media_path')->nullable();
            $table->unsignedTinyInteger('hint_price')->default(10);
            $table->integer('times_hint_used')->default(0);
            $table->integer('times_correct_with_hint')->default(0);

            // Статистика

            $table->float('difficulty_rating')->default(3.0);
            $table->boolean('is_multiplayer_compatible')->default(true);
            $table->boolean('is_multilanguage_compatible')->default(false);
            $table->json('translatable_fields')->nullable();

            $table->integer('times_answered')->default(0);
            $table->integer('times_correct')->default(0);


            $table->timestamps();
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
