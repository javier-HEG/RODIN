<?php
	header("content-type: application/xml");
	echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';
	require('../tools/rssparser/rss_fetch.inc');
	error_reporting(0);
	$url=$_GET["u"];
	$rss = fetch_rss($url);
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
<header><![CDATA[<table width="100%" height="30" border="0" cellpadding="0" cellspacing="2" bgcolor="#ffffff"><tr><td width="35%" style="border:1px solid #000000" bgcolor="#F5F500">&nbsp;</td><td style="border:1px solid #000000" bgcolor="#3333FF">&nbsp;&nbsp;</td></tr></table>]]></header>
<?php
	if (!$item=$rss->items[0])
	{echo "<title>aucun article pour le moment ...</title>";}
	else {
		$minelement=min(20,count($rss->items));
		for($inc=0;$inc<$minelement;$inc++){
			$item=$rss->items[$inc];
?>
<item>
<title><![CDATA[<?php echo $item['title'];?>]]></title>
<desc><![CDATA[<?php echo $item['description'];?>]]></desc>
<image><?php echo $item['enclosure'][0]['url'];?></image>
<link><![CDATA[<?php echo $item['link'];?>]]></link>
<pubdate>10/10/06</pubdate>
</item>
<?php
		}
	}
?>
</Module>