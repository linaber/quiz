<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function adminTopUp(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1',
        ]);

        $user = User::find($request->user_id);
        $user->increment('balance', $request->amount);

        Transaction::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'type' => 'admin_topup',
            'status' => 'success',
            'is_admin' => true
        ]);

        return response()->json(['new_balance' => $user->balance]);
    }
}
