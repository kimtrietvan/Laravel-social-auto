<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;
use App\Models\PinterestAccount;
use App\Models\PinterestBoard;
use App\Http\Controllers\PinterestController;
use App\Models\Woocommerce;
use App\Models\WoocommerceProduct;
use App\Models\WoocommerceProductImage;
use App\Models\WoocommerceProductSync;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
//            PinterestBoard::query()->delete();
            $accounts = PinterestAccount::all();
            foreach ($accounts as $account) {
                $cookie = $account['cookie'];
                $username = $account['username'];
                $proxy = $account['proxy'] ?? '';
                $response = PinterestController::get_board_list($cookie, $username, $proxy);
                if ($response->status() == 200) {
                    $user_data = $response->json();
                    if ($user_data['resource_response']['status'] == 'success' && $user_data['resource_response']['message'] == 'ok') {
                        $board_list = $user_data['resource_response']['data'];
                        foreach (PinterestBoard::where('pinterest_id', '=', $account['id'])->get() as $pinterest_db) {
                            if (in_array($pinterest_db['board_id'], array_column($board_list, 'id'))) {
                                continue;
                            }
                            else {
                                PinterestBoard::where('board_id', $pinterest_db['board_id'])->delete();
                            }
                        }
                        foreach ($board_list as $board) {
                            if (PinterestBoard::where('board_id', '=', $board['id'])->first() !== null) {
                                $been_update_board = PinterestBoard::where('board_id', '=', $board['id'])->first();
                                if ($been_update_board->board_name != $board['name']) {
                                    $been_update_board->board_name = $board['name'];
                                    $been_update_board->save();
                                }
                                continue;
                            }
                            PinterestBoard::create([
                                'pinterest_id' => $account['id'],
                                'board_id' => $board['id'],
                                'board_name' => $board['name']
                            ]);
                        }
                    }
                }

            }
        })->everyThirtySeconds();


        $schedule->call(function () {

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
                                $product = WoocommerceProduct::where('product_id', $delete_product)->where(['woocommerce_id', $site->id])->first();
                                if ($product) $product->delete();
                            }
                        }
                        foreach ($remote_products as $product) {
                            $product_result = WoocommerceProduct::where("product_code", $product['id'])->where('woocommerce_id', $site->id)->first();
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
        })->everySecond();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
