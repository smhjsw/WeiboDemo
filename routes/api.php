<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\FriendController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('users/register', [UserController::class, 'register']);

Route::post('users/login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('posts', [PostController::class, 'store']);
    Route::get('posts', [PostController::class, 'index']);
    Route::get('posts/following', [PostController::class, 'following']);

    Route::get('friends/posts', [FriendController::class, 'posts']);

    Route::post('friends/{friend_id}/follow', [FriendController::class, 'follow']);
    Route::post('friends/{friend_id}/unfollow', [FriendController::class, 'unfollow']);
    Route::get('friends/{friend_id}/posts', [FriendController::class, 'posts']);
});

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
