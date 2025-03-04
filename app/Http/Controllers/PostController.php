<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    /**
     * @Index
     */
    public function index(): AnonymousResourceCollection
    {
        $posts = Post::with('user' , 'like' , 'comments')->latest()->get();
        return PostResource::collection($posts);
    }

    /**
     * Create a new post
     *
     * Allows an authenticated user to create a post with optional file attachment.
     *
     * @bodyParam content string required The content of the post. Example: This is my first post!
     * @bodyParam file file optional An image or file attachment (jpg, png, pdf, max 2MB).
     *
     * @response 201 {
     *   "id": 1,
     *   "content": "This is my first post!",
     *   "user": {"id": 1, "name": "Ali", "email": "ali@example.com"},
     *   "likes_count": 0,
     *   "comments_count": 0,
     *   "attachments": [{"id": 1, "file_path": "attachments/file.jpg", "file_type": "image/jpeg"}],
     *   "created_at": "just now"
     * }
     */
    public function store(PostRequest $request): PostResource
    {
        $post= auth()->user()->posts()->create($request->only('content'));

        if ($request->hasFile('file')){
            $file = $request->file('file');
            $path = $file->store('attachments' , 'public');
            $post->attahcments()->create([
                'file_path' => $path,
                'file_type' => $file->getClientMimeType()
            ]);
        }
        return new PostResource($post->load('user','like','comments','attachments'));
    }

    /**
     * @show
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($post->load('user','likes','comments'));
    }

    /**
     * @update
     */
    public function update(PostRequest $request,Post $post): PostResource
    {
        $this->authorize('update',$post);
        $post->update($request->validated());
        return new PostResource($post->load('user','likes','comments'));
    }

    /**
     * @destroy
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete',$post);
        $post->delete();
        return response()->json(['message' => 'پست با موفقیت حذف شد']);
    }
}
