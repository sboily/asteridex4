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

    for($i=0;$i<strlen($tab);$i++) {

      if(strlen($tab) == 3) {
        $w = "33%";
      } else {
        $w = "25%";
      }
      $tpl->assign("w", $w);

      $chr = str_split($tab);
      $chr_start = $chr[0];
      $chr_end = end($chr);

      $query_string="SELECT * FROM user1 WHERE substr(name,1,1) between '$chr_start' and '$chr_end' order by name asc";
      $db->exec($query_string);
      $result = $db->query($query_string);

      $i = 0;
      while ($row = $result->fetchArray()) {
        $entries[$i]['out'] = $row["out"];
        $entries[$i]['dialcode'] = $row["dialcode"];
        $entries[$i]['name'] = $row["name"];
        $i++;
      }
      $tpl->assign("entries", $entries);
    }

    $db->close();
    $tpl->display("tpl/index.html");

} else {

    $tpl->display("tpl/login.html");

}

?>
