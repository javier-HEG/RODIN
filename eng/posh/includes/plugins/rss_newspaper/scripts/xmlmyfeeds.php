<?php
# ***************************************
# newspaper : user feeds
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="includes/plugins/rss_newspaper/scripts/xmlmyfeeds.php";
//includes
require_once('includes.php');

$xmlfile=new xmlFile();
$xmlfile->header("myrsswidgets");

$DB->getResults("SELECT a.variables,b.name FROM module AS a,dir_item AS b WHERE a.item_id=b.id AND a.user_id=%u AND b.format='R' ",$DB->escape($_SESSION['user_id']));

while ($row = $DB->fetch(0))
{
	echo "<widget>";
	echo "<variables><![CDATA[".$row["variables"]."]]></variables>";
	echo "<title><![CDATA[".$row["name"]."]]></title>";
	echo "</widget>";
}
$DB->freeResults();

$xmlfile->footer();

$DB->close();

?>