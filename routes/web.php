<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PinterestController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    print_r(PinterestController::get_board_list( 'TWc9PSZyZ3FZbDZTMnJRWnhWRlZlZ1hMaVZMTjdvcEo3YmMrMGVVTC9OYkpnYUVnUTFpWmFvTEZmZk1YWUpOQmpZOERuZVVoNmMwaGhwcTdVNC83ZFRkVHg1a1JOOFViQ2JvNHZnaDJnb3RTc2NJcW90V3BVb1FOcnZETUdoVW1PWW9YVHRCSEhPRVpmY1pYQnhNZHhoc2pKR1lTWmFRSHBQazlZaWliY2VzbUNlRHV1SXJva0lJZ3dEcXlGM29rVnRjcm54UDVnT1lPMUdvbjJrODVVZ1B3ZkpkeE1lN24wTUJFeVdadENXZkJwamx4bW5TbnJ4b2pUWjV4YmcwNWFCV2FUaW9yVUlRQWxWalFFUmFua1Z2MXV2VUFkNk9SelJZTW5FcktTOHNjL3dYeHZvUGpEMjJOMlZYY2VaMGdsbWlKdUdzN1NpUm8wQTY0WFo0RDE5RnluWS9ES0JiZVFQSEo4cmJrMGZUZnFiMURiV3h6L2lmNFlnWSswSWgydHkvcE5Rc1VjbXlIQVNyeVhJemg0VStLM2NRPT0mR3lrNGdWQ1BvN09GTVFualFFc3F5Zmp0dlowPQ==', 'kimtrietvan'));
});

Route::get('/test1', function () {
    $data = array();
    $data["board_id"] = "1067353249126508421";
    $data["title"] = "Test";
    $data["note"] = "test";
    $data["image_url"] = "https://www.simplilearn.com/ice9/free_resources_article_thumb/what_is_image_Processing.jpg";

    print_r(PinterestController::post_pin_to_board("TWc9PSZyZ3FZbDZTMnJRWnhWRlZlZ1hMaVZMTjdvcEo3YmMrMGVVTC9OYkpnYUVnUTFpWmFvTEZmZk1YWUpOQmpZOERuZVVoNmMwaGhwcTdVNC83ZFRkVHg1a1JOOFViQ2JvNHZnaDJnb3RTc2NJcW90V3BVb1FOcnZETUdoVW1PWW9YVHRCSEhPRVpmY1pYQnhNZHhoc2pKR1lTWmFRSHBQazlZaWliY2VzbUNlRHV1SXJva0lJZ3dEcXlGM29rVnRjcm54UDVnT1lPMUdvbjJrODVVZ1B3ZkpkeE1lN24wTUJFeVdadENXZkJwamx4bW5TbnJ4b2pUWjV4YmcwNWFCV2FUaW9yVUlRQWxWalFFUmFua1Z2MXV2VUFkNk9SelJZTW5FcktTOHNjL3dYeHZvUGpEMjJOMlZYY2VaMGdsbWlKdUdzN1NpUm8wQTY0WFo0RDE5RnluWS9ES0JiZVFQSEo4cmJrMGZUZnFiMURiV3h6L2lmNFlnWSswSWgydHkvcE5Rc1VjbXlIQVNyeVhJemg0VStLM2NRPT0mR3lrNGdWQ1BvN09GTVFualFFc3F5Zmp0dlowPQ==", $data));
});
