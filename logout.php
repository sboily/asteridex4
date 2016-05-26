<?php

include_once("lib/xivo.php");

xivo_logout($_COOKIE['asteridex']['session']);
unset($_COOKIE['asteridex']['session']);
setcookie("asteridex[session]", "", time() - 3600);
setcookie("asteridex[uuid]", "", time() - 3600);
header('Location: index.php');

?>
