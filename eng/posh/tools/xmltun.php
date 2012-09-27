<?php
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
$tp=isset($_GET["ptyp"])?$_GET["ptyp"]:"";
$t=$_SERVER['QUERY_STRING'];
$t=substr($t,strpos($t,"url")+4);
if ($tp=="xml") header("Content-type: text/xml; charset=UTF-8");
if ($t=="") exit();
require_once('../includes/feed.inc.php');

$feed = new feed($t);
echo $feed->load();
?>