<?php

/*
 Copyright (C) 2016 - Ward Mundy, Sylvain Boily
 SPDX-License-Identifier: GPL-3.0+
*/

require("/usr/share/php/smarty3/Smarty.class.php");
include_once("config/config.inc.php");
include_once("lib/xivo.php");

$tpl = new Smarty();

$xivo = new XiVO($xivo_host);
$xivo->xivo_api_user = $xivo_api_user;
$xivo->xivo_api_pwd = $xivo_api_pwd;

$OUT=urlencode($_REQUEST['OUT']);
$OUT = str_replace( chr(13), "", $OUT );
$OUT = str_replace( chr(10), "", $OUT );
$OUT = str_replace( ">", "", $OUT );
$OUT = str_replace( "<", "", $OUT );

$pos = false ;
if (strlen($OUT)>100) :
 $pos=true ;
endif ;

if ($pos===false) {
    $tpl->assign("duration", 4000);
    $tpl->assign("exten", $OUT);
    $tpl->assign("pos", TRUE);

    $xivo->do_call($OUT, $xivo_api_user, $xivo_api_pwd);
} else {
    $tpl->assign("duration", 1000);
    $tpl->assign("pos", FALSE);
}

$tpl->display("tpl/call.html");

?>
