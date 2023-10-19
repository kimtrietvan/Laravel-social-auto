<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PinterestController;
use App\Http\Middleware\ValidateCreatePin;
use Illuminate\Support\Facades\Http;

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


Route::post("/proxy/check", function (Request $request) {
    $response = Http::withOptions(["proxy" => $request->proxy ?? ""])->get("https://api.ipify.org")->body();
    return $response;
});

Route::get("/test/{sess}", function (string $sess) {
    $response = Http::withHeaders([
        "Cookie" => '_pinterest_sess="' . $sess . '"',
        "X-Requested-With" => "XMLHttpRequest",
        "Referer" => "https://pinterest.com/login/"
    ])->acceptJson()->get("https://www.pinterest.com/resource/HomefeedBadgingResource/get/")->json();
    return $response;
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

    $proxy = $request->proxy ?? "";

    return PinterestController::post_pin_to_board($sess, $data, $proxy);


})->middleware(ValidateCreatePin::class);

