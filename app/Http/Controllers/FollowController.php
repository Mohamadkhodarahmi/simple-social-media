<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    /**
     * Get the list of followers for a specific user.
     *
     * @param User $user
     * @return AnonymousResourceCollection
     */
    public function followers(User $user): AnonymousResourceCollection
    {
        return UserResource::collection($user->followers);
    }

    /**
     * Get the list of users a specific user is following.
     *
     * @param User $user
     * @return AnonymousResourceCollection
     */
    public function following(User $user): AnonymousResourceCollection
    {
        return UserResource::collection($user->following);
    }

    /**
     * Follow a user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function follow(User $user): JsonResponse
    {
        $currentUser = Auth::user();

        // Check if already following to prevent duplicate entries
        if ($currentUser->following()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'message' => 'شما از قبل این کاربر را follow کرده‌اید'
            ], 400);
        }

        $currentUser->following()->attach($user->id);

        return response()->json([
            'message' => 'کاربر با موفقیت follow شد',
            'user' => new UserResource($user)
        ]);
    }

    /**
     * Unfollow a user.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function unfollow(User $user): JsonResponse
    {
        $currentUser = Auth::user();

        if (!$currentUser->following()->where('users.id', $user->id)->exists()) {
            return response()->json([
                'message' => 'شما این کاربر را follow نکردید'
            ], 400);
        }

        $currentUser->following()->detach($user->id);

        return response()->json([
            'message' => 'کاربر با موفقیت unfollow شد',
            'user' => new UserResource($user)
        ]);
    }
}
