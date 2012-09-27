<?php
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
header("Content-type: text/xml; charset=UTF-8");
$a=$_GET["auth"];
$tp=$_GET["ptyp"];
$t=$_SERVER['QUERY_STRING'];
$t=substr($t,strpos($t,"url")+4);
//$t=preg_replace("/url=/","",$t,1);
if ($tp=="xml") header("Content-type: text/xml; charset=UTF-8");
if ($t=="") exit();

require_once('../includes/feed.inc.php');

$h = new feed($t);
$h->http->put_authorization("Basic $a");
echo  $h->load();
?>