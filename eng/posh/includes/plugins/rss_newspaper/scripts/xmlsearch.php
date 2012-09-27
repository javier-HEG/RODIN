<?php
# ***************************************
# newspaper dashboard
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/xmlsearch.php";
//includes
require_once('includes.php');

$xmlfile=new xmlFile();
$xmlfile->header("results");

$kw=str_replace(",","','",$_GET["searchtxt"]);

$DB->getResults("SELECT b.id,a.title,b.pubdate,b.filename FROM rssnewspaper AS a, rssnewspaper_publication AS b,rssnewspaper_keywords AS c,search_keyword AS d WHERE a.id=b.newspaper_id AND a.status='O' AND b.status='O' AND b.access=1 AND a.id=c.newspaper_id AND c.kw_id=d.id AND d.label_simplified IN ('%s') ORDER BY b.id DESC LIMIT %u,11 ",$kw,$DB->escape($_GET["page"])*10);

while ($row = $DB->fetch(0)){
	echo "<result>";
	echo "<id>".$row["id"]."</id>";
	echo "<title><![CDATA[".$row["title"]."]]></title>";
	echo "<pubdate><![CDATA[".$row["pubdate"]."]]></pubdate>";
	echo "<filename><![CDATA[".$row["filename"]."]]></filename>";
	echo "</result>";
}
$DB->freeResults();

$xmlfile->footer();

$DB->close();

?>