<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterUserResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * Creates a new user account and returns an authentication token.
     *
     * @bodyParam name string required The name of the user. Example: Ali
     * @bodyParam email string required The email address of the user. Must be unique. Example: ali@example.com
     * @bodyParam password string required The password (minimum 8 characters). Example: password123
     * @bodyParam password_confirmation string required Must match the password.
     *
     * @response 201 {
     *   "message": "User registered successfully",
     *   "user": {"id": 1, "name": "Ali", "email": "ali@example.com"},
     *   "token": "1|random-token"
     * }
     */
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
            'user' => new RegisterUserResource($user),
            'token' => $token
        ],201);

    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email' , $request->email)->first();

        if (!$user || !Hash::check($request->password , $user->password)){
            throw ValidationException::withMessages([
               'email' => ['اطلاعات نامعتبر ']
            ]);
        }
        $token = $user->createToken('auth-token')->plainTextToken;
        return response()->json([
            'message' => 'کاربر با موفقیت وارد شد',
            'user' => new RegisterUserResource($user),
            'token' => $token
        ],201);

    }

    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'با موفقیت خارج شدید'
        ]);
    }
}
