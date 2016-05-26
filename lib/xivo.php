<?php

/*
 Copyright (C) 2016 - Ward Mundy, Sylvain Boily
 SPDX-License-Identifier: GPL-3.0+
*/


include_once("restclient.php");


class XiVO {

    function __construct($xivo_host, $xivo_api_user=NULL, $xivo_api_pwd=NULL) {
        $this->xivo_host = $xivo_host;
        $this->xivo_api_user = $xivo_api_user;
        $this->xivo_api_pwd = $xivo_api_pwd;
        $this->xivo_backend_user = "xivo_user";
    }

    function _connect($port, $version, $token=NULL, $xivo_api_user=NULL, $xivo_api_pwd=NULL) {
        $connect = new RestClient([
            'base_url' => "https://".$this->xivo_host.":".$port."/".$version,
            'headers' => ['X-Auth-Token' => $token],
            'curl_options' => [CURLOPT_SSL_VERIFYPEER => FALSE,
                               CURLOPT_SSL_VERIFYHOST => FALSE,
                               CURLOPT_ENCODING => 'application/json',
                              ],
            'decoders' => ['json'],
            'username' => $xivo_api_user,
            'password' => $xivo_api_pwd
        ]);

        return $connect;
    }

    function xivo_login($login, $password) {
        $info = $this->get_token($login, $password, $this->xivo_backend_user);
        setcookie("asteridex[uuid]", $info['uuid'], time() + 3600);

        return $info['token'];
    }

    function xivo_logout() {
        $token = $_COOKIE['asteridex']['session'];

        $connect = $this->_connect(9497, "0.1");
        $connect->delete("token/$token");

        unset($_COOKIE['asteridex']['session']);
        setcookie("asteridex[session]", "", time() - 3600);
        setcookie("asteridex[uuid]", "", time() - 3600);

        header('Location: index.php');
    }

    function get_displayname() {

        $connect = $this->_connect(9486, "1.1", $_COOKIE['asteridex']['session']);
        $user = $connect->get("users/".$_COOKIE['asteridex']['uuid']);

        if ($user->info->http_code == 200) {
            $info['firstname'] = json_decode($user->response)->firstname;
            $info['lastname'] = json_decode($user->response)->lastname;

            $displayname = $info['firstname']." ".$info['lastname'];
            return $displayname;
        }

        return "Error to get displayname";
    }

    function _get_context() {
        $line_id = $this->_get_line();

        $info = $this->get_token($this->xivo_api_user, $this->xivo_api_pwd, "xivo_service");
        $connect = $this->_connect(9486, "1.1", $info['token']);
        $line = $connect->get("lines/".$line_id);

        if ($line->info->http_code == 200) {
            return json_decode($line->response)->context;
        }

        return FALSE;
    }

    function _get_line() {
        $connect = $this->_connect(9486, "1.1", $_COOKIE['asteridex']['session']);
        $lines = $connect->get("users/".$_COOKIE['asteridex']['uuid']."/lines");

        $result = json_decode($lines->response);

        if ($lines->info->http_code == 200) {
            for ($i = 0; $i<count($result); $i++) {
                if ($result->items[$i]->main_line) {
                    return $result->items[$i]->line_id;
                }
            }
        }

        return FALSE;
    }

    function do_call($extension, $xivo_api_user, $xivo_api_pwd) {

        $info = $this->get_token($xivo_api_user, $xivo_api_pwd, "xivo_service");
        $user = $_COOKIE['asteridex']['uuid'];

        $call = json_encode(['destination' => ['extension' => $extension,
                                               'context' => $this->_get_context(),
                                               'priority' => 0],
                             'source' => ['user' => $user]]);

        $connect = $this->_connect(9500, "1.0", $info['token']);
        $connect->post("calls", $call, ['Content-Type' => 'application/json']);
    }

    function get_token($xivo_api_user, $xivo_api_pwd, $backend) {
        $auth_info = json_encode(['backend' => $backend,
                                  'expiration' => 3600
                                 ]);

        $connect = $this->_connect(9497, "0.1", NULL, $xivo_api_user, $xivo_api_pwd);
        $t = $connect->post("token", $auth_info, ['Content-Type' => 'application/json']);

        if ($t->info->http_code == 200) {
            $info['token'] = json_decode($t->response)->data->token;
            $info['uuid'] = json_decode($t->response)->data->xivo_user_uuid;

            return $info;
        }

        return FALSE;
    }

}

?>
