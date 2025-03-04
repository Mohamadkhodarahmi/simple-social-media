<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FollowController extends Controller
{

    public function followers(User $user): AnonymousResourceCollection
    {
        return UserResource::collection($user->followers);
    }

    public function following(User $user): AnonymousResourceCollection
    {
        return UserResource::collection($user->following);
    }

    public function follow(Request $request,User $user): JsonResponse
    {
        auth()->user()->following()->attach($user->id);
        return response()->json(['message' , 'کاربر با موفقیت follow شد ']);

    }

    public function unfollow(Request $request,User $user): JsonResponse
    {
        if (!auth()->user()->following()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'شما این کاربر را follow نکردید'], 400);
        }
        auth()->user()->following()->detach($user->id);
        return response()->json([
            'message' => 'کاربر با موفقیت unfollow شد',
            'user' => new UserResource($user),
            ]);
    }
}
