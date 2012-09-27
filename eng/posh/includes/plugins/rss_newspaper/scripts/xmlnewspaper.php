<?php
# ***************************************
# newspaper dashboard
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/xmlnewspaper.php";
//includes
require_once('includes.php');

$xmlfile=new xmlFile();
$xmlfile->header("newspaper");

$id=$_GET["id"];

$DB->getResults("SELECT id,title,description,header_img FROM rssnewspaper WHERE status='O' AND id=%u AND author_id=%u",$DB->escape($id),$DB->escape($_SESSION['user_id']));

if ($DB->nbResults()>0)
{
	$row=$DB->fetch(0);
	echo "<id>".$row["id"]."</id>";
	echo "<title><![CDATA[".$row["title"]."]]></title>";
	echo "<description><![CDATA[".$row["description"]."]]></description>";
	echo "<header><![CDATA[".$row["header_img"]."]]></header>";
	$DB->freeResults();

	$DB->getResults("SELECT DISTINCT feed_id FROM rssnewspaper_feeds WHERE newspaper_id=%u",$DB->escape($id));
	while ($row = $DB->fetch(0)){
		echo "<feed>";
		echo "<fid>".$row["feed_id"]."</fid>";
		echo "</feed>";
	}
	$DB->freeResults();

	$DB->getResults("SELECT b.label FROM rssnewspaper_keywords AS a,search_keyword AS b WHERE newspaper_id=%u AND kw_id=b.id",$DB->escape($id));
	while ($row = $DB->fetch(0)){
		echo "<tag>";
		echo "<label>".$row["label"]."</label>";
		echo "</tag>";
	}
	$DB->freeResults();
}

$xmlfile->footer();

$DB->close();

?>