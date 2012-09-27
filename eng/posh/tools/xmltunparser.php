<?php
header("Content-type: text/xml; charset=UTF-8");
//error_reporting(0);
echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'><rss version="2.0" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"><channel>';
function getVal($s,$tag){
	//return preg_split("/<\/?".$tag."(>| )/",$s);
	$spart=preg_split("/<\/?".$tag."/",$s);
	if (count($spart)==1){return "";}
	else {
		$value=$spart[1];
		if (strpos($value," />")===false){
			if ($value{0}==" " || $value{0}==">"){
				if ($value{0}==">"){return substr($value,1);}
				if ($value{0}==" "){
					$pos=strpos($value,">");
					return substr($value,$pos+1);
				}
			} else {return "";}
		} else {
			$pos=strpos($value," />");
			if (strpos($value,">")==$pos+2){return "";}
			else {return substr($value,strpos($value,">")+1);}
		}
	}
}
function getProps($s,$tag){
	//return preg_split("/<\/?".$tag."(>| )/",$s);
	$spart=preg_split("/<\/?".$tag."/",$s);
	if (count($spart)==1){return "";}
	else {
		$value=$spart[1];
		if ($value{0}==" "){
			if ($value{0}==" "){
				$pos=strpos($value,">");
				return substr($value,0,$pos);
			}
		} else {return "";}
	}
}
function getProp($s,$prop){
//	if (preg_match('/('.$prop.'=")(.*?)(")/is',$s,$pattern) || preg_match("/(".$prop."=')(.*?)(')/is",$s,$pattern)){
	if (preg_match('/('.$prop.'=("|\'))(.*?)(("|\'))/is',$s,$pattern)){
		return $pattern[3];
	} else return "";
}
function formatData($s){
	$s=str_replace("]]>","",str_replace("<![CDATA[","",$s));
	//$s=html_entity_decode($s,ENT_NOQUOTES);
	//$s=utf8_decode($s);
	//$s=htmlentities($s,ENT_NOQUOTES,'UTF-8');
	//if (!isUtf8($s)) $s=utf8_encode($s);
    //$s = html_entity_decode($s);
	//$s=urlencode($s);
	//$s=htmlentities($s);
    //$s= preg_replace('/([^\x09\x0A\x0D\x20-\x7F]|[\x21-\x2F]|[\x3A-\x40]|[\x5B-\x60])/e', '"&#".ord("$0").";"', $s);
	$s=str_replace(array("é","è","à","ù","ç","ê","î","ô","û","â"),array("&eacute;","&egrave;","&agrave;","&ugrave;","&ccedil;","&ecirc;","&icirc;","&ocirc;","&ucirc;","&acirc;"), $s);
	$s="<![CDATA[".$s."]]>";
	return $s;
}
function isutf($string){
	return preg_match('%(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+%xs', $string);
}
function toUtf8($s){
	//$s = str_replace(array("&","<",">","é","è","à","ù","ç","ê","î","ô","û","â"),array("&","<",">","é","è","à","ù","ç","ê","î","ô","û","â"), $s);
	//$s= preg_replace('/([^\x09\x0A\x0D\x20-\x7F]|[\x21-\x2F]|[\x3A-\x40]|[\x5B-\x60])/e', '"&#".ord("$0").";"', $s);
	$check=preg_match('%(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+%xs', $s);
	if ($check==false){
		$this->encoding="other";
		$s=utf8_encode($s);
$s=str_replace("</title>","kk</title>",$s);
	}
		
	return $s;
}

$t=$_SERVER['QUERY_STRING'];
$t=preg_replace("/url=/","",$t,1);
if ($t=="") exit();

$fp = fopen($t,"r");
$x = "";
if ($fp){
	while (!feof($fp)){$x.= fgets($fp, 8184);}
	//if not utf8, encode
	$x=toUtf8($x);
	//separate feed parts based on item tag
	$parts=preg_split("/<\/?item(>| )/",$x);
	//treat feed header
	$title=getVal($parts[0],"title");
	if ($title!="") echo "<title>". formatData($title)."</title>";
	$link=getVal($parts[0],"link");
	if ($link!="") echo "<link>". formatData($link)."</link>";
	$date=getVal($parts[0],"pubDate");
	if ($date=="") $date=getVal($parts[0],"pubdate");
	if ($date=="") $date=getVal($parts[0],"lastBuildDate");
	if ($date=="") $date=getVal($parts[0],"lastbuilddate");
	if ($date!="") echo "<date>". formatData($date)."</date>";
	
	if (count($parts)==1){
		$parts=preg_split("/<\/?entry(>| )/",$x);
	}

	// treat and display information of each ITEM element
	for($i=1;$i<count($parts)-1;$i+=2){
echo "<item>";
		$itemTitle=getVal($parts[$i],"title");
		if ($itemTitle!="") echo "<title>". formatData($itemTitle)."</title>";
		$itemLink=getVal($parts[$i],"link");
		if ($itemLink==""){$itemLink=getProps($parts[$i],"link");$itemLink=getProp($parts[$i],"href");}
		if ($itemLink!=""){
			if ($itemLink{0}==chr(13)){$itemLink=getProp($parts[$i],"href");}
			echo "<link>". formatData($itemLink)."</link>";
		}
		$itemDesc=getVal($parts[$i],"content:encoded");
		if ($itemDesc=="") $itemDesc=getVal($parts[$i],"description");
		if ($itemDesc=="") $itemDesc=getVal($parts[$i],"summary");
		if ($itemDesc!="") echo "<description>". formatData($itemDesc)."</description>";
		$itemDate=getVal($parts[$i],"pubDate");
		if ($itemDate=="") $itemDate=getVal($parts[$i],"pubdate");
		if ($itemDate=="") $itemDate=getVal($parts[$i],"dc:date");
		if ($itemDate=="") $itemDate=getVal($parts[$i],"published");
		if ($itemDate=="") $itemDate=getVal($parts[$i],"date");
		if ($itemDate!="") echo "<date>". formatData($itemDate)."</date>";
		$itemEnclosure=getProps($parts[$i],"enclosure");
		if ($itemEnclosure!=""){
			$type=getProp($itemEnclosure,"type");
			if ($type!=""){
				$url=getProp($itemEnclosure,"url");
				if ($url!="") $url=formatData($url);
				if ($type=="audio/mpeg") echo "<audio>".$url."</audio>";
				else if ($type=="video/x-mp4" || $type=="video/mp4") echo "<video>".$url."</video>";
				else if ($type=="image/jpeg") echo "<image>".$url."</image>";
				else if ($type=="img/jpeg") echo "<image>".$url."</image>";
				else if ($type=="image/png") echo "<image>".$url."</image>";
				else if ($type=="image/gif") echo "<image>".$url."</image>";
				else echo "<other>".$url."</other>";
			}
		}
		$itemId=getVal($parts[$i],"guid");
		if ($itemId!="") echo "<id>". formatData($itemId)."</id>";
echo "</item>";
	}
}
else {echo "do not process";}
echo "</channel></rss>";
?>