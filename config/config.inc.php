<?php

$title = "Nerd Vittles AsteriDex for XiVO";
$sub_title = "Welcome to AsteriDex -- The Poor Man's Rolodex";

$xivo_host = "127.0.0.1";
$xivo_host_db = "127.0.0.1";
$xivo_api_user = "sylvain";
$xivo_api_pwd = "sylvain";
$xivo_backend_user = "xivo_user";

$xivo_host = null !== getenv('XIVO_HOST') ? getenv('XIVO_HOST') : $xivo_host;
$xivo_host_db = null !== getenv('XIVO_HOST_DB') ? getenv('XIVO_HOST_DB') : $xivo_host_db;
$xivo_api_user = null !== getenv('XIVO_API_USER') ? getenv('XIVO_API_USER') : $xivo_api_user;
$xivo_api_pwd = null !== getenv('XIVO_API_PWD') ? getenv('XIVO_API_PWD') : $xivo_api_pwd;
$xivo_backend_user = null !== getenv('XIVO_BACKEND_USER') ? getenv('XIVO_BACKEND_USER') : $xivo_backend_user;

$db = pg_connect("host=".$xivo_host_db." port=5432 dbname=asterisk user=asterisk password=proformatique");

?>
