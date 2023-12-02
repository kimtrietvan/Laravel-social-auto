<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Closure;
use App\Models\PinterestBoard;
use App\Models\PinterestAccount;
use App\Models\ScheduleWoocommerceProduct;
use App\Models\ScheduleTask as ScheduleTaskModel;

class ScheduleTask extends Controller
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

        $this->middleware(function (Request $request, Closure $next) {
            if ($request->input('minute') == '' && $request->input('hours') == '' && $request->input('day') == '' && $request->input('month') == '' && $request->input('year') == '') {
                return redirect('/close');
            }
            return $next($request);
        })->only('create');
    }
    public function create(Request $request) {
        if ($request->input('pinterest_enable') == '1') {
            $boards = $request->input('Pinterest_board');
            foreach ($boards as $board_id) {
                $board = PinterestBoard::where('id', $board_id)->first();
                $pinterest_account = PinterestAccount::where('id', $board['pinterest_id'])->first();
                // $cookie = $pinterest_account['cookie'];
                $title = $request->input('Pinterest_title') ?? '';
                $expression = $request->input('minute') . ' ' . $request->input('hours'). ' ' . $request->input('day') . ' ' . $request->input('month') . ' ' . $request->input('year');
                $note = $request->input('Pinterest_note') ?? '';
                $link = $request->input('Pinterest_link') ?? '';
                $product_id = $request->input('product_id');
                $mediaType = $request->input('Pinterest_type') ?? 'Image';
                if ($mediaType != 'Image' && $mediaType != 'Video')
                    return redirect()->back()->withErrors('Media type wrong');
                $new_schedule_woocommerce = new ScheduleWoocommerceProduct;
                $new_schedule_woocommerce->product_id = $product_id;
                $new_schedule_woocommerce->message = $note;
                $new_schedule_woocommerce->links = $link;
                $new_schedule_woocommerce->mediaType = $mediaType;
                $new_schedule_woocommerce->accountType = 'Pinterest';
                $new_schedule_woocommerce->account_id = $pinterest_account->id;
                $new_schedule_woocommerce->board_id = $board->board_id;
                $new_schedule_woocommerce->title = $title;
                $new_schedule_woocommerce->save();
                $new_schedule_model = new ScheduleTaskModel;
                $new_schedule_model->type = 'Woocommerce_Product';
                $new_schedule_model->task_id = $new_schedule_woocommerce->id;
                $new_schedule_model->expression = $expression;
                $new_schedule_model->save();

                

            }
        }
        return redirect('/close');
    }
}
