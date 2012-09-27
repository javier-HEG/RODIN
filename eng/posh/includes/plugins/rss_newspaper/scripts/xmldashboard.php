<?php
# ***************************************
# newspaper dashboard
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/xmldashboard.php";
//includes
require_once('includes.php');

$xmlfile=new xmlFile();
$xmlfile->header("dashboard");

$DB->getResults("SELECT b.id,a.title,b.pubdate,b.filename,c.long_name FROM rssnewspaper AS a, rssnewspaper_publication AS b,users AS c WHERE a.id=b.newspaper_id AND a.status='O' AND b.status='O' AND b.access=1 AND a.author_id=c.id ORDER BY b.id DESC LIMIT 0,10 ");

while ($row = $DB->fetch(0)){
	echo "<publication>";
	echo "<id>".$row["id"]."</id>";
	echo "<title><![CDATA[".$row["title"]."]]></title>";
	echo "<pubdate><![CDATA[".$row["pubdate"]."]]></pubdate>";
	echo "<filename><![CDATA[".$row["filename"]."]]></filename>";
	echo "<author><![CDATA[".$row["long_name"]."]]></author>";
	echo "</publication>";
}
$DB->freeResults();

$DB->getResults("SELECT b.id,a.title,b.pubdate,b.filename FROM rssnewspaper AS a, rssnewspaper_publication AS b WHERE a.id=b.newspaper_id AND a.status='O' AND b.status='O' AND a.author_id=%u ORDER BY b.id DESC LIMIT 0,10 ",$DB->escape($_SESSION['user_id']));

while ($row = $DB->fetch(0)){
	echo "<mypublication>";
	echo "<id>".$row["id"]."</id>";
	echo "<title><![CDATA[".$row["title"]."]]></title>";
	echo "<pubdate><![CDATA[".$row["pubdate"]."]]></pubdate>";
	echo "<filename><![CDATA[".$row["filename"]."]]></filename>";
	echo "</mypublication>";
}
$DB->freeResults();

$DB->getResults("SELECT id,title FROM rssnewspaper WHERE author_id=%u AND status='O' ",$DB->escape($_SESSION['user_id']));

while ($row = $DB->fetch(0)){
	echo "<mynewspaper>";
	echo "<id>".$row["id"]."</id>";
	echo "<title><![CDATA[".$row["title"]."]]></title>";
	echo "</mynewspaper>";
}
$DB->freeResults();

$xmlfile->footer();

$DB->close();

?>