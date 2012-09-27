<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of POSH (Portaneo Open Source Homepage) http://sourceforge.net/projects/posh/.

	POSH is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version

	POSH is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Posh.  If not, see <http://www.gnu.org/licenses/>.
*/
# ***************************************
# Get feeds articles
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=0;
//$granted="I";
$pagename="portal/xmlfeeds.php";
//includes
require_once('includes.php');
require_once('../includes/feed.inc.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("Module");
?>
<UserPref name="nb" display_name="lblNbArticles" datatype="enum" default_value="5">
  <EnumValue value="1" display_value="1"/>
  <EnumValue value="2" display_value="2"/>
  <EnumValue value="3" display_value="3"/>
  <EnumValue value="5" display_value="5"/>
  <EnumValue value="10" display_value="10"/>
  <EnumValue value="15" display_value="15"/>
  <EnumValue value="30" display_value="30"/>
  <EnumValue value="50" display_value="50"/>
</UserPref>
<UserPref name="popenin" display_name="openArticleIn" datatype="enum" default_value="0">
  <EnumValue value="0" display_value="inThisPage"/>
  <EnumValue value="1" display_value="inWebsite"/>
  <EnumValue value="2" display_value="inANewTab"/>
</UserPref>
<UserPref name="pdisplay" display_name="lblDisplay" datatype="enum" default_value="0">
  <EnumValue value="0" display_value="lblList"/>
  <EnumValue value="2" display_value="detailed"/>
  <EnumValue value="1" display_value="lblSNews"/>
</UserPref>
<UserPref name="rssurl" display_name="lblRss" datatype="readonly" default_value="" />
<UserPref name="pfid" display_name="." datatype="hidden" default_value="" />
<?php

if (isset($_POST["auth"]))
{
	echo '<UserPref name="auth" display_name="." datatype="hidden" default_value="" />';
	if ($_POST["auth"]!='x')
	{
		echo '<UserPref name="user" display_name="lblLogin" datatype="string" default_value="" />';
		echo '<UserPref name="pass" display_name="lblPassword" datatype="password" default_value="" />';
	}
}

?>
<header />
<?php

$nb=(isset($_POST["nb"])?$_POST["nb"]+1:11);//we take one more article to test if this is the last feed page
$pfid=$_POST["pfid"];
$refreshDelay=__refreshFeedsDelai;
$rssurl=(isset($_POST["rssurl"])?$_POST["rssurl"]:'');
$userAccessThisFeed=true;

if (isset($_POST["auth"])) $auth=$_POST["auth"];
if (isset($_POST["user"])) $auth=base64_encode($_POST["user"].":".$_POST["pass"]);
$proxy=isset($_POST["proxy"])?$_POST["proxy"]:"";
if (isset($auth) && empty($auth)){echo "<footer>auth</footer></Module>";exit();}

if (!isset($auth))
{
	include_once('../includes/refreshfeed.inc.php');

	echo "<ftitle><![CDATA[".$title."]]></ftitle>";

	if (__getNbArticleOfArchive)
	{
		$DB->getResults($xmlfeeds_getTotalNbArticles,$DB->escape($pfid));
		$row=$DB->fetch(1);
		$totalNbOfArticles=$row[0];
		$DB->freeResults();
		if ($_SESSION["user_id"])
		{
			$DB->getResults($xmlfeeds_getNbUnreadArticles,$pfid,$DB->escape($_SESSION['user_id']));
			$row=$DB->fetch(1);
			$NbOfUnreadArticles=$row[0];
			$DB->freeResults();
		} else $NbOfUnreadArticles=0;

		echo "<nbunread>".($totalNbOfArticles-$NbOfUnreadArticles)."</nbunread>";
	}

	if ($_SESSION["user_id"])
		$DB->getResults($xmlfeeds_getItemsWithStatus,$DB->escape($_SESSION['user_id']),$DB->escape($pfid),$DB->escape($_POST["s"]),$DB->escape($nb));
	else
		$DB->getResults($xmlfeeds_getItems,$DB->escape($pfid),$DB->escape($_POST["s"]),$DB->escape($nb));

	while ($row = $DB->fetch(0))
	{
		echo "<item>"
			."<id>".$row["id"]."</id>"
			."<title><![CDATA[".$row["title"]."]]></title>"
			."<link><![CDATA[".$row["link"]."]]></link>"
			."<desc><![CDATA[".$row["description"]."]]></desc>"
			."<pubdate>".$row["pubdate"]."</pubdate>"
			."<read>".$row["status"]."</read>"
			."<image>".$row["image"]."</image>"
			."<video><![CDATA[".$row["video"]."]]></video>"
			."<audio><![CDATA[".$row["audio"]."]]></audio>"
			."<source><![CDATA[".$row["source"]."]]></source>"
		."</item>";
	}	
	$DB->freeResults();
}
else
{
	$feed=new feed($rssurl);
	launch_hook('xmlfeeds_headeradditions',$feed,$proxy);
	$feed->auth=$auth;
	$RSSfile=$feed->loadAuth();
	$RSSarticles=$feed->getNewArticles($RSSfile,"","");
	if (count($RSSarticles)!=0)
	{
		$nbArticles=count($RSSarticles);
		for ($i=$_POST["s"]+1;$i<$nbArticles;$i++)
		{
			echo "<item>"
				."<title><![CDATA[".$RSSarticles[$i]["title"]."]]></title>"
				."<link><![CDATA[".$RSSarticles[$i]["link"]."]]></link>"
				."<desc><![CDATA[".$RSSarticles[$i]["desc"]."]]></desc>"
				."<pubdate>".$RSSarticles[$i]["date"]."</pubdate>"
				."<read>0</read>"
				."<image><![CDATA[".(isset($RSSarticles[$i]["image"])?$RSSarticles[$i]["image"]:"")."]]></image>"
				."<video><![CDATA[".(isset($RSSarticles[$i]["video"])?$RSSarticles[$i]["video"]:"")."]]></video>"
				."<audio><![CDATA[".(isset($RSSarticles[$i]["audio"])?$RSSarticles[$i]["audio"]:"")."]]></audio>"
				."<source><![CDATA[".$RSSarticles[$i]["source"]."]]></source>"	
			."</item>";
		}
	}
	echo "<ftitle><![CDATA[".$RSSarticles[0]["title"]."]]></ftitle>";
}

$file->footer("Module");

$DB->close();
?>