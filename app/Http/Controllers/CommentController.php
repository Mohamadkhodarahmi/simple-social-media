<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Add a comment to a post
     *
     * Allows an authenticated user to comment on an existing post.
     *
     * @bodyParam post_id integer required The ID of the post to comment on. Example: 1
     * @bodyParam content string required The comment text. Example: Great post!
     *
     * @response 201 {
     *   "id": 1,
     *   "content": "Great post!",
     *   "user": {"id": 1, "name": "Ali", "email": "ali@example.com"},
     *   "post_id": 1,
     *   "created_at": "just now"
     * }
     */
    public function store(CommentRequest $request): CommentResource
    {
        $comment = auth()->user()->comments()->create($request->validated());
        return new CommentResource($comment->load('user'));
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete',$comment);
        $comment->delete();
        return response()->json(['message' => 'کامنت با موفقیت حذف شد']);
    }
}
