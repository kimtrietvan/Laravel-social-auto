<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PinterestController;
use App\Http\Middleware\ValidateCreatePin;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("/user/{sess}", function (string $sess) {
    return PinterestController::get_user_data_from_cookie($sess);
});

Route::get("/user/{sess}/board/", function (string $sess) {
    $user_data = PinterestController::get_user_data_from_cookie($sess);
    return PinterestController::get_board_list($sess, $user_data["username"]);
});

Route::post("/user/board/create", function (Request $request) {
    $data = array();
    $sess = $request->sess;
    $data["board_id"] = $request->board_id;
    $data["image_url"] = $request->image_url ?? "";
    $data["video_url"] = $request->video_url ?? "";
    $data["title"] = $request->title ?? "";
    $data["description"] = $request->description ?? "";
    $data["link"] = $request->link ?? "";
    $data["alt_text"] = $request->alt_text ?? "";
    return PinterestController::post_pin_to_board($sess, $data);


})->middleware(ValidateCreatePin::class);
