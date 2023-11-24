<?php

namespace App\Http\Controllers;
use App\Models\WoocommerceProductSync;
use Closure;
use Validator;
use Illuminate\Http\Request;
use Auth;
use App\Models\Woocommerce;
class WoocommerceManagerController extends Controller
{
    public function __construct() {
        $this->middleware(function (Request $request, Closure $next) {
            $validated = Validator::make($request->all(), [
                'cs' => 'required',
                'ck' => 'required',
                'site' => 'required'
            ]);
            if ($validated->fails()) {
                return redirect()->back()->withErrors($validated);
            }
            return $next($request);
        })->except('delete', 'syncSite');
        
    }
    public function add(Request $request) {
        $ck = $request->input('ck');
        $cs = $request->input('cs');
        $base_url = $request->input('site');
        preg_match('/^(https?:\/\/[^\/]+)/', $base_url, $base_url, PREG_OFFSET_CAPTURE);
        $base_url = $base_url[0][0];
        $woo_site = new Woocommerce;
        $woo_site->cs = $cs;
        $woo_site->ck = $ck;
        $woo_site->base_url = $base_url;
        $woo_site->user_id = Auth::id();
        $woo_site->save();
        return redirect()->back();
    }
    public function delete(Request $request) {
        $delete_id = $request->input('delete');
        $woo_sites = Woocommerce::where('user_id', Auth::id());
        $woo_sites->where('id', $delete_id)->delete();
        return redirect()->back();
    }

    public function syncSite(Request $request) {
        $sync_id = $request->input('syncId');
        $site = Woocommerce::where('user_id', Auth::id())->where('id', $sync_id)->first();
        if ($site) {
            $check_not_in_queue = WoocommerceProductSync::where('woocommerce_id', $site->id)->where('finished', 0)->first();
            if (!$check_not_in_queue) {
                $sync_request = new WoocommerceProductSync;
                $sync_request->woocommerce_id = $site->id;
                $sync_request->save();
            }
            
        }
        return redirect()->back();
    }
}
