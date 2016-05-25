<?php

$title = "Nerd Vittles AsteriDex for XiVO (SQLite 3 edition)";
$sub_title = "Welcome to AsteriDex -- The Poor Man's Rolodex";


//SIP Configuration -- See callboth.php for details about these variables

$INtrunk="SIP" ;
$defaultExt="701";
$defaultLine="8zqnfdpw";
$LDprefix="1" ;
$CallerID="8005551212" ;


//SQLite3 database connection settings

$db = new SQLite3('/var/lib/asterisk/agi-bin/asteridex.sqlite');


if(isset($_POST['submit'])){
	$INdefault = $_POST['sipID'];
	setcookie("asteridex[sipID]",$INdefault,time()+99999999);
  } else {
  if(isset($_COOKIE['asteridex'])){
	$INdefault = $_COOKIE['asteridex']['sipID'];
  } else {
	//$INdefault = "local/701@from-internal" ;
	$INdefault = $defaultExt ;
	setcookie("asteridex[sipID]",$INdefault,time()+99999999);
  }
}
?>
