<?php

namespace App\Http\Controllers;
use App\Http\Controllers\PinterestController;
use App\Http\Controllers\TempFileController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class PinterestRouteController extends Controller
{
    public function __construct() {
        $this->middleware(function (Request $request, Closure $next) {
            $validate = Validator::make($request->all(), [
                'cookie' => 'required'
            ]);
            if ($validate->fails()) {
                return response()->json($validate->errors());
            }
            return $next($request);
        });
        // Get board list middleware

        $this->middleware(function (Request $request, Closure $next) {
            $validate = Validator::make($request->all(), [
                'username' => 'required'
            ]);
            if ($validate->fails()) {
                    return response()->json($validate->errors());
                }
                return $next($request);
        })->only('GetBoardList');
        // Post pin to board middleware
        $this->middleware(function (Request $request, Closure $next) {
            $validate = Validator::make($request->all(), [
               'board_id' => 'required',
               'image_url' => 'required',
               'title' => 'required'
            ]);
            if ($validate->fails()) {
                return response()->json($validate->errors());
            }
            return $next($request);
        })->only('PostImagePinToBoard');

        $this->middleware(function (Request $request, Closure $next) {
            $validate = Validator::make($request->all(), [
                'file' => 'required|file',
                'board_id' => 'required'
            ]);
            if ($validate->fails()) {
                return response()->json($validate->errors());
            }
            return $next($request);
        })->only('PostVideoPinToBoard');

    }
    public function GetUserData(Request $request) {
        $cookie = $request->input('cookie');
        $proxy = $request->input('proxy') ?? "";
        $response = PinterestController::get_user_data_from_cookie($cookie, $proxy);
        if ($response->status() == 200) {
            $user_data = $response->json();
            if ($user_data['resource_response']['status'] == 'success' && $user_data['resource_response']['message'] == 'ok') {
                $user_info = array();
                $user_info['username'] = $user_data['client_context']['user']['username'];
                $user_info['id'] = $user_data['client_context']['user']['id'];
                $user_info['full_name'] = $user_data['client_context']['user']['full_name'];
                $user_info['email'] = $user_data['client_context']['user']['email'];
                return response($user_info, 200);
            }
        }
        return response($response->body() ?? "Error", $response->status());
    }

    public function GetBoardList(Request $request) {
        $cookie = $request->input("cookie");
        $username = $request->input('username');
        $proxy = $request->input('proxy') ?? '';
        $response = PinterestController::get_board_list($cookie, $username, $proxy);
//        return $response->json();
        if ($response->status() == 200) {
            $user_data = $response->json();
            if ($user_data['resource_response']['status'] == 'success' && $user_data['resource_response']['message'] == 'ok') {
                $board_list = $user_data['resource_response']['data'];
                return response($board_list, 200);
            }
        }
        return response($response->body() ?? "Error", $response->status());
    }

    public function PostImagePinToBoard(Request $request) {
        $cookie = $request->input('cookie');
        $pinData = array();
        $pinData['board_id'] = $request->input('board_id');
        $pinData['image_url'] = $request->input('image_url');
        $pinData['title'] = $request->input('title');
        $pinData['alt_text'] = $request->input('alt_text') ?? '';
        $pinData['note'] = $request->input('note') ?? '';
        $pinData['link'] = $request->input('link') ?? '';
        $proxy = $request->input('proxy') ?? '';
        $response = PinterestController::post_pin_to_board($cookie, $pinData, $proxy);
        return $response->json();
    }

    public function PostVideoPinToBoard(Request $request) {
        $cookie = $request->input("cookie");
        $board_id = $request->input("board_id");
        $video_name = randomID().'.'.$request->file('file')->extension();
        TempFileController::SaveAs($video_name, $request->file('file'));
        $register_video_data = PinterestController::register_upload_video($cookie, $video_name);
        $register_video_id = array_keys($register_video_data['resource_response']['data'])[0];
        $upload_video_parameters = $register_video_data['resource_response']['data'][$register_video_id]['upload_parameters'];
        $upload_video_url = $register_video_data['resource_response']['data'][$register_video_id]['upload_url'];
        $upload_video_id = $register_video_data['resource_response']['data'][$register_video_id]['upload_id'];
        $upload_video_response = PinterestController::upload_video($upload_video_url, $upload_video_parameters, $video_name);

        $register_image_data = PinterestController::register_upload_image($cookie, $video_name);
        $register_image_id = array_keys($register_image_data['resource_response']['data'])[0];
        $upload_image_parameters = $register_image_data['resource_response']['data'][$register_image_id]['upload_parameters'];
        $upload_image_url = $register_image_data['resource_response']['data'][$register_image_id]['upload_url'];
        $upload_image_id = $register_image_data['resource_response']['data'][$register_image_id]['upload_id'];
        $upload_image_response = PinterestController::upload_image($upload_image_url, $upload_image_parameters, $video_name);
        sleep(2);
        $video_status = PinterestController::get_status_of_id($cookie, $upload_video_id);
        $image_status = PinterestController::get_status_of_id($cookie, $upload_image_id);
        $video_signature = $video_status['resource_response']['data'][$upload_video_id]['signature'];
        $image_signature = $image_status['resource_response']['data'][$upload_image_id]['signature'];
        $pinData = array();
        $pinData['board_id'] = $request->input('board_id');
        $pinData['note'] = $request->input('note') ?? "";
        $pinData['link'] = $request->input('link') ?? "";
        $pinData['title'] = $request->input('title');
        $pinData['image_signature'] = $image_signature;
        $pinData['video_id'] = $upload_video_id;
        $pinData['video_signature'] = $video_signature;
        return PinterestController::create_story_pinterest($cookie, $pinData, $video_name);
    }
}
