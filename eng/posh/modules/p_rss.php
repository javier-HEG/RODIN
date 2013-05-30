<?php
// this file needs to be utf8 without BOM. Check accentuated chars : é à è ù
	error_reporting(0);
	header("content-type: application/xml");
	echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';
?>
<Module>
<UserPref name="nb" display_name="lblNbArticles" datatype="enum" default_value="5">
  <EnumValue value="1" display_value="1"/>
  <EnumValue value="2" display_value="2"/>
  <EnumValue value="3" display_value="3"/>
  <EnumValue value="5" display_value="5"/>
  <EnumValue value="10" display_value="10"/>
  <EnumValue value="15" display_value="15"/>
</UserPref>
<UserPref name="rssurl" display_name="lblRss" datatype="string" default_value="" />
<UserPref name="auth" display_name="." datatype="hidden" default_value="" />
<?php
	if (isset($_POST["rssurl"]) && 0)
	{
		require('../includes/config.inc.php');
		require('../tools/rssparser/rss_fetch.inc');
		
		if (__useproxy){
			if (isset($_POST["proxy"])){
				if ($_POST["proxy"]!=""){
					if ($_POST["proxy"]=="0")$url=__LOCALFOLDER."tools/xmltun.php?url=".$_POST["rssurl"];
					else $url=__LOCALFOLDER."tools/xmltunproxypac.php?url=".$_POST["rssurl"]."&proxy=".$_POST["proxy"];
				} else {$url=__LOCALFOLDER."tools/xmltunauth.php?auth=".$_POST["auth"]."&url=".$_POST["rssurl"];}
			}
//			$url=__LOCALFOLDER."tools/xmltunproxy.php?url=".$_POST["rssurl"];
		} else {
			if (isset($_POST["auth"]) && $_POST["auth"]!=""){
				$url=__LOCALFOLDER."tools/xmltunauth.php?auth=".$_POST["auth"]."&url=".$_POST["rssurl"];
			} else {
				$url=__LOCALFOLDER."tools/xmltun.php?url=".$_POST["rssurl"];
			}
		}
		
		//avoid the cache
		if (isset($_GET["pnocache"])){
			if (strpos($_POST["rssurl"],"?")===false){$url.="?pnocache=".$_GET["pnocache"];}
			else {$url.="&pnocache=".$_GET["pnocache"];}
		}
		
		$rss = fetch_rss($url);
		$main=$rss->channel;
//		if (isset($_POST["bar"]) AND $_POST["bar"]=="N"){
//			echo "<header><![CDATA[<table cellpadding='3' cellspacing='0' border='0' width='100%'><tr><td><img src='../modules/external/portaneo/rss.gif' align='absmiddle' /> <a href='".$main["link"]."' target=_blank><b>".$main["title"]."</b></a></td></tr></table>]]></header>";
//		} else {echo "<header />";}
		echo "<header />";
		if ($main["title"]){echo "<ftitle>".$main["title"]."</ftitle>";} else {echo "<ftitle>RSS feed</ftitle>";}
		if (!$item=$rss->items[0])
		{echo "<title>aucun article pour le moment ...</title>";}
		else {
			$nbart=isset($_POST["nb"])?$_POST["nb"]:5;
			$minelement=min($nbart,count($rss->items));
			for($inc=0;$inc<$minelement;$inc++){
				$item=$rss->items[$inc];
?>
<item>
<title><![CDATA[<?php echo $item['title'];?>]]></title>
<desc><![CDATA[<?php echo $item['description'];?>]]></desc>
<?php 
	if (!Empty($item['link'])) echo "<link><![CDATA[".$item['link']."]]></link>";
	else if (!Empty($item['id'])) echo "<link><![CDATA[".$item['id']."]]></link>";
?>
<?php if ($item['enclosure'] AND $item['enclosure'][0]["type"]=="audio/mpeg") echo "<audio><![CDATA[".$item['enclosure'][0]['url']."]]></audio>";?>
<?php if ($item['enclosure'] AND $item['enclosure'][0]["type"]=="video/x-mp4") echo "<mpeg><![CDATA[".$item['enclosure'][0]['url']."]]></mpeg>";?>
<?php if ($item['enclosure'] AND $item['enclosure'][0]["type"]=="video/mp4") echo "<mpeg><![CDATA[".$item['enclosure'][0]['url']."]]></mpeg>";?>
<?php if ($item['enclosure'] AND ($item['enclosure'][0]["type"]=="image/jpeg" || $item['enclosure'][0]["type"]=="image/gif")) echo "<image><![CDATA[".$item['enclosure'][0]['url']."]]></image>";?>
<pubdate><?php
	if ($item['pubdate'] AND $item['pubdate']!="") {echo $item['pubdate'];}
	elseif ($item['dc']['date'] AND $item['dc']['date']!=""){echo $item['dc']['date'];}
	elseif ($item['published'] AND $item['published']!=""){echo $item['published'];}
	elseif ($pubdate=$rss->channel['pubdate']){echo $rss->channel['pubdate'];}
	elseif ($pubdate=$rss->channel['lastbuilddate']){ echo $rss->channel['lastbuilddate'];}
?></pubdate>
<?php if ($item['content']) echo "<content><![CDATA[".$item['content']['encoded']."]]></content>";?>
</item>
<?php
			}
		}
?>
<?php
	} else {
?>
 <header><![CDATA[<table cellpadding="3" cellspacing="0" border="0" width="100%"><tr><td><img src="../modules/external/portaneo/rss.gif" align="absmiddle" /> <a href="#" onclick="return $p.app.widgets.param.show(<?php echo $_POST["p"];?>)">add a RSS feed / ajouter un flux RSS</a></td></tr></table>]]></header>
<?php
	}
?>
</Module>