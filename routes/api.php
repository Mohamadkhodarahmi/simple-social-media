<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function (){
    Route::post('/logout',[AuthController::class,'logout']);
    Route::apiResource('posts', PostController::class);
    Route::post('/comments',[CommentController::class,'store']);
    Route::delete('/comments/{comment}',[CommentController::class,'destroy']);
    Route::post('/likes',[LikeController::class,'store']);
    Route::delete('/likes/{like}',[LikeController::class,'destroy']);
    Route::get('/liked-posts/count',[LikeController::class,'likedPostsCount']);
    Route::post('/follow/{user}',[FollowController::class,'follow']);
    Route::post('/unfollow/{user}',[FollowController::class,'unfollow']);
    Route::get('/users/{user}/followers',[FollowController::class,'followers']);
    Route::get('/users/{user}/following ',[FollowController::class,'following']);
});
