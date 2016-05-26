<?php

$title = "Nerd Vittles AsteriDex for XiVO (SQLite 3 edition)";
$sub_title = "Welcome to AsteriDex -- The Poor Man's Rolodex";


//SIP Configuration -- See callboth.php for details about these variables

$LDprefix="1" ;
$CallerID="8005551212" ;

$xivo_api_user = "sylvain";
$xivo_api_pwd = "sylvain";

//SQLite3 database connection settings

$db = new SQLite3('/var/lib/asterisk/agi-bin/asteridex.sqlite');

?>
