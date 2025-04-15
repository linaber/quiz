<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error('credentials_are_incorrect');
        }

        if ($user?->is_banned) {
            return ApiResponse::error('user_is_banned');
        }


        // Опционально: проверка верификации email
//        if ($user->email_verified_at === null) {
//            return response()->json([
//                'error' => 'Please verify your email address'
//            ], 403);
//        }

        return response()->json([
            'token' => $user->createToken('flutter-token', ['api-access'])->plainTextToken,
            'user' => $user->only(['id', 'name', 'email']),
            'expires_in' => 3600
        ]);
    }



}
