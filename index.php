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
   $tab = isset($_GET['tab']) ? $_GET['tab'] : "ABC";
   $alph = array("ABC","DEF","GHI","JKL","MNO","PQRS","TUV","WXYZ")
?>

<body>
	<h1><?php echo $title; ?></h1>
	<form id="setExtension" method="POST" action="index.php">
	<label>Extension/Line:</label>
<?php
echo"	<input type=\"text\" name=\"sipID\" size=\"21\" max_size=\"21\" value=\"$defaultExt/$defaultLine\">";
// echo"	<input type=\"submit\" name=\"submit\" value=\"save\" />";
?>
	</form>
	<div id="header">
	<ul id="primary">
	<?php
	   for($i=0;$i<count($alph);$i++){
	      if(strtoupper($tab) == strtoupper($alph[$i])) {
		echo "<li><span>{$alph[$i]}</span></li>\n";
	      } else {
	        echo "<li><a href=\"index.php?tab={$alph[$i]}\">{$alph[$i]}</a></li>\n";
	      }
	   }

 	if (file_exists("admin")) :
 	echo "		<li><a href=\"admin.php\">Admin</a></li>\n" ;
 	endif ;
?> 
	</ul>
	</div>
	<div id="main">
		<div id="contents">
			<h2><?php echo $sub_title; ?></h2>
			<p class="note">Select the desired tab above. Then click below on the name of the person or business to call.</p>

<?php

echo "<P>\n" ;
echo "<TABLE cellSpacing=0 cellPadding=15 width=\"100%\" border=0>\n" ;
echo "<TBODY>\n";
echo "<TR width=\"100%\">\n" ;
echo "<TD class=dir vAlign=top align=left width=\"100%\">\n" ;
echo "<TABLE cellSpacing=1 cellPadding=8 border=0 width=\"100%\">\n" ;
echo "<TBODY><TR>\n";

// $db = new SQLite3('/var/lib/asterisk/agi-bin/asteridex.sqlite');

for($i=0;$i<strlen($tab);$i++){

	$chr = substr($tab,$i,1);
	if(strlen($tab) == 3) {
		$w = "33%";
	} else {
		$w = "25%";
	}
	echo "<TD vAlign=top width=\"$w\"><FONT face=verdana,sans-serif>\n" ;
	$query_string="SELECT * FROM user1 where name between '$chr' AND '$chr".'zzzz'."' order by name asc";
	$db->exec($query_string);
	$result = $db->query($query_string);
	while ($row = $result->fetchArray()) {
 	echo "<A HREF=\"javascript:void(window.open('./callboth.php?SEQ=958217" . "&amp;IN=" . $defaultLine . "&amp;OUT=" . $row["out"]  . "',%20'Window2',%20config='height=250,width=550'))\" onMouseover=\"window.status='".$row["out"]." " .$row["dialcode"] ."'; return true\" onMouseout=\"window.status=' '; return true\">" . $row["name"] . "<span> " .$row["out"] . " " . $row["dialcode"]   . "</span>" . "</A><BR><BR>\n" ;
	}
	echo "</FONT></TD>\n";
}


echo "</TR></TBODY>\n" ;
echo "</TABLE></TD></TR></TBODY></TABLE></P>\n";


?>
		</div>
	</div>
</body>
</html>
<?php $db->close(); ?>
