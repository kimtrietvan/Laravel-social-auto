<?php

namespace App\Http\Controllers;

use App\Models\PinterestAccount;
use App\Models\PinterestBoard;
use App\Models\User;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Auth;

class QuickShotController extends Controller
{
    public function __construct() {
        $this->middleware(function (Request $request, Closure $next) {
            if ($request->input('pinterest_enable') == '1') {
                if ($request->input('Pinterest_note') === null ) {
                    return redirect()->back()->withErrors('Message can not empty');
                }
                $user = Auth::user();
                $boards = $request->input('Pinterest_board');
                foreach ($boards as $board_id) {
                    $board = PinterestBoard::where('id', $board_id)->first();
                    if ($board === null) {
                        return redirect()->back()->withErrors('Not found pinterest board');

                    }
                    $pinterest_account = PinterestAccount::where('id', $board['pinterest_id'])->first();
                    if ($user['id'] != $pinterest_account['user_id']) {
                        return redirect()->back()->withErrors('Not found pinterest');
                    }
                }
            }




            return $next($request);
        });
    }
    public function index(Request $request) {
        if ($request->input('pinterest_enable') == '1') {
            $boards = $request->input('Pinterest_board');
            foreach ($boards as $board_id) {
                $board = PinterestBoard::where('id', $board_id)->first();
                $pinterest_account = PinterestAccount::where('id', $board['pinterest_id'])->first();
                $cookie = $pinterest_account['cookie'];
                $board_code = $board['board_id'];
                $proxy = $pinterest_account['proxy'] ?? '';
                $title = $request->input('Pinterest_title');
                $note = $request->input('Pinterest_note') ?? '';
                $link = $request->input('Pinterest_link') ?? '';
                if ($request->input('Pinterest_type') == 'Image') {
                    $pinData = array();
                    $pinData['board_id'] = $board_code;
                    $pinData['image_url'] = $request->input('Pinterest_image');
                    $pinData['title'] = $title;
                    $pinData['note'] = $note;
                    $pinData['link'] = $link;
                    PinterestController::post_pin_to_board($cookie, $pinData, $proxy);
                }
            }
        }

        return redirect()->back();
    }
}
