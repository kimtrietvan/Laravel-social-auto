<?php

namespace App\Http\Controllers;
use App\Http\Controllers\PhpFFmpegController;
use App\Http\Controllers\TempFileController;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use FFMpeg\Coordinate\TimeCode;
class PinterestController extends Controller
{
    /**
     * @throws \Exception
     */
    public static function get_board_list_temp($sess, $username): array|string {
        $ch = curl_init();
        $apiUrl = "https://www.pinterest.com/resource/BoardsResource/get/?source_url=%2F".$username."%2F&data=%7B%22options%22%3A%7B%22privacy_filter%22%3A%22all%22%2C%22sort%22%3A%22last_pinned_to%22%2C%22username%22%3A%22".$username."%22%7D%2C%22context%22%3A%7B%7D%7D";
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        $headers = array();
        $headers[] =  'Cookie: csrftoken='.bin2hex(random_bytes(32)) .'; _auth=1; _pinterest_sess='.$sess.';';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode == 200) {
            $data = json_decode($result, true);
            curl_close($ch);
            return $data["resource_response"]["data"];
        }
        else {
            return "Error";
        }
    }

    public static function get_board_list($sess, $username, $proxy): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response {
        $apiUrl = "https://www.pinterest.com/resource/BoardsResource/get/?source_url=%2F".$username."%2F&data=%7B%22options%22%3A%7B%22privacy_filter%22%3A%22all%22%2C%22sort%22%3A%22last_pinned_to%22%2C%22username%22%3A%22".$username."%22%7D%2C%22context%22%3A%7B%7D%7D";
        $response = Http::withHeaders([
            "Accept-Encoding" => 'gzip, deflate',
            "Cookie" => 'Cookie: csrftoken='.bin2hex(random_bytes(32)) .'; _auth=1; _pinterest_sess='.$sess.';'
        ])->withOptions(['proxy' => $proxy])->get($apiUrl);
        return $response;
    }


    public static function get_user_data_from_cookie_temp($sess): array|string
    {
        $apiURL = 'https://www.pinterest.com/resource/HomefeedBadgingResource/get/';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // Good leeway for redirections.
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_REFERER, 'https://pinterest.com/login/');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With:XMLHttpRequest", "Accept:application/json"));
        curl_setopt($ch, CURLOPT_COOKIE, '_pinterest_sess="' . $sess . '"');

//        $response = curl_exec($ch);
//        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        curl_close($ch);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);
        if ($httpCode == '200') {

            $userData = $data['client_context']['user'] ?? array();

            $user = array();
            if (!empty($userData['username'])) {
                $user['username'] = $userData['username'];
                $user['sessid'] = $sess;
                $user['id'] = $userData['id'] ?? '';
                $user['email'] = $userData['email'] ?? '';
                $user['full_name'] = $userData['full_name'] ?? '';
                return $user;
            }
            else {
                return "Error";
            }
        }
        else {
            return "Error";
        }
    }

    public static function get_user_data_from_cookie(string $sess, $proxy): \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response
    {
        if ($proxy == "") {

            $response = Http::withHeaders([
                "Cookie" => '_pinterest_sess="' . $sess . '"',
                "X-Requested-With" => "XMLHttpRequest",
                "Referer" => "https://pinterest.com/login/"
            ])->acceptJson()->get("https://www.pinterest.com/resource/HomefeedBadgingResource/get/");
        }
        else {
            $response = Http::withHeaders([
                "Cookie" => '_pinterest_sess="' . $sess . '"',
                "X-Requested-With" => "XMLHttpRequest",
                "Referer" => "https://pinterest.com/login/"
            ])->withOptions(['proxy' => $proxy])->acceptJson()->get("https://www.pinterest.com/resource/HomefeedBadgingResource/get/");
        }
        return $response;
    }

    public static function post_pin_to_board($sess, $data = array(), $proxy) {
        $apiURL = 'https://www.pinterest.com/resource/PinResource/create/';
        $pinData = array(
            "options" => array(
                "alt_text" => $data["alt_text"] ?? "",
                "board_id" => $data['board_id'],
                "title" => $data["title"] ?? "",
                "description" => $data['note'] ?? '',
                "link" => $data['link'] ?? '',
                "image_url" => $data["image_url"],
                "method" => "uploaded",

            ),
            "context" => array()
        );
        $csrftoken = bin2hex(random_bytes(32));
        if ($proxy !== "") {
            $response = Http::withHeaders([
                "X-Requested-With" => "XMLHttpRequest",
                "X-CSRFToken" => $csrftoken,
                "Cookie" => 'csrftoken=' . $csrftoken . '; _pinterest_sess="' . $sess . '"; c_dpr=1'
            ])->withOptions(["proxy" => $proxy])->post($apiURL, ['data' => json_encode($pinData)])->json();
        }
        else {
            $response = Http::withHeaders([
                "X-Requested-With" => "XMLHttpRequest",
                "X-CSRFToken" => $csrftoken,
                "Cookie" => 'csrftoken=' . $csrftoken . '; _pinterest_sess="' . $sess . '"; c_dpr=1'
            ])->post($apiURL, ['data' => json_encode($pinData)]);
        }
        return $response;
    }



    /**
     * @throws \Exception
     */
    public static function post_pin_to_board_temp($sess, $data = array(), $proxy = array()): array {
        $apiURL = 'https://www.pinterest.com/resource/PinResource/create/';
        $pinData = array(
            "options" => array(
                "alt_text" => $data["alt_text"] ?? "",
                "board_id" => $data['board_id'],
                "title" => $data["title"] ?? "",
                "description" => $data['note'] ?? '',
                "link" => $data['link'] ?? '',
                "image_url" => $data["image_url"],
                "method" => "uploaded",

            ),
            "context" => array()
        );

        $postField = array(
            'data' => json_encode($pinData)
        );

        $fields = http_build_query($postField);

        // generated csrf token dynamically
        $csrftoken = bin2hex(random_bytes(32));



        $ch = curl_init();

        if ($proxy["host"] !== null) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy['host'].":".$proxy['port']);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        }
//        if ($proxy['port'] !== null) {
//            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
//        }
//        if ($proxy["type"] !== null) {
//            switch ($proxy["type"]) {
//                case "http":
//                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
//                    break;
//                case "https":
//                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTPS);
//                    break;
//                case "socks5":
//                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
//                    break;
//                case "socks4":
//                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
//                    break;
//            }
//        }

        if ($proxy["auth"] !== null) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy["auth"]);
        }

        curl_setopt($ch, CURLOPT_URL, $apiURL);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest", "X-CSRFToken: {$csrftoken}"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_COOKIE, 'csrftoken=' . $csrftoken . '; _pinterest_sess="' . $sess . '"; c_dpr=1');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);
        $status = $data['resource_response']['status'] ?? '';
        if ($httpCode == '200' && $status == 'success') {
            $pinData = $data['resource_response']['data'] ?? '';
            $respose['status'] = 'success';
            $respose['pindata'] = $pinData;
        } else {
            $respose['status'] = 'error';
            $respose['message'] = $data['resource_response']['error']['message'] ?? 'Something goes wrong, please try later.';
        }
        return $respose;
    }

    public static function register_upload_video($sess, $fileName)
    {
        $csrftoken = bin2hex(random_bytes(32));
        $apiURL = "https://www.pinterest.com/resource/ApiResource/create/";
        $filePath = TempFileController::GetPath($fileName);
        $video = FFProbe::create([
            'ffmpeg.binaries'  => env('FFMPEG'),
            'ffprobe.binaries' => env('FFPROBE')
        ]);
        $duration = (int) round($video->streams($filePath)->videos()->first()->get("duration") * 1000);
        $id = getRandomHex(4)."-".getRandomHex(2).'-'.getRandomHex(2).'-'.getRandomHex(2).'-'.getRandomHex(6);

        $client = new Client();
        $headers = [
            'authority' => 'www.pinterest.com',
            'accept' => 'application/json, text/javascript, */*, q=0.01',
            'accept-language' => 'en-US,en;q=0.9,vi;q=0.8',
            'cache-control' => 'no-cache',
            'content-type' => 'application/x-www-form-urlencoded',
            'cookie' => 'csrftoken='.$csrftoken.'; g_state={"i_l":0}; _auth=1; _pinterest_sess='.$sess,
            'origin' => 'https://www.pinterest.com',
            'pragma' => 'no-cache',
            'referer' => 'https://www.pinterest.com/',
            'sec-ch-ua' => '"Chromium";v="118", "Google Chrome";v="118", "Not=A?Brand";v="99"',
            'sec-ch-ua-full-version-list' => '"Chromium";v="118.0.5993.70", "Google Chrome";v="118.0.5993.70", "Not=A?Brand";v="99.0.0.0"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-model' => '""',
            'sec-ch-ua-platform' => '"macOS"',
            'sec-ch-ua-platform-version' => '"13.6.0"',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'same-origin',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
            'x-app-version' => 'c8350c6',
            'x-csrftoken' => $csrftoken,
            'x-pinterest-appstate' => 'active',
            'x-pinterest-pws-handler' => 'www/idea-pin-builder.js',
            'x-pinterest-source-url' => '/idea-pin-builder/',
            'x-requested-with' => 'XMLHttpRequest'
        ];
        $options = [
            'form_params' => [
                'source_url' => '/idea-pin-builder/',
                'data' => '{"options":{"url":"/v3/media/uploads/register/batch/","data":{"media_info_list":"[{\\"id\\":\\"'.$id.'\\",\\"media_type\\":\\"video-story-pin\\",\\"upload_aux_data\\":{\\"clips\\":[{\\"durationMs\\":'.$duration.',\\"isFromImage\\":false,\\"startTimestampMs\\":-1}]}}]"}},"context":{}}'
            ]];
        $request = new Request('POST', $apiURL, $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody(), true);
    }

    public static function upload_video($url, $parameter, $videoName) {
        $client = new Client();
        $headers = [
            'sec-ch-ua' => '"Chromium";v="118", "Google Chrome";v="118", "Not=A?Brand";v="99"',
            'sec-ch-ua-platform' => '"macOS"',
            'Referer' => 'https://www.pinterest.com/',
            'sec-ch-ua-mobile' => '?0',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36'
        ];
        $options = [
            'multipart' => [
                [
                    'name' => 'x-amz-date',
                    'contents' => $parameter['x-amz-date']
                ],
                [
                    'name' => 'x-amz-signature',
                    'contents' => $parameter['x-amz-signature']
                ],
                [
                    'name' => 'x-amz-security-token',
                    'contents' => $parameter['x-amz-security-token']
                ],
                [
                    'name' => 'x-amz-algorithm',
                    'contents' => $parameter['x-amz-algorithm']
                ],
                [
                    'name' => 'key',
                    'contents' => $parameter['key']
                ],
                [
                    'name' => 'policy',
                    'contents' => $parameter['policy']
                ],
                [
                    'name' => 'x-amz-credential',
                    'contents' => $parameter['x-amz-credential']
                ],
                [
                    'name' => 'Content-Type',
                    'contents' => $parameter['Content-Type']
                ],
                [
                    'name' => 'file',
                    'contents' => Utils::tryFopen(TempFileController::GetPath($videoName), 'r'),

                ]
            ]];
        $request = new Request('POST', $url, $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return $res->getStatusCode();
    }

    public static function get_status_of_id($sess, $id) {
        $client = new Client();
        $headers = [
            'X-Pinterest-PWS-Handler' => 'www/idea-pin-builder.js',
            'Accept' => 'application/json, text/javascript, */*, q=0.01',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-Pinterest-Source-Url' => '/idea-pin-builder/',
            'Cookie' => '_auth=1; _pinterest_sess='.$sess
            ];
        $request = new Request('GET', 'https://www.pinterest.com/resource/VIPResource/get/?source_url=/idea-pin-builder/&data=%7B%22options%22%3A%7B%22upload_ids%22%3A%5B%22'.$id.'%22%5D%7D%2C%22context%22%3A%7B%7D%7D', $headers);
        $res = $client->sendAsync($request)->wait();
        return json_decode($res->getBody(), true);
    }

    public static function register_upload_image($sess, $fileName)
    {
        $csrftoken = bin2hex(random_bytes(32));
        $apiURL = "https://www.pinterest.com/resource/ApiResource/create/";
        $filePath = TempFileController::GetPath($fileName);
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  => env('FFMPEG'),
            'ffprobe.binaries' => env('FFPROBE')
        ]);
        $video = $ffmpeg->open($filePath);
        $video->frame(TimeCode::fromSeconds(0))->save(TempFileController::GetRootPath().'/'.$fileName.'.jpeg');
        $id = getRandomHex(4)."-".getRandomHex(2).'-'.getRandomHex(2).'-'.getRandomHex(2).'-'.getRandomHex(6);

        $client = new Client();
        $headers = [
            'authority' => 'www.pinterest.com',
            'accept' => 'application/json, text/javascript, */*, q=0.01',
            'accept-language' => 'en-US,en;q=0.9,vi;q=0.8',
            'cache-control' => 'no-cache',
            'content-type' => 'application/x-www-form-urlencoded',
            'cookie' => 'csrftoken='.$csrftoken.'; g_state={"i_l":0}; _auth=1; _pinterest_sess='.$sess,
            'origin' => 'https://www.pinterest.com',
            'pragma' => 'no-cache',
            'referer' => 'https://www.pinterest.com/',
            'sec-ch-ua' => '"Chromium";v="118", "Google Chrome";v="118", "Not=A?Brand";v="99"',
            'sec-ch-ua-full-version-list' => '"Chromium";v="118.0.5993.70", "Google Chrome";v="118.0.5993.70", "Not=A?Brand";v="99.0.0.0"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-model' => '""',
            'sec-ch-ua-platform' => '"macOS"',
            'sec-ch-ua-platform-version' => '"13.6.0"',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'same-origin',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36',
            'x-app-version' => 'c8350c6',
            'x-csrftoken' => $csrftoken,
            'x-pinterest-appstate' => 'active',
            'x-pinterest-pws-handler' => 'www/idea-pin-builder.js',
            'x-pinterest-source-url' => '/idea-pin-builder/',
            'x-requested-with' => 'XMLHttpRequest'
        ];
        $options = [
            'form_params' => [
                'source_url' => '/idea-pin-builder/',
                'data' => '{"options":{"url":"/v3/media/uploads/register/batch/","data":{"media_info_list":"[{\\"id\\":\\"'.$id.'\\",\\"media_type\\":\\"image-story-pin\\"}]"}},"context":{}}'
            ]];
        $request = new Request('POST', $apiURL, $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody(), true);
    }

    public static function upload_image($url, $parameter, $videoName) {
        $client = new Client();
        $headers = [
            'sec-ch-ua' => '"Chromium";v="118", "Google Chrome";v="118", "Not=A?Brand";v="99"',
            'sec-ch-ua-platform' => '"macOS"',
            'Referer' => 'https://www.pinterest.com/',
            'sec-ch-ua-mobile' => '?0',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36'
        ];
        $options = [
            'multipart' => [
                [
                    'name' => 'x-amz-date',
                    'contents' => $parameter['x-amz-date']
                ],
                [
                    'name' => 'x-amz-signature',
                    'contents' => $parameter['x-amz-signature']
                ],
                [
                    'name' => 'x-amz-security-token',
                    'contents' => $parameter['x-amz-security-token']
                ],
                [
                    'name' => 'x-amz-algorithm',
                    'contents' => $parameter['x-amz-algorithm']
                ],
                [
                    'name' => 'key',
                    'contents' => $parameter['key']
                ],
                [
                    'name' => 'policy',
                    'contents' => $parameter['policy']
                ],
                [
                    'name' => 'x-amz-credential',
                    'contents' => $parameter['x-amz-credential']
                ],
                [
                    'name' => 'Content-Type',
                    'contents' => $parameter['Content-Type']
                ],
                [
                    'name' => 'file',
                    'contents' => Utils::tryFopen(TempFileController::GetPath($videoName.'.jpeg'), 'r'),
                ]
            ]];
        $request = new Request('POST', $url, $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return $res->getStatusCode();
    }

    public static function create_story_pinterest($sess, $pinData = array(), $fileName) {
        $csrftoken = bin2hex(random_bytes(32));
        $client = new Client();
        $headers = [
            'X-Pinterest-AppState' => 'background',
            'X-Pinterest-PWS-Handler' => 'www/idea-pin-builder.js',
            'X-Requested-With' => 'XMLHttpRequest',
            'X-CSRFToken' => $csrftoken,
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json, text/javascript, */*, q=0.01',
            'Referer' => 'https://www.pinterest.com/',
            'X-Pinterest-Source-Url' => '/idea-pin-builder/',
            'Cookie' => '_auth=0; _pinterest_sess='.$sess.'; csrftoken='.$csrftoken
        ];
        $options = [
            'form_params' => [
                'source_url' => '/idea-pin-builder/',
                'data' => '{"options":{"allow_shopping_rec":true,"board_id":"'.$pinData['board_id'].'","description":"'.$pinData['note'].'","is_comments_allowed":true,"is_removable":false,"is_unified_builder":true,"link":"'.$pinData['link'].'","orbac_subject_id":"","story_pin":"{\\"metadata\\":{\\"pin_title\\":\\"'.$pinData['title'].'\\",\\"pin_image_signature\\":\\"'.$pinData['image_signature'].'\\",\\"canvas_aspect_ratio\\":1.7777777777777777},\\"pages\\":[{\\"blocks\\":[{\\"block_style\\":{\\"height\\":100,\\"width\\":100,\\"x_coord\\":0,\\"y_coord\\":0},\\"tracking_id\\":\\"'.$pinData['video_id'].'\\",\\"video_signature\\":\\"'.$pinData['video_signature'].'\\",\\"type\\":3}],\\"clips\\":[{\\"clip_type\\":1,\\"end_time_ms\\":-1,\\"is_converted_from_image\\":false,\\"source_media_height\\":'.PhpFFmpegController::GetHeight(TempFileController::GetPath($fileName)).',\\"source_media_width\\":'.PhpFFmpegController::GetWidth(TempFileController::GetPath($fileName)).',\\"start_time_ms\\":-1}],\\"layout\\":0,\\"style\\":{\\"background_color\\":\\"#FFFFFF\\"}}]}","user_mention_tags":"[]"},"context":{}}'
            ]];
        $request = new Request('POST', 'https://www.pinterest.com/resource/StoryPinResource/create/', $headers);
        $res = $client->sendAsync($request, $options)->wait();
        return json_decode($res->getBody(), true);
    }



}


