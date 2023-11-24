<?php

namespace App\Http\Controllers;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\PinterestController;
use App\Models\PinterestAccount;
class DashboardController extends Controller
{
    public function __construct() {
        $this->middleware(function (Request $request, Closure $next) {
            $validator = Validator::make($request->all(), [
                'cookie' => 'required'
            ]);

            if ($validator->fails()) {
                // Throw an exception that will be caught by the exception handler
                return redirect()->back()->withErrors($validator->errors());
            }

            return $next($request);
        })->only(['add_account_pinterest', 'check_account_pinterest']);
        $this->middleware('auth');
    }
    public function add_account_pinterest(Request $request) {
        $response = PinterestController::get_user_data_from_cookie($request->input('cookie'), $request->input('proxy') ?? '');
        if ($response->status() == 200) {
            $user_data = $response->json();
            if ($user_data['resource_response']['status'] == 'success' && $user_data['resource_response']['message'] == 'ok') {
                $user_info = array();
                $user_info['username'] = $user_data['client_context']['user']['username'];
                $user_info['id'] = $user_data['client_context']['user']['id'];
                $user_info['full_name'] = $user_data['client_context']['user']['full_name'];
                $user_info['email'] = $user_data['client_context']['user']['email'];
            }
        }

        if (PinterestAccount::where('username', '=', $user_info['username'])->first() !== null) {
            return redirect()->back()->withErrors(['error' => "This account in records"]);
        }

        PinterestAccount::create([
            'username' => $user_info['username'],
            'cookie' => $request->input('cookie'),
            'proxy' => $request->input('proxy'),
            'user_id' => Auth::user()['id']
        ]);

        return redirect()->back();

//        return response($response->body() ?? "Error", $response->status());
    }

    public function check_account_pinterest(Request $request) {
        $response = PinterestController::get_user_data_from_cookie($request->input('cookie'), $request->input('proxy') ?? '');
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

    public function delete_account_pinterest(Request $request) {
        $pin_id_be_delete = $request->input('deletePinterest');
        $user_id = Auth::user()['id'];
        $account_pinterest_check = PinterestAccount::where('id', '=', $pin_id_be_delete)->first();
        if ($account_pinterest_check === null) {
            return redirect()->back()->withErrors(['error' => 'Account pinterest id not found']);
        }
        if ($account_pinterest_check['user_id'] !== $user_id) {
            return redirect()->back()->withErrors(['error' => 'Account pinterest id not found']);
        }
        $account_pinterest_check->delete();
        return redirect()->back();
    }

}
