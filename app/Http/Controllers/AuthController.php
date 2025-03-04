<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'کاربر با موفقیت ثبت نام کرد',
            'user' => new UserResource($user),
            'token' => $token
        ],201);

    }

    public function login(LoginRequest $request): void
    {
        $user = User::where('email' , $request->email)->first();

        if (!$user || !Hash::check($request->password , $user->password)){
            throw ValidationException::withMessages([
               'email' => ['اطلاعات نامعتبر ']
            ]);
        }
    }

    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'با موفقیت خارج شدید'
        ]);
    }
}
