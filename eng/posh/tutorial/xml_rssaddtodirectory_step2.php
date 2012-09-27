<?php
$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
//$granted="A";
$pagename="tutorial/xml_rssaddtodirectory_step2.php";
$errLog="";
$tabname="tutorial";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("rssadd");

$DB->getResults($rssaddtodirectory_addFeed,$DB->escape($_GET["id"]));
$row=$DB->fetch(0);
$rss=$row["url"];
$pfid=$row["id"];
echo "<url><![CDATA[".$rss."]]></url>";
echo "<id>".$pfid."</id>";
$DB->freeResults();

$auth=isset($_GET["auth"])?$_GET["auth"]:"";

//Check that the feed is not already registered in the DB
$DB->getResults($rssaddtodirectory_getDefvar);
$rssalreadyregistered=false;
while ($row=$DB->fetch(0))
{
	if (strpos($row["defvar"],$rss)>0) { echo "<registered>1</registered>"; }
	if (strpos($row["defvar"],urlencode($rss))>0) { echo "<registered>1</registered>"; }
}
$DB->freeResults();

$file->footer();
?>