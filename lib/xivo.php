<?php

/*
 Copyright (C) 2016 - Ward Mundy, Sylvain Boily
 SPDX-License-Identifier: GPL-3.0+
*/


include_once("restclient.php");


class XiVO {

    function __construct($xivo_host) {
        $this->xivo_host = $xivo_host;
        $this->xivo_backend_user = "xivo_user";
        $this->xivo_session = $_COOKIE['asteridex']['session'];
        $this->xivo_uuid = $this->_get_uuid();
    }

    private function _connect($port, $version, $token=NULL, $login=NULL, $password=NULL) {
        $connect = new RestClient([
            'base_url' => "https://$this->xivo_host:$port/$version",
            'headers' => ['X-Auth-Token' => $token],
            'curl_options' => [CURLOPT_SSL_VERIFYPEER => false,
                               CURLOPT_SSL_VERIFYHOST => false,
                               CURLOPT_ENCODING => 'application/json',
                              ],
            'decoders' => ['json'],
            'username' => $login,
            'password' => $password

        ]);

        return $connect;
    }

    private function _get_uuid() {
        if (empty($this->xivo_session)) {
            return false;
        }

        $connect = $this->_connect(9497, "0.1", $this->xivo_session);
        $uuid = $connect->get("token/$this->xivo_session");

        if ($uuid->info->http_code == 200) {
            return json_decode($uuid->response)->data->xivo_user_uuid; 
        }

        return false;
    }

    private function _get_token($login, $password, $backend) {
        $auth_info = json_encode(['backend' => $backend,
                                  'expiration' => 3600
                                 ]);

        $connect = $this->_connect(9497, "0.1", NULL, $login, $password);
        $t = $connect->post("token", $auth_info, ['Content-Type' => 'application/json']);

        if ($t->info->http_code == 200) {
            $info['token'] = json_decode($t->response)->data->token;
            $info['uuid'] = json_decode($t->response)->data->xivo_user_uuid;

            return $info;
        }

        return false;
    }

    public function xivo_login($login, $password) {
        $info = $this->_get_token($login, $password, $this->xivo_backend_user);

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

        $call = json_encode(['extension' => $extension]);

        $connect = $this->_connect(9500, "1.0", $this->xivo_session);
        $connect->post("users/me/calls", $call, ['Content-Type' => 'application/json']);
    }

    public function get_personal() {
        $connect = $this->_connect(9489, "0.1", $this->xivo_session);
        $personal = $connect->get("personal");

        $result = json_decode($personal->response);

        $contacts = array();
        if ($personal->info->http_code == 200) {
            return $result->items;
        }

        return false;
    }

    public function add_personal($contact) {
        if (empty($contact['firstname']) or empty($contact['number'])) {
            print "Error to add personal contact!";
            return false;
        }
        $contact = json_encode(['firstname' => $contact['firstname'],
                                'lastname' => $contact['lastname'],
                                'number' => $contact['number']
                               ]);

        $connect = $this->_connect(9489, "0.1", $this->xivo_session);
        $personal = $connect->post("personal", $contact, ['Content-Type' => 'application/json']);
    }

    public function delete_personal($id) {
        $connect = $this->_connect(9489, "0.1", $this->xivo_session);
        $connect->delete("personal/$id");
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
        $entries[$i]['id'] = $row[$i]->id;
    }

    return $entries;
}

function do_call($tpl, $xivo) {
    $exten = urlencode($_REQUEST['exten']);
    $exten = str_replace( chr(13), "", $exten );
    $exten = str_replace( chr(10), "", $exten );
    $exten = str_replace( ">", "", $exten );
    $exten = str_replace( "<", "", $exten );

    $pos = false ;
    if (strlen($exten)>100) :
        $pos=true ;
    endif ;

    if ($pos===false) {
        $tpl->assign("duration", 4000);
        $tpl->assign("exten", $exten);
        $tpl->assign("pos", true);

        $xivo->do_call($exten);
    } else {
        $tpl->assign("duration", 1000);
        $tpl->assign("pos", false);
    }

    $tpl->display("tpl/call.html");
}

?>
