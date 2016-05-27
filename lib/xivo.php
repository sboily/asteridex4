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
        $this->xivo_session = $_COOKIE['asteridex']['session'];
        $this->xivo_uuid = $_COOKIE['asteridex']['uuid'];
    }

    private function _connect($port, $version, $token=NULL, $xivo_api_user=NULL, $xivo_api_pwd=NULL) {
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

    private function _get_context() {
        $line_id = $this->_get_line();

        $info = $this->_get_token($this->xivo_api_user, $this->xivo_api_pwd, "xivo_service");
        $connect = $this->_connect(9486, "1.1", $info['token']);
        $line = $connect->get("lines/".$line_id);

        if ($line->info->http_code == 200) {
            return json_decode($line->response)->context;
        }

        return FALSE;
    }

    private function _get_line() {
        $connect = $this->_connect(9486, "1.1", $this->session);
        $lines = $connect->get("users/$this->xivo_uuid/lines");

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

    private function _get_token($xivo_api_user, $xivo_api_pwd, $backend) {
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

    public function xivo_login($login, $password) {
        $info = $this->_get_token($login, $password, $this->xivo_backend_user);
        setcookie("asteridex[uuid]", $info['uuid'], time() + 3600);

        return $info['token'];
    }

    public function xivo_logout() {
        $connect = $this->_connect(9497, "0.1");
        $connect->delete("token/$this->xivo_session");

        setcookie("asteridex[session]", "", time() - 3600);
        setcookie("asteridex[uuid]", "", time() - 3600);

        header('Location: index.php');
    }

    public function get_displayname() {

        $connect = $this->_connect(9486, "1.1", $this->xivo_session);
        $user = $connect->get("users/$this->xivo_uuid");

        if ($user->info->http_code == 200) {
            $info['firstname'] = json_decode($user->response)->firstname;
            $info['lastname'] = json_decode($user->response)->lastname;

            $displayname = $info['firstname']." ".$info['lastname'];
            return $displayname;
        }

        return "Error to get displayname";
    }

    public function do_call($extension) {

        $call = json_encode(['destination' => ['extension' => $extension,
                                               'context' => $this->_get_context(),
                                               'priority' => 0],
                             'source' => ['user' => $this->xivo_uuid]
                            ]);

        $connect = $this->_connect(9500, "1.0", $this->xivo_session);
        $connect->post("calls", $call, ['Content-Type' => 'application/json']);
    }

    public function get_personal() {
        $connect = $this->_connect(9489, "0.1", $this->xivo_session);
        $personal = $connect->get("personal");

        $result = json_decode($personal->response);

        $contacts = array();
        if ($personal->info->http_code == 200) {
            return $result->items;
        }

        return FALSE;
    }

    public function add_personal($contact) {
        $contact = json_encode(['firstname' => $contact['firstname'],
                                'lastname' => $contact['lastname'],
                                'number' => $contact['number']
                               ]);

        $connect = $this->_connect(9489, "0.1", $this->xivo_session);
        $personal = $connect->post("personal", $contact, ['Content-Type' => 'application/json']);
    }
}

function get_phonebook($db, $tab) {
    $chr = str_split($tab);
    $chr_start = $chr[0];
    $chr_end = end($chr);

    $query_string = "select displayname, number from phonebook, phonebooknumber
                     where phonebook.id=phonebooknumber.phonebookid and substr(displayname,1,1)
                     between '$chr_start' and '$chr_end' order by displayname asc";
    $result = pg_query($db, $query_string);

    $i = 0;
    while ($row = pg_fetch_array($result)) {
      $entries[$i]['number'] = $row["number"];
      $entries[$i]['dialcode'] = "";
      $entries[$i]['displayname'] = $row["displayname"];
      $i++;
    }

    pg_close($db);
    return $entries;
}

function get_personal($xivo) {
    $row = $xivo->get_personal();
    for ($i = 0; $i < count($row); $i++) {
        $entries[$i]['displayname'] = $row[$i]->firstname." ".$row[$i]->lastname;
        $entries[$i]['number'] = $row[$i]->number;
    }

    return $entries;
}

?>
