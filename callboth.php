<?php

/*
 Copyright (C) 2016 - Ward Mundy, Sylvain Boily
 SPDX-License-Identifier: GPL-3.0+
*/

include_once("config/config.inc.php");
include_once("lib/xivo.php");

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

if ($pos===false) :
    $xivo->do_call($OUT, $xivo_api_user, $xivo_api_pwd);
?>

<html>
  <head>
    <script>
      var duration = 4000;
      x = null

      function closeIt() {
        x = setTimeout("self.close()",duration);
      }
    </script>
  </head>

  <body onload="closeIt();self.focus()">
    Extension is ringing now... <br>
    When you answer, call to <?php echo $OUT ?> will be placed.
  </body>
</html>

<?php
else :
?>

<html>
  <head>
    <script>
      var duration = 1000;
      x = null;

      function closeIt() {
        x = setTimeout("self.close()",duration);
      }
    </script>

  </head>
  <body onload="closeIt();self.focus()">
  </body>
</html>

<?php endif; ?>
