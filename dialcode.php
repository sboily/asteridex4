<?php
include_once("config.inc.php");

// dialcode.php - This dialcode updater erases existing dialcode entries in AsteriDex
//                and replaces every entry with the 3-digit phone number matching the first three letters in name.
//                Apostrophes, hyphens and non-alpha characters are skipped and the next character is examined.

//Allow access from local networks only
$ll=strlen($localnet);
if (substr($_SERVER['REMOTE_ADDR'],0,$ll)<>$localnet){
   header("Location: ./index.php?ip=$REMOTE_ADDR"); 
   exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?php echo $title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<style type="text/css" media="screen">@import "basic.css";</style>
	<style type="text/css" media="screen">@import "tabs.css";</style>
</head>

<?php
   $tab = isset($_GET['tab']) ? $_GET['tab'] : "";
   $alph = array("ABC","DEF","GHI","JKL","MNO","PQRS","TUV","WXYZ")
?>

<body>

<?php
//  $query = "UPDATE `user1` SET `name` = '$CONTACT2', `in` = '$IN2', `out` = '$OUT2', `dialcode` = '$DIALCODE2' WHERE `id` = $RECNO2 LIMIT 1";
//  $result = mysql_query($query)
//    or die("Database update failed");
//  $MSG="Record #" . $RECNO2 . " updated for $CONTACT2: $OUT2." ;
  $sql_select = "SELECT * FROM user1 WHERE id>0 order by id" ;
  $sql_result = mysql_query($sql_select)
    or die ("Couldn't execute SQL query on AsteriDex table.") ;
  while ($row = mysql_fetch_array($sql_result))  {
   $id = $row[0];
   $thename = strtoupper($row[1]) ;
   $i=0;
   $reps=3;
   $dialcode="";
   while ($i<$reps) {
   $letter = substr($thename,$i,1);
   if (strpos(" ABC",$letter)>0) :
    $dialcode = $dialcode . "2";
   elseif (strpos(" DEF",$letter)>0) :    
    $dialcode = $dialcode . "3";
   elseif (strpos(" GHI",$letter)>0) :    
    $dialcode = $dialcode . "4";
   elseif (strpos(" JKL",$letter)>0) :    
    $dialcode = $dialcode . "5";
   elseif (strpos(" MNO",$letter)>0) :    
    $dialcode = $dialcode . "6";
   elseif (strpos(" PQRS",$letter)>0) :    
    $dialcode = $dialcode . "7";
   elseif (strpos(" TUV",$letter)>0) :    
    $dialcode = $dialcode . "8";
   elseif (strpos(" WXYZ",$letter)>0) :    
    $dialcode = $dialcode . "9";
   else :
    $reps=$reps+1 ;
   endif ;
   $i=$i+1 ;
   }
   echo $id . " " . $row[1] . " dialcode: " . $dialcode . "<BR>" ;

   $query = "UPDATE `user1` SET `dialcode` = '$dialcode' WHERE `id` = $id LIMIT 1";
   $result = mysql_query($query)
    or die("Database update failed");

   $reps = 3;
  }
?>
</body></html>
