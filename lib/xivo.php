<?php

include_once("restclient.php");

function xivo_authenticate($login, $password) {
    $info = get_token($login, $password, "xivo_user");
    setcookie("asteridex[uuid]", $info['uuid'], time() + 3600);
    return $info['token'];
}

function xivo_logout($token) {
    $logout = new RestClient([
        'base_url' => "https://127.0.0.1:9497/0.1",
        'curl_options' => [CURLOPT_SSL_VERIFYPEER => FALSE,
                           CURLOPT_SSL_VERIFYHOST => FALSE
                          ],
    ]);

    $logout->delete("token/$token");
}

function do_call($extension, $xivo_api_user, $xivo_api_pwd) {

    $info = get_token($xivo_api_user, $xivo_api_pwd, "xivo_service");
    $user = $_COOKIE['asteridex']['uuid'];

    $do_call = new RestClient([
        'base_url' => "https://127.0.0.1:9500/1.0",
        'headers' => ['X-Auth-Token' => $info['token']],
        'curl_options' => [CURLOPT_SSL_VERIFYPEER => FALSE,
                           CURLOPT_SSL_VERIFYHOST => FALSE
                          ],
    ]);

    $call = json_encode(['destination' => ['extension' => $extension,
                                           'context' => 'default',
                                           'priority' => 0],
                         'source' => ['user' => $user]]);

    $result = $do_call->post("calls", $call, ['Content-Type' => 'application/json']);
}

function get_token($xivo_api_user, $xivo_api_pwd, $backend) {
    $auth = new RestClient([
        'base_url' => "https://127.0.0.1:9497/0.1",
        'curl_options' => [CURLOPT_SSL_VERIFYPEER => FALSE,
                           CURLOPT_SSL_VERIFYHOST => FALSE,
                           CURLOPT_ENCODING => 'application/json',
                          ],
        'decoders' => ['json'],
        'username' => $xivo_api_user,
        'password' => $xivo_api_pwd
    ]);

    $auth_info = json_encode(['backend' => $backend,
                              'expiration' => 3600
                             ]);

    $t = $auth->post("token", $auth_info, ['Content-Type' => 'application/json']);

    if ($t->info->http_code == 200) {
        $info['token'] = json_decode($t->response)->data->token;
        $info['uuid'] = json_decode($t->response)->data->xivo_user_uuid;

        return $info;
    }

    return FALSE;
}

?>
