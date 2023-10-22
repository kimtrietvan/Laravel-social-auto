<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PinterestController;
use App\Http\Middleware\ValidateCreatePin;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\PinterestRouteController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

//Route::get("/user/{sess}", function (string $sess) {
//    return PinterestController::get_user_data_from_cookie($sess);
//});

Route::get('/user', [PinterestRouteController::class, 'GetUserData']);
Route::get('/user/board', [PinterestRouteController::class, 'GetBoardList']);
Route::post('/user/board/image', [PinterestRouteController::class, 'PostImagePinToBoard']);
Route::post('/user/board/video', [PinterestRouteController::class, 'PostVideoPinToBoard']);


