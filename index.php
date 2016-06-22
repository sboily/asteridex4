<?php

/*
 Copyright (C) 2016 - Ward Mundy, Sylvain Boily
 SPDX-License-Identifier: GPL-3.0+
*/

require("/usr/share/php/smarty3/Smarty.class.php");

include_once("config/config.inc.php");
include_once("lib/xivo.php");

$xivo = new XiVO($xivo_host);
$xivo->xivo_backend_user = $xivo_backend_user;

$tpl = new Smarty();
$tpl->assign("title", $title);

if ($_POST) {
    $session = $xivo->xivo_login($_POST['username'], $_POST['password']);
    if ($session) {
        setcookie("asteridex[session]", $session, time() + 3600);
        header('Location: index.php');
    }
}

$session = isset($_COOKIE['asteridex']['session']) ? $_COOKIE['asteridex']['session'] : "";

if (!empty($session)) {

    $tab = isset($_GET['tab']) ? $_GET['tab'] : "ABC";

    $tpl->assign("displayname", $xivo->get_displayname());
    $tpl->assign("alph", array("ABC","DEF","GHI","JKL","MNO","PQRS","TUV","WXYZ"));
    $tpl->assign("sub_title", $sub_title);
    $tpl->assign("tab", $tab);
    $tpl->assign("uuid", $session);

    switch($_GET['action']) {
        case 'logout':
            $xivo->xivo_logout();
            break;
        case 'delete':
                $id = $_GET['contactid'];
                if (!empty($id)) {
                    $xivo->delete_personal($id);
                }
            break;
        case 'docall':
                do_call($tpl, $xivo);
                die();
            break;
    }

    switch($tab) {
        case 'personal':
            if (!empty($_POST)) {
                $xivo->add_personal($_POST);
            }
            $entries = get_personal($xivo);
            $tpl->assign("entries", $entries);
            $tpl->assign("personal", true);
            $tpl->display("tpl/personal.html");
            break;

        default:
            $entries = get_phonebook($db, $tab);
            if (!empty($entries)) {
              $tpl->assign("entries", $entries);
            }
            $tpl->display("tpl/contacts.html");
    }


} else {

    $tpl->display("tpl/login.html");

}

?>
