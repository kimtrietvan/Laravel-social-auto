<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
class PinterestController extends Controller
{
    /**
     * @throws \Exception
     */
    public static function get_board_list($sess, $username): array|string {
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

    public static function get_user_data_from_cookie($sess): array|string
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

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
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

    /**
     * @throws \Exception
     */
    public static function post_pin_to_board($sess, $data = array(), $proxy = array()): array {
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

        if ((isset($proxy["host"]) && isset($proxy['port']) && isset($proxy["type"])) && (isset($proxy["host"]) || isset($proxy['port']) || isset($proxy["type"]))) {
            return array(
                "status" => "Error",
                "error" => "Must be has 3 var host, port and type, or non of them"
            );
        }

        $ch = curl_init();
        if (isset($proxy["host"])) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy["host"]);
        }
        if (isset($proxy['port'])) {
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
        }
        if (isset($proxy["type"])) {
            switch ($proxy["type"]) {
                case "http":
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                    break;
                case "https":
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTPS);
                    break;
                case "socks5":
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                    break;
                case "socks4":
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
                    break;
            }
        }
        curl_setopt($ch, CURLOPT_URL, $apiURL);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest", "X-CSRFToken: {$csrftoken}"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

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
}
