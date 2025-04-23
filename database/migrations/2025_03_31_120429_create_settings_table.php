<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });


        DB::table('settings')->insert([
            [
                'key' => 'hint_price',
                'value' => 10,
                'description' => 'Стоимость подсказки в баллах',
            ],
            [
                'key' => 'questions_per_game',
                'value' => 10,
                'description' => 'Количество вопросов в одной игре',
            ],
//            [
//                'key' => 'min_balance_for_game',
//                'value' => 0,
//                'description' => 'Минимальный баланс для старта игры',
//            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
