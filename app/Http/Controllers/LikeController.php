<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikeRequest;
use App\Http\Resources\LikeResource;
use App\Models\Like;
use Illuminate\Http\JsonResponse;


class LikeController extends Controller
{
    public function store(LikeRequest $request): LikeResource|JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if (Like::where($data)->exist()){
            return response()->json(['message' => 'شما این پست را لایک کرده اید'],400);
        }

        $like = Like::create($data);
        return new LikeResource($like->load('user'));
    }

    public function destroy(Like $like): JsonResponse
    {
        $this->authorize('delete' ,$like);
        $like->delete();
        return response()->json(['message' => 'لایک با موفقیت پاک شد']);
    }
    public function likedPostsCount(): JsonResponse
    {
        $count = auth()->user()->likes()->count();
        return response()->json(['liked_posts_count' => $count]);
    }
}
