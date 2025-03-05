<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikeRequest;
use App\Http\Resources\LikeResource;
use App\Http\Responses\ApiResponses;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Store a new like
     *
     * @param LikeRequest $request
     * @return JsonResponse
     */
    public function store(LikeRequest $request): JsonResponse
    {
        try {
            // Start a database transaction
            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                $data['user_id'] = auth()->id();

                // Check if like already exists (use first() for efficiency)
                $existingLike = Like::where($data)->first();
                if ($existingLike) {
                    return response()->json(
                        ApiResponses::validationError([
                            'message' => 'شما قبلاً این پست را لایک کرده‌اید'
                        ]),
                        400
                    );
                }

                // Create like and load relationships in one query
                $like = Like::create($data);
                $like->load('user');

                return response()->json(
                    ApiResponses::success(new LikeResource($like)),
                    201
                );
            });
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Like creation error: ' . $e->getMessage());

            return response()->json(
                ApiResponses::error([
                    'message' => 'خطا در ثبت لایک',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ]),
                422
            );
        }
    }

    /**
     * Remove a like
     *
     * @param Like $like
     * @return JsonResponse
     */
    public function destroy(Like $like): JsonResponse
    {
        try {
            // Authorize the deletion
            $this->authorize('delete', $like);

            // Delete the like
            $like->delete();

            return response()->json(
                ApiResponses::success([
                    'message' => 'لایک با موفقیت حذف شد'
                ]),
                200
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(
                ApiResponses::forbidden([
                    'message' => 'شما مجاز به حذف این لایک نیستید'
                ]),
                403
            );
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Like deletion error: ' . $e->getMessage());

            return response()->json(
                ApiResponses::error([
                    'message' => 'خطا در حذف لایک'
                ]),
                500
            );
        }
    }

    /**
     * Get count of liked posts for authenticated user
     *
     * @return JsonResponse
     */
    public function likedPostsCount(): JsonResponse
    {
        try {
            $user = auth()->user();

            // Early return if user is not authenticated
            if (!$user) {
                return response()->json(
                    ApiResponses::unauthenticated([
                        'message' => 'برای دریافت تعداد لایک‌ها باید وارد شوید'
                    ]),
                    401
                );
            }

            // Get likes count efficiently
            $count = $user->likes()->count();

            return response()->json(
                ApiResponses::success([
                    'liked_posts_count' => $count
                ]),
                200
            );
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Likes count error: ' . $e->getMessage());

            return response()->json(
                ApiResponses::error([
                    'message' => 'خطای سرور رخ داد',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ]),
                500
            );
        }
    }
}
