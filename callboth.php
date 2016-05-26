<?php

include_once("config/config.inc.php");
include_once("lib/xivo.php");

$IN=urlencode($_REQUEST['IN']);
$OUT=urlencode($_REQUEST['OUT']);
$SEQ=urlencode($_REQUEST['SEQ']);
$OUT= $LDprefix . $OUT ;

$OUT = str_replace( chr(13), "", $OUT );
$OUT = str_replace( chr(10), "", $OUT );
$OUT = str_replace( ">", "", $OUT );
$OUT = str_replace( "<", "", $OUT );


$pos = false ;
if (strlen($OUT)>100) :
 $pos=true ;
endif ;
if ($SEQ<>"958217") :
 $pos=true ;
endif ;

if ($pos===false) :
    do_call($OUT, $xivo_api_user, $xivo_api_pwd);
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
    Extension <?php echo $IN ?>is ringing now. <br>
    When <?php echo $IN ?>answers, call to <?php echo $OUT ?>will be placed.
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
