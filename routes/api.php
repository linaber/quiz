<?php

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\{
    AuthController,
    QuestionController,
    GameSessionController,
    BalanceController,
    Admin\BalanceController as AdminBalanceController
};


//Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::middleware(['auth:sanctum'])->group(function () {
    // Вопросы
    Route::get('/questions', [QuestionController::class, 'index']);

    Route::get('questions/{question:id}', [QuestionController::class, 'show']);
   // Route::get('/questions/{id}', [QuestionController::class, 'show']);


    Route::get('/balance', [BalanceController::class, 'getBalance']);
    Route::post('/balance/buy-hint', [BalanceController::class, 'buyHint']);

    // Игровые сессии
    Route::post('/sessions', [GameSessionController::class, 'store']);
    Route::get('/sessions/history', [GameSessionController::class, 'history']);
});


Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::post('/balance/top-up', [AdminBalanceController::class, 'topUp']);
    Route::get('/test', function () {
        return response()->json(['message' => 'admin test works!']);
    });
});

Route::get('/test', function () {
    return response()->json(['message' => 'API works!']);
});
Route::get('/test-login', function() {
    // 1. Создаём пользователя через Filament-систему
    $user = User::firstOrCreate(
        ['email' => 'admin4@test.com'],
        [
            'name' => 'Admin',
        //    'password' => Hash::make('admin123'),
            'password' => 'admin123',
            'role' => 'admin',
            'email_verified_at' => now()
        ]
    );

    // 2. Авторизуем через правильный guard
    Filament::auth()->login($user);

    // 3. Редирект через Filament
    return redirect(Filament::getUrl());
});


Route::get('/check-password', function() {
    $password = 'admin123';
    $hash = Hash::make($password);

    return [
        'input' => $password,
        'hash' => $hash,
        'check' => Hash::check($password, $hash) ? '✅ ok' : '❌ fail'
    ];
});
