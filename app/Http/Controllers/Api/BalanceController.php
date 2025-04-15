<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BalanceController extends Controller
{

    public function getBalance(Request $request)
    {
        $user = $request->user();

        $cacheKey = "user_{$user->id}_balance";

        $balance = Cache::remember($cacheKey, now()->addMinutes(30), function() use ($user) {
            return $user->balance; // Получаем из БД, если нет в кеше
        });

        return response()->json([
            'balance' => $balance,
            'is_cached' => Cache::has($cacheKey) // Опционально для отладки
        ]);
    }

    public function getTransactions(Request $request)
    {

        $validated = $request->validate([
            'per_page' => 'nullable|integer|max:100',
            'type' => 'nullable|in:hint,payment,admin_topup'
        ]);

        $query = $request->user()->transactions(); // Автоматическая привязка к user_id

        if ($request->has('type')) {
            $query->where('type', $request->type); // Фильтр по типу
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));
    }

    public function buyHint(Request $request) {
        $user = $request->user();
        $hintType = $request->input('hint_type');  // 'hint',  maybe in future: '50_50', 'skip_question'
        $questionId = $request->input('question_id');

        // Проверка баланса и списание
        $price = Setting::getValue('hint_price');
        if ($user->balance < $price) {
        //    return response()->json(['error' => 'Недостаточно средств'], 402);

            return ApiResponse::error('insufficient_funds', [
                'current_balance' => $user->balance,
                'required_amount' => $price
            ]);
        }

        DB::transaction(function () use ($user, $hintType, $questionId, $price) {
            $user->decrement('balance', $price);
            Cache::forget("user_{$user->id}_balance");
            $priceToDB = 0 - $price;

            Transaction::create([
                'user_id' => $user->id,
                'amount' => $priceToDB,
                'type' => 'hint_' . $hintType,
                'status' => 'success',
                'metadata' => ['question_id' => $questionId]
            ]);
        });

        return response()->json(['new_balance' => $user->balance]);
    }


}
