<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Http\Responses\ApiResponses;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display all Posts
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $posts = Post::with('user', 'like', 'comments', 'attachments')
            ->latest()
            ->paginate(20);
        return response()->json(ApiResponses::success(PostResource::collection($posts)));
    }
    /**
     * create a Post
     *
     * @param PostRequest $request
     * @return JsonResponse
     */

    public function store(PostRequest $request): JsonResponse
    {
        try {
            $post = auth()->user()->posts()->create($request->only('content'));

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('attachments', 'public');
                $post->attachments()->create([
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                ]);
            }


            $post->load(['user', 'like', 'comments', 'attachments']);

            $resource = new PostResource($post);
            if (json_encode($resource) === false) {
                throw new \Exception('خطا در تبدیل داده به JSON');
            }

            return response()->json(
                ApiResponses::success($resource),
                201
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('خطای اعتبارسنجی در store پست: ' . $e->getMessage());
            return response()->json(ApiResponses::validationError($e->errors()), 422);
        } catch (\Illuminate\Database\Eloquent\RelationNotFoundException $e) {
            Log::error('خطای رابطه در store پست: ' . $e->getMessage());
            return response()->json(ApiResponses::error('خطا در بارگذاری روابط.', 500, ['error' => $e->getMessage()]), 500);
        } catch (\Exception $e) {
            Log::error('خطا در store پست: ' . $e->getMessage());
            return response()->json(ApiResponses::error('خطای سرور رخ داد.', 500, ['error' => $e->getMessage()]), 500);
        }
    }

    /**
     * Display a single post
     *
     * @param Post $post The post to retrieve
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        try {
            if (!$post) {
                return response()->json(ApiResponses::notFound(), 404);
            }
            return response()->json(ApiResponses::success(new PostResource($post->load('user', 'likes', 'comments'))), 200);
        } catch (\Exception $e) {
            return response()->json(ApiResponses::error('خطای سرور رخ داد.', 500, ['error' => $e->getMessage()]), 500);
        }
    }

    /**
     * update
     * @return JsonResponse
     */
    public function update(PostRequest $request, Post $post): JsonResponse
    {
        try {
            $this->authorize('update', $post);

            $validatedData = $request->validated();
            Log::info('Validated data: ', $validatedData); // دیباگ
            if (!isset($validatedData['content']) || trim($validatedData['content']) === '') {
                return response()->json(
                    ApiResponses::validationError(['content' => ['محتوا الزامی است.']]),
                    422
                );
            }

            $post->update($validatedData);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $path = $file->store('attachments', 'public');
                $post->attachments()->create([
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                ]);
            }

            return response()->json(
                ApiResponses::success(new PostResource($post->load('user', 'like', 'comments'))),
                200
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('خطای اعتبارسنجی در update پست: ' . $e->getMessage());
            return response()->json(ApiResponses::validationError($e->errors()), 422);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(ApiResponses::forbidden(), 403);
        } catch (\Exception $e) {
            Log::error('خطا در update پست: ' . $e->getMessage());
            return response()->json(ApiResponses::error('خطای سرور رخ داد.', 500, ['error' => $e->getMessage()]), 500);
        }
    }

    /**
     * @destroy
     */
    public function destroy($id): JsonResponse
    {
        try {
            $post = Post::find($id);
            if (!$post) {
                return response()->json(ApiResponses::notFound(), 404);
            }

            $this->authorize('delete', $post);
            $post->delete();
            return response()->json(ApiResponses::success(['message' => 'پست با موفقیت حذف شد']), 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json(ApiResponses::forbidden(), 403);
        } catch (\Exception $e) {
            return response()->json(ApiResponses::error('خطای سرور رخ داد.', 500, ['error' => $e->getMessage()]), 500);
        }
    }
}
