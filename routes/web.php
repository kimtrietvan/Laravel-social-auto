<?php

use App\Models\WoocommerceProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PinterestController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Models\Woocommerce;
use App\Models\WoocommerceProductImage;
use App\Models\WoocommerceProductSync;
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


Route::get("/login", function () {
    if (Auth::check()) {
        return redirect(route('dashboard'));
    }
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', function () {
    if (Auth::check()) {
        return redirect(route('dashboard'));
    }
    return view('register');
})->name('register');

Route::post('/register', [LoginController::class, 'register']);

Route::get('/', function () {
    if (Auth::check()) {
        return redirect(route('dashboard'));
    }
    return redirect(\route('login'));
});

Route::get('/dashboard', function () {
    return view('user_dashboard');
})->middleware('auth')->name('dashboard');

Route::match(['get', 'post'], '/logout', function () {
    Auth::logout();
    return redirect()->route('login');
});

Route::get('/dashboard/pinterest/add', function () {
    return view('add_pinterest_account');
})->middleware('auth')->name('pinterest_add_account');

Route::post('/dashboard/pinterest/add', [DashboardController::class, 'add_account_pinterest']);
Route::post('/dashboard/pinterest/check', [DashboardController::class, 'check_account_pinterest'])->name('check_account_pinterest');

Route::prefix('/dashboard')->middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('user_dashboard');
    })->middleware('auth')->name('dashboard');
    Route::post('/pinterest/add', [DashboardController::class, 'add_account_pinterest']);
    Route::post('/pinterest/check', [DashboardController::class, 'check_account_pinterest'])->name('check_account_pinterest');

    Route::get('/pinterest/manager', function () {
        return view('dashboard_pinterest_manager');
    })->middleware('auth')->name("pinterest_account_manager");
    Route::post('/pinterest/delete', [DashboardController::class, 'delete_account_pinterest']);
    Route::post('/quick_shot', [\App\Http\Controllers\QuickShotController::class, 'index']);

    Route::get('/woocommerce', function () {
        $id = Auth::id();
        $woocommerce_data = Woocommerce::where('user_id', $id)->get();
        return view('dashboard_woocommerce', ['data' => $woocommerce_data]);
    })->name('woocommerce_manager');
    Route::get('/woocommerce/add', function () {
        return view('dashboard_woocommerce_add');
    })->name('woocommerce_add_site');

    Route::post('/woocommerce/add', [\App\Http\Controllers\WoocommerceManagerController::class, 'add']);
    Route::post('/woocommerce/delete', [\App\Http\Controllers\WoocommerceManagerController::class, 'delete'])->name('woocommerce_delete_site');
    Route::get('/woocommerce/products', function () {
        $woocommerce_data = Woocommerce::where("user_id", Auth::id())->get();
        return view('woocommerce_products', ['data' => $woocommerce_data]);
    })->name('woocommerce_product');
    Route::post('/woocommerce/sync', [\App\Http\Controllers\WoocommerceManagerController::class,'syncSite'])->name('woocommerce_sync_site');
});
Route::get('/test', function () {

    $sync_request = WoocommerceProductSync::where('finished', 0)->get();
            foreach ($sync_request as $request) {
                $site = Woocommerce::where('id', $request->woocommerce_id)->first();
                // return print_r($site);
                $base_url = $site->base_url;
                $cs = $site->cs;
                $ck = $site->ck;
                $page = 1;
                $remote_products = [];
                do {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic '. base64_encode($ck.":".$cs)
                ])->get($base_url . "/wp-json/wc/v3/products?per_page=100&page=".$page)->json();
                if (count($response) > 0)
                $remote_products = array_merge($remote_products, $response);
                $page++;
                } while (count($response) > 0);

                if (WoocommerceProduct::where('woocommerce_id', $site->id)->first()) {
                    $products = [];
                    foreach (WoocommerceProduct::where('woocommerce_id', $site->id)->get() as $product) {
                        array_push($products, $product);
                    }
    
                    $local_products_id = array_column($products, 'product_code');
                    $remote_products_id = array_column($remote_products, 'id');
                    $delete_products = array_diff($local_products_id, $remote_products_id);
                    foreach ($delete_products as $delete_product) {
                        $product = WoocommerceProduct::where(['product_id', $delete_product], ['woocommerce_id', $site->id])->first();
                        if ($product) $product->delete();
                    }
                }
                foreach ($remote_products as $product) {
                    $product_result = WoocommerceProduct::where(["product_code", $product['id']], ['woocommerce_id', $site->id])->first();
                    if (!$product_result) {
                        $new_product = new WoocommerceProduct;
                        $new_product->product_code = $product['id'];
                        $new_product->product_name = $product['name'];
                        $new_product->product_url = $product['permalink'];
                        $new_product->woocommerce_id = $site->id;
                        $new_product->save();
                        foreach ($product['images'] as $image) {
                            $images = new WoocommerceProductImage;
                            $images->product_image = $image['src'];
                            $images->product_id = $new_product->id;
                            $images->save();
                        }
                    }
                    }
                $request->finished = 1;
                $request->save();
            }
});


