<?php

/*
 Copyright (C) 2016 - Ward Mundy, Sylvain Boily
 SPDX-License-Identifier: GPL-3.0+
*/

require("/usr/share/php/smarty3/Smarty.class.php");

include_once("config/config.inc.php");
include_once("lib/xivo.php");

$xivo = new XiVO($xivo_host);

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

    $chr = str_split($tab);
    $chr_start = $chr[0];
    $chr_end = end($chr);

    $query_string = "select displayname, number from phonebook, phonebooknumber
                     where phonebook.id=phonebooknumber.phonebookid and substr(displayname,1,1)
                     between '$chr_start' and '$chr_end' order by displayname asc";
    $result = pg_query($db, $query_string);

    $i = 0;
    while ($row = pg_fetch_array($result)) {
      $entries[$i]['out'] = $row["number"];
      $entries[$i]['dialcode'] = "";
      $entries[$i]['name'] = $row["displayname"];
      $i++;
    }

    if (!empty($entries)) {
      $tpl->assign("entries", $entries);
    }

    pg_close($db);
    $tpl->display("tpl/index.html");

} else {

    $tpl->display("tpl/login.html");

}

?>
