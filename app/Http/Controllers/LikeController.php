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
     * like a post
     *
     * @param LikeRequest $request
     * @return JsonResponse
     */
    public function store(LikeRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                $data['user_id'] = auth()->id();


                if (Like::where($data)->exists()) {
                    return response()->json(
                        ApiResponses::validationError([
                            'message' => 'شما قبلاً این پست را لایک کرده‌اید'
                        ]),
                        400
                    );
                }

                $like = Like::create($data);

                return response()->json(
                    ApiResponses::success(new LikeResource($like), 201,'شما با موفقیت لایک کردید'),
                    201
                );
            });
        } catch (\Exception $e) {
            Log::error('Like creation error: ' . $e->getMessage());

            return response()->json(
                ApiResponses::error([
                    'message' => 'خطا در ثبت لایک',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ],500),
                500
            );
        }
    }

    /**
     * delete a like
     *
     * @param Like $like
     * @return JsonResponse
     */
    public function destroy(Like $like): JsonResponse
    {
        try {
            $this->authorize('delete', $like);
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
            Log::error('Like deletion error: ' . $e->getMessage());

            return response()->json(
                ApiResponses::error([
                    'message' => 'خطا در حذف لایک'
                ],500),
                500
            );
        }
    }

    /**
     * liked posts count
     *
     * @return JsonResponse
     */
    public function likedPostsCount(): JsonResponse
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(
                    ApiResponses::unauthenticated([
                        'message' => 'برای دریافت تعداد لایک‌ها باید وارد شوید'
                    ]),
                    401
                );
            }

            $count = $user->likes()->count();

            return response()->json(
                ApiResponses::success([
                    'liked_posts_count' => $count
                ]),
                200
            );
        } catch (\Exception $e) {
            Log::error('Likes count error: ' . $e->getMessage());

            return response()->json(
                ApiResponses::error([
                    'message' => 'خطای سرور رخ داد',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ],500),
                500
            );
        }
    }
}
