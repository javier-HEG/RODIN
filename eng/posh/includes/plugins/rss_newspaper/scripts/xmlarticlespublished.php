<?php
# ***************************************
# newspaper articles
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/xmlarticlespublished.php";
//includes
require_once('includes.php');

$xmlfile=new xmlFile();
$xmlfile->header("newarticles");

$DB->getResults("SELECT title,header_img FROM rssnewspaper WHERE id=%u AND author_id=%u",$DB->escape($_GET["id"]),$DB->escape($_SESSION['user_id']));
$row=$DB->fetch(0);
echo "<title><![CDATA[".$row["title"]."]]></title>";
echo "<header><![CDATA[".$row["header_img"]."]]></header>";
$DB->freeResults();

$DB->getResults("SELECT a.title AS art_title,a.link,a.description,a.image,a.pubdate,d.title AS feed_title,a.feed_id FROM feed_articles AS a,rssnewspaper AS b,rssnewspaper_feeds AS c,dir_rss AS d WHERE b.id=%u AND b.id=c.newspaper_id AND b.author_id=%u AND c.feed_id=a.feed_id AND a.feed_id=d.id AND a.id>c.latest_read_id ORDER BY a.feed_id,a.id DESC",$DB->escape($_GET["id"]),$DB->escape($_SESSION['user_id']));

$nbArticle=0;
$feed="";
while ($row = $DB->fetch(0))
{
	//not more than 20 articles by feed
	if ($feed!=$row["feed_id"])
	{
		$feed=$row["feed_id"];
		$nbArticle=1;
	}
	else{
		$nbArticle++;
	}
	if ($nbArticle<21)
	{
		echo "<article>";
		echo "<feed><![CDATA[".$row["feed_title"]."]]></feed>";
		echo "<title><![CDATA[".$row["art_title"]."]]></title>";
		echo "<link><![CDATA[".$row["link"]."]]></link>";
		echo "<desc><![CDATA[".$row["description"]."]]></desc>";
		echo "<image><![CDATA[".$row["image"]."]]></image>";
		echo "<pubdate><![CDATA[".$row["pubdate"]."]]></pubdate>";
		echo "</article>";
	}
}
$DB->freeResults();

$xmlfile->footer();

$DB->close();

?>