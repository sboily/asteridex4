<?php

require("/usr/share/php/smarty3/Smarty.class.php");

include_once("config/config.inc.php");
include_once("lib/restclient.php");

$tpl = new Smarty();

$tab = isset($_GET['tab']) ? $_GET['tab'] : "ABC";

$tpl->assign("alph", array("ABC","DEF","GHI","JKL","MNO","PQRS","TUV","WXYZ"));
$tpl->assign("title", $title);
$tpl->assign("sub_title", $sub_title);
$tpl->assign("tab", $tab);
$tpl->assign("defaultLine", $defaultLine);
$tpl->assign("defaultExt", $defaultExt);

if (file_exists("admin")) {
  $tpl->assign("admin", TRUE);
}

for($i=0;$i<strlen($tab);$i++) {
  $chr = substr($tab,$i,1);

  if(strlen($tab) == 3) {
    $w = "33%";
  } else {
    $w = "25%";
  }
  $tpl->assign("w", $w);

  $query_string="SELECT * FROM user1 where name between '$chr' AND '$chr".'zzzz'."' order by name asc";
  $db->exec($query_string);
  $result = $db->query($query_string);

  $i = 0;
  while ($row = $result->fetchArray()) {
    $entries[$i]['out'] = $row["out"];
    $entries[$i]['dialcode'] = $row["dialcode"];
    $entries[$i]['name'] = $row["name"];
    $i++;
  }
  $tpl->assign("entries", $entries);

}

$db->close();

$tpl->display("tpl/index.html");

?>
