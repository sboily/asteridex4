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

$exten = urlencode($_REQUEST['OUT']);
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
    $tpl->assign("pos", TRUE);

    $xivo->do_call($exten);
} else {
    $tpl->assign("duration", 1000);
    $tpl->assign("pos", FALSE);
}

$tpl->display("tpl/call.html");

?>
