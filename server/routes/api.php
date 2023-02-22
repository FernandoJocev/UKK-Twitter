<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Main\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'prefix' => 'auth',
    'middleware' => 'api',
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::get('getUsers', [AuthController::class, 'getUsers']);
    Route::get('getUser/{token}', [AuthController::class, 'getUser']);
    Route::get('getTweets/{token}', [AuthController::class, 'getTweets']);
    Route::post('editProfile', [AuthController::class, 'updateProfile']);
});

Route::group([
    'prefix' => 'main',
    'middleware' => 'api',
], function () {
    Route::post('post', [PostController::class, 'createPost']);
    Route::get('getComments/{id}', [PostController::class, 'getComments']);
    Route::get('getEditComment/{id}', [PostController::class, 'getEditComment']);
    Route::post('updateComment', [PostController::class, 'updateComment']);
    Route::post('/deleteComment/{id}', [PostController::class, 'deleteComment']);
    Route::get('getPost/{id}', [PostController::class, 'getPost']);
    Route::post('updatePost/{id}', [PostController::class, 'updatePost']);
    Route::post('deletePost/{id}', [PostController::class, 'deletePost']);
    Route::post('comment/{id}', [PostController::class, 'comment']);
    Route::get('/search', [PostController::class, 'searchPost']);
});
