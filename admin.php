<?php
include_once("config.inc.php");
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
	<h1><?php echo $title; ?></h1>
	<div id="header">
	<ul id="primary">
	<?php
           for($i=0;$i<count($alph);$i++){
              if($tab == $alph[$i]) {
                echo "<li><span>{$alph[$i]}</span></li>\n";
              } else {
                echo "<li><a href=\"index.php?tab={$alph[$i]}\">{$alph[$i]}</a></li>\n";
              }
           }
	?>
		<li><span>Admin</span></li>
	</ul>
	</div>
	<div id="main">
		<div id="contents">
			<h2><?php echo $sub_title; ?></h2>			
<?php
$MSG=htmlentities($_REQUEST['MSG']);
$AddButton=htmlentities($_REQUEST['AddButton']);
$EditButton=htmlentities($_REQUEST['EditButton']);
$UpdateButton=htmlentities($_REQUEST['UpdateButton']);
$EditComboBox=htmlentities($_REQUEST['EditComboBox']);
$DeleteButton=htmlentities($_REQUEST['DeleteButton']);
$DeleteComboBox=htmlentities($_REQUEST['DeleteComboBox']);

if ($AddButton=="Add New Record") :
 $ErrorFlag = False ;
 $MSG = "" ;
 $CONTACT=htmlentities($_REQUEST['CONTACT']); 
 $IN=urlencode($_REQUEST['IN']); 
 $OUT=urlencode($_REQUEST['OUT']); 
 $DIALCODE=urlencode($_REQUEST['DIALCODE']);
 $EMAIL=$_REQUEST['EMAIL'];
 if ($CONTACT<"A") :
   $ErrorFlag = True ;
   $MSG .= "Contact Name cannot be blank. "   ;
 endif ;
 if ($OUT<"1") :
   $ErrorFlag = True ;
   $MSG .= "Contact Number cannot be blank. "   ;
 endif ;
 if ($IN=="%2A") :
   $IN = "*";
 endif ;
 if ($IN<>"*" AND $IN<"1") :
   $ErrorFlag = True ;
   $MSG .= "Your Extension cannot be blank. "   ;
 endif ;
 if (!$ErrorFlag) :

  $query="SELECT COALESCE(MAX(id)+1, 0) FROM user1";
  $db->exec($query);
  $result = $db->query($query);
  $row = $result->fetchArray();
  $NEXTREC = $row[0];

  $CONTACT = AddSlashes($CONTACT) ;
  $query = "INSERT INTO `user1` (`id`, `name`, `in`, `out`, `dialcode`, `email`) VALUES ($NEXTREC, '$CONTACT', '$IN', '$OUT', '$DIALCODE', '$EMAIL')";
  $result = $db->exec($query);
  $CONTACT = StripSlashes($CONTACT) ;
  $MSG="Record $NEXTREC added for $CONTACT: $OUT." ;
  $CONTACT="" ;
  $IN = "*" ;
  $OUT = "" ;
  $DIALCODE = "" ;
  echo "			<font color=\"blue\"><p class=\"note\">$MSG</p>\n" ;
 else : 
  echo "			<font color=\"red\"><p class=\"note\">$MSG</p></font>\n" ;
 endif ;
else :
 $CONTACT="" ;
 $IN = "*" ;
 $OUT = "" ;
 $DIALCODE = "" ;
endif ; 

if ($EditButton=="Edit This Record") :
 if ($EditComboBox=="PickOne") :
   $EditButton = "" ;
 else :
  $MSG = "Edit Record #$EditComboBox entry below. Then click Update Record button to save changes." ;
  echo "			<p class=\"note\">$MSG</p>\n" ;
 endif ;
endif ;

if ($UpdateButton=="Update This Record") :
 $ErrorFlag = False ;
 $MSG = "" ;
 $CONTACT2=htmlentities($_REQUEST['CONTACT2']); 
 $IN2=urlencode($_REQUEST['IN2']); 
 $OUT2=urlencode($_REQUEST['OUT2']); 
 $DIALCODE2=urlencode($_REQUEST['DIALCODE2']);
 $EMAIL2=urlencode($_REQUEST['EMAIL2']);
 $EMAIL2=str_replace("%40","@",$EMAIL2);
 $RECNO2=urlencode($_REQUEST['RECNO2']); 
 if ($CONTACT2<"A") :
   $ErrorFlag = True ;
   $MSG .= "Contact Name cannot be blank. "   ;
 endif ;
 if ($OUT2<"1") :
   $ErrorFlag = True ;
   $MSG .= "Contact Number cannot be blank. "   ;
 endif ;
 if ($IN2=="%2A") :
   $IN2 = "*";
 endif;
 if ($IN2<>"*" AND $IN2<"1") :
   $ErrorFlag = True ;
   $MSG .= "Your Extension cannot be blank. "   ;
 endif ;
 if (!$ErrorFlag) :
  $CONTACT2 = AddSlashes($CONTACT2) ;
  $query = "UPDATE `user1` SET `name` = '$CONTACT2', `in` = '$IN2', `out` = '$OUT2', `dialcode` = '$DIALCODE2',`email` = '$EMAIL2' WHERE `id` = $RECNO2 LIMIT 1";
//  $db = new SQLite3('/var/lib/asterisk/agi-bin/asteridex.sqlite');
  $result = $db->exec($query);
  $CONTACT2 = StripSlashes($CONTACT2) ;
  $MSG="Record #" . $RECNO2 . " updated for $CONTACT2: $OUT2." ;
  $EMAIL2="" ;
  $CONTACT2="" ;
  $IN2 = "*" ;
  $OUT2 = "" ;
  $RECNO2="" ;
  $DIALCODE2="" ;
  $EMAIL="" ;
  $CONTACT="" ;
  $IN = "*" ;
  $OUT = "" ;
  $DIALCODE = "" ;
  echo "			<font color=\"blue\"><p class=\"note\">$MSG</p>\n" ;
 else : 
  echo "			<font color=\"red\"><p class=\"note\">$MSG</p></font>\n" ;
  $EditButton="Edit This Record" ;
  $EMAIL="" ;
  $CONTACT="" ;
  $IN = "*" ;
  $OUT = "" ;
  $DIALCODE = "" ;
 endif ;
endif ;

if ($DeleteButton=="Delete This Record") :
 if ($DeleteComboBox=="PickOne") :
   $DeleteButton = "" ;
 else :
  $query = "DELETE FROM `user1` WHERE `id` = $DeleteComboBox LIMIT 1";
  $result = $db->exec($query);
  $DeleteButton = "" ;
  $MSG = "<font color=\"red\">Record $DeleteComboBox entry DELETED as requested.</font>" ;
  echo "			<p class=\"note\">$MSG</p>\n" ;
 endif ;
endif ;



if (!$MSG) :
 $MSG="Use the options below to Add, Edit, or Delete entries from the AsteriDex database." ;
 echo "			<p class=\"note\">$MSG</p>\n" ;
endif ; 
			

echo "<P>\n" ;
echo "<TABLE cellSpacing=0 cellPadding=15 width=\"100%\" border=0>\n" ;
echo "<TBODY>\n";
echo "<TR width=\"100%\">\n" ;
echo "<TD class=dir vAlign=top align=left width=\"100%\">\n" ;
echo "<TABLE cellSpacing=1 cellPadding=8 border=0>\n" ;
echo "<TBODY><TR>\n";

echo "<TD vAlign=top width=\"33%\"><FONT face=verdana,sans-serif>\n" ;
echo "<H2>" ;
echo "Add Entry</H2><BR>\n" ;
echo "<FORM NAME=\"AddFORM\" ACTION=\"./admin.php\" METHOD=POST>\n";
echo "<BR>Contact Name<BR><INPUT TYPE=TEXT NAME=\"CONTACT\" VALUE=\"$CONTACT\" ID=\"CONTACT\">\n" ;
echo "<BR>Contact Phone Number<BR><INPUT TYPE=TEXT NAME=\"OUT\" VALUE=\"$OUT\" ID=\"OUT\">\n" ;
echo "<BR>Your Extension<BR><INPUT TYPE=TEXT NAME=\"IN\" VALUE=\"$IN\" ID=\"IN\">\n" ;
echo "<BR>Dial Code<BR><INPUT TYPE=TEXT NAME=\"DIALCODE\" VALUE=\"$DIALCODE\" ID=\"DIALCODE\">\n" ;
echo "<BR>Email Address<BR><INPUT TYPE=TEXT NAME=\"EMAIL\" VALUE=\"$EMAIL\" ID=\"EMAIL\">\n" ;
echo "<BR><BR><INPUT TYPE=SUBMIT NAME=\"AddButton\" VALUE=\"Add New Record\" ID=\"AddButton\">\n" ;
echo "</FORM>\n";
echo "</FONT></TD>\n";

echo "<TD vAlign=top width=\"33%\"><FONT face=verdana,sans-serif>\n" ;
echo "<H2>" ;
echo "Edit Entry</H2><BR>\n" ;

if ($EditButton<>"Edit This Record") :
 echo "<BR><BR><FORM NAME=\"EditFORM\" ACTION=\"./admin.php\" METHOD=POST>\n";

  $sql_select = "SELECT * FROM user1 WHERE id>0 order by name" ;
  $db->exec($sql_select);
  $sql_result = $db->query($sql_select);

  echo "                    <SELECT ID=\"EditComboBox\" NAME=\"EditComboBox\">";
  echo "                        <OPTION VALUE=\"PickOne\" SELECTED>Select Contact to Edit:</OPTION>";
  while ($row = $sql_result->fetchArray()) {
   echo "                                    <OPTION VALUE=\"$row[0]\">" . $row[1] . "</OPTION>";
  }
  echo "                    </SELECT>";
  echo "<BR><BR><INPUT TYPE=SUBMIT NAME=\"EditButton\" VALUE=\"Edit This Record\" ID=\"EditButton\">\n" ;
  echo "</FORM>\n";
else :
 if (!$ErrorFlag) :
  $sql_select = "SELECT * FROM user1 WHERE id=$EditComboBox" ;
  $db->exec($sql_select);
  $sql_result = $db->query($sql_select);
  $row = $sql_result->fetchArray();
  $RECNO2 = $row["id"] ;
  $CONTACT2=$row["name"] ;
  $IN2 = $row["in"] ;
  $OUT2 = $row["out"] ;
  $DIALCODE2 = $row["dialcode"];
  $EMAIL2 = $row["email"];
  $EMAIL2 = str_replace("%40","@",$EMAIL2);
 endif ;
  echo "<FORM NAME=\"UpdateFORM\" ACTION=\"./admin.php\" METHOD=POST>\n";
  echo "<font color=\"blue\">Record #" . $RECNO2 . "</font>\n";
  echo "<INPUT TYPE=HIDDEN NAME=\"RECNO2\" VALUE=\"$RECNO2\" ID=\"RECNO2\">\n" ;
  echo "<BR>Contact Name<BR><INPUT TYPE=TEXT NAME=\"CONTACT2\" VALUE=\"$CONTACT2\" ID=\"CONTACT2\">\n" ;
  echo "<BR>Contact Phone Number<BR><INPUT TYPE=TEXT NAME=\"OUT2\" VALUE=\"$OUT2\" ID=\"OUT2\">\n" ;
  echo "<BR>Your Extension<BR><INPUT TYPE=TEXT NAME=\"IN2\" VALUE=\"$IN2\" ID=\"IN2\">\n" ;
  echo "<BR>Dial Code<BR><INPUT TYPE=TEXT NAME=\"DIALCODE2\" VALUE=\"$DIALCODE2\" ID=\"DIALCODE2\">\n" ;
  echo "<BR>Email Address<BR><INPUT TYPE=TEXT NAME=\"EMAIL2\" VALUE=\"$EMAIL2\" ID=\"EMAIL2\">\n" ;
  echo "<BR><BR><INPUT TYPE=SUBMIT NAME=\"UpdateButton\" VALUE=\"Update This Record\" ID=\"UpdateButton\">\n" ;
  echo "</FORM>\n";
endif ;
echo "</FONT></TD>\n";

// --------- Third column -- Delete record

echo "<TD vAlign=top width=\"33%\"><FONT face=verdana,sans-serif>\n" ;
echo "<H2>" ;
echo "Delete Entry</H2><BR>\n" ;
 echo "<BR><BR><FORM NAME=\"EditFORM\" ACTION=\"./admin.php\" METHOD=POST>\n";
  $sql_select = "SELECT * FROM user1 WHERE id>0 order by name" ;
  $db->exec($sql_select);
  $sql_result = $db->query($sql_select);
  echo "                    <SELECT ID=\"DeleteComboBox\" NAME=\"DeleteComboBox\">";
  echo "                        <OPTION VALUE=\"PickOne\" SELECTED>Select Contact to Delete:</OPTION>";
  while ($row = $sql_result->fetchArray()) {
   echo "                                    <OPTION VALUE=\"$row[0]\">" . $row[1] . "</OPTION>";
  }
  echo "                    </SELECT>";
  echo "<BR><BR><INPUT TYPE=SUBMIT NAME=\"DeleteButton\" VALUE=\"Delete This Record\" ID=\"DeleteButton\">\n" ;
  echo "</FORM>\n";
echo "</FONT></TD>\n";


echo "</TR></TBODY>\n" ;
echo "</TABLE></TD></TR></TBODY></TABLE></P>\n";


?>
		</div>
	</div>
</body>
</html>
<?php $db->close(); ?>
