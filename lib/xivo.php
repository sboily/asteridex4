<?php

include_once("restclient.php");

function do_call($extension, $xivo_api_user, $xivo_api_pwd) {

    $token = get_token($xivo_api_user, $xivo_api_pwd);
    $user = $_COOKIE['asteridex']['uuid'];

    $do_call = new RestClient([
        'base_url' => "https://127.0.0.1:9500/1.0",
        'headers' => ['X-Auth-Token' => $token],
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

function get_token($xivo_api_user, $xivo_api_pwd) {
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

    $auth_info = json_encode(['backend' => 'xivo_service',
                              'expiration' => 3600
                             ]);

    $t = $auth->post("token", $auth_info, ['Content-Type' => 'application/json']);

    $token = json_decode($t->response)->data->token;
    $user = json_decode($t->response)->data->xivo_user_uuid;

    return $token;
}

?>
