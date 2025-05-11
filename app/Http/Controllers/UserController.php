<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function login(LoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // guard 'api' не имеет метода attempt
        if (!Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) {
            return response()->json([
                'message' => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // если мы проверяли пользователя через guard 'web' - получаем его также через guard 'web'
        $user = Auth::guard('web')->user();

        $token = $user->createToken($email);

        return response()->json([
            'token' => $token->plainTextToken,
        ]);
    }
}
