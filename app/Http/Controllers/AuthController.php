<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\RegisterUserResource;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponses;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => Hash::make($request->validated('password'))
            ]);

            $token = $user->createToken(
                $request->header('User-Agent', 'unknown device')
            )->plainTextToken;

            return response()->json(
                ApiResponses::success([
                    'message' => 'ثبت نام با موفقیت انجام شد',
                    'user' => new RegisterUserResource($user),
                    'token' => $token,
                ], 201),
                201
            );
        } catch (\Throwable $e) {
            Log::error('خطای ثبت نام: ' . $e->getMessage());

            return response()->json(
                ApiResponses::error([
                    'message' => 'خطا در ثبت نام. لطفاً مجدداً تلاش کنید',
                ], 422),
                422
            );
        }
    }

    /**
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            if (!Auth::attempt($request->only(['email', 'password']))) {
                throw ValidationException::withMessages(['email' => ['اطلاعات ورود نادرست است']]);
            }

            $user = Auth::user();
            $token = $user->createToken($request->header('User-Agent', 'unknown device'))->plainTextToken;

            return response()->json(
                ApiResponses::success([
                    'message' => 'ورود با موفقیت انجام شد',
                    'user' => new RegisterUserResource($user),
                    'token' => $token,
                ]),
                200
            );
        } catch (ValidationException $e) {
            return response()->json(ApiResponses::validationError($e->errors()), 422);
        } catch (\Exception $e) {
            Log::error('خطای ورود: ' . $e->getMessage());
            return response()->json(ApiResponses::error('خطا در ورود. لطفاً مجدداً تلاش کنید', 500), 500);
        }
    }

    /**
     *
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            request()->user()->currentAccessToken()->delete();
            return response()->json(ApiResponses::success(['message' => 'خروج با موفقیت انجام شد']), 200);
        } catch (\Exception $e) {
            Log::error('خطای خروج: ' . $e->getMessage());
            return response()->json(ApiResponses::unauthenticated(), 401);
        }
    }

    /**
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(ApiResponses::unauthenticated(), 401);
            }
            return response()->json(ApiResponses::success(['user' => new UserResource($user)]), 200);
        } catch (\Exception $e) {
            Log::error('خطای دریافت اطلاعات کاربر: ' . $e->getMessage());
            return response()->json(ApiResponses::unauthenticated(), 401);
        }
    }

    /**
     *
     * @return JsonResponse
     */
    public function refreshToken(): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(ApiResponses::unauthenticated(), 401);
            }

            $user->tokens()->delete();
            $token = $user->createToken(request()->header('User-Agent', 'unknown device'))->plainTextToken;

            return response()->json(
                ApiResponses::success(['message' => 'توکن با موفقیت بازسازی شد', 'token' => $token]),
                200
            );
        } catch (\Exception $e) {
            Log::error('خطای بازسازی توکن: ' . $e->getMessage());
            return response()->json(ApiResponses::error('خطا در بازسازی توکن.', 500), 500);
        }
    }
}
