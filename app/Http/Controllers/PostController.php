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
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request): PostResource
    {
        $post= auth()->user()->posts()->create($request->validated());
        return new PostResource($post->load('user','like','comments'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($post->load('user','likes','comments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request,Post $post): PostResource
    {
        $this->authorize('update',$post);
        $post->update($request->validated());
        return new PostResource($post->load('user','likes','comments'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('delete',$post);
        $post->delete();
        return response()->json(['message' => 'پست با موفقیت حذف شد']);
    }
}
