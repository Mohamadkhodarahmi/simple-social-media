<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Responses\ApiResponses;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller

{
    use AuthorizesRequests;
    /**
     * create a comment
     *
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function store(CommentRequest $request): JsonResponse
    {
        try {
            $comment = auth()->user()->comments()->create($request->validated());
            return response()->json(ApiResponses::success(new CommentResource($comment->load('user'))), 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(ApiResponses::validationError($e->errors()), 422);
        } catch (\Exception $e) {
            return response()->json(ApiResponses::error('خطای سرور رخ داد.', 500, ['error' => $e->getMessage()]), 500);
        }
    }
    /**
     * delete a comment
     *
     * @param Comment $comment
     * @return JsonResponse
     */
    public function destroy(Comment $comment): JsonResponse
    {
        try {
            $this->authorize('delete', $comment);
            $comment->delete();
            return response()->json(ApiResponses::success(['message' => 'کامنت با موفقیت حذف شد']), 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(ApiResponses::forbidden(), 403);
        } catch (\Exception $e) {
            return response()->json(ApiResponses::notFound(), 404);
        }
    }
}
