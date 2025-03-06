<?php

namespace Tests\Feature;

use App\Http\Responses\ApiResponses;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user can create a post.
     */
    public function test_authenticated_user_can_create_post()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts', [
            'content' => 'This is a test post',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'content',
                    'user_id',
                    'user_name',
                    'likes_count',
                    'attachments',
                    'created_at',
                ],
                'status',
            ]);

        $this->assertDatabaseHas('posts', [
            'content' => 'This is a test post',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test unauthenticated user cannot create a post.
     */
    public function test_unauthenticated_user_cannot_create_post()
    {
        $response = $this->postJson('/api/posts', [
            'content' => 'This is a test post',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test authenticated user can update own post.
     */
    public function test_authenticated_user_can_update_own_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/posts/{$post->id}", [
            'content' => 'Updated content here',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'content',
                    'user_id',
                    'user_name',
                    'likes_count',
                    'attachments',
                    'created_at',
                ],
                'status',
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'content' => 'Updated content here',
        ]);
    }

    /**
     * Test authenticated user cannot update another user's post.
     */
    public function test_authenticated_user_cannot_update_another_users_post()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2, 'sanctum')->putJson("/api/posts/{$post->id}", [
            'content' => 'Unauthorized update',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test authenticated user can delete own post.
     */
    public function test_authenticated_user_can_delete_own_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['message'],
                'status',
            ]);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /**
     * Test authenticated user cannot delete another user's post.
     */
    public function test_authenticated_user_cannot_delete_another_users_post()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user1->id]);

        $response = $this->actingAs($user2, 'sanctum')->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(403);
    }
}
