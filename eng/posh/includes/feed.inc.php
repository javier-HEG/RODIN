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
# RSS feed treatment class
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é à è ù à
# ***************************************

require_once('http.inc.php');

class feed
{
	var $url;
	var $encoding="UTF-8";
	//var $encoding="utf8"; EM: error UTF-8 instead of utf8
	var $auth;
	var $http;
	/*
		Ctor
		Input :
			$url (string) : RSS feed url
	*/
	function feed($url)
	{
		$this->url = $url;
		$this->http = new http($url);
	}
	/*
	 * Open RSS
	 * Return :
	 *	file object
	 */
	function load()
	{
		$content= $this->http->get();
		if (!$content)
			return false;
		$content = $this->transcode($content);
		return $content;
	}
	/*
		loadAuth : Load authenticate RSS feed
		Output : RSS feed content
	*/
	function loadAuth()
	{
		$this->http->put_authorization("Basic $this->auth");
		return $this->transcode($this->http->get());
	}
	/*
		transcode : convert RSS feed content to UTF8
		Input :
			$content(string) : RSS feed content
	*/
	function transcode($content)
	{
		$encoding = "";
		if (preg_match('/<?xml[^>]*encoding=["\']([^>]*)?["\'][^>]*?>/',$content,$matches)!==false)
		{
			if (count($matches)>=2)
			{
				$encoding = $matches[1];
			}
		}
		if ($encoding=="")
		{
			// With HTTP headers
			$encoding = $this->http->get_header_subvalue("Content-Type","charset");
		}

		if (!empty($this->encoding) && function_exists('mb_convert_encoding'))
		{
			$content = @mb_convert_encoding($content,$this->encoding,$encoding);
			$content=preg_replace('/(<?xml[^>]*)encoding=["\']([^>"\']*)?["\']([^>]*?>)/','$1encoding="'.$this->encoding.'"$3',$content);
		}
		else if (!empty($encoding) && stristr($encoding,'ISO-8859-1')!==false)
		{
			$content = toUtf8($s);
		}
		return $content;
	}
	
	function toUtf8($s)
	{
		//$s = str_replace(array("&","<",">","é","è","à","ù","ç","ê","î","ô","û","â"),array("&","<",">","é","è","à","ù","ç","ê","î","ô","û","â"), $s);
		//$s= preg_replace('/([^\x09\x0A\x0D\x20-\x7F]|[\x21-\x2F]|[\x3A-\x40]|[\x5B-\x60])/e', '"&#".ord("$0").";"', $s);

		$check=preg_match('%(?:[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2})+%xs', $s);
		if ($check==false)
		{
			$this->encoding="other";
			$s=str_replace("’","'",$s);
			$s=utf8_encode($s);
		}
			
		return $s;
	}
	
	/*
	 * Get the RSS items
	 * Input :
	 *	$file : RSS file object
	 *	$lastLoaded : unique ID of the last loaded item of this feed
	 * Return :
	 *	RSS items in an array
	 */
	function getNewArticles($lastLoaded,$title)
	{
		$x = "";
		$arr=array();
		if (!empty($this->http->body))
		{
			$x = $this->http->body;
			$x = $this->transcode($x);
			//separate feed parts based on item tag
			$parts=preg_split("/<\/?item(>| )/",$x);
			//treat feed header
			if ($title=="")
			{
				$title=$this->getVal($parts[0],"title");
				if ($title!="") $arr[0]["title"]=$this->formatData($title);
				else $arr[0]["title"]="";
			}
			else $arr[0]["title"]="";
			
			$date=$this->getVal($parts[0],"pubDate");
			if ($date=="") $date=$this->getVal($parts[0],"pubdate");
			if ($date=="") $date=$this->getVal($parts[0],"lastBuildDate");
			if ($date=="") $date=$this->getVal($parts[0],"lastbuilddate");
	
			// treat and display information of each ITEM element
			if (count($parts)==1){$parts=preg_split("/<\/?entry(>| )/",$x);}
			$inc=1;
			for($i=1;$i<count($parts)-1;$i+=2)
			{
				$itemTitle=$this->getVal($parts[$i],"title");
		
				$itemLink=$this->getVal($parts[$i],"link");
				
				if ($itemLink=="")
				{
					preg_match_all("/
							(<\s?link[^\>]+>)
							/xmsi",$parts[$i],$itemLinkProps);
					for($k=0;$k<=count($itemLinkProps[0]);$k++) {
						if( $this->getProp($itemLinkProps[0][$k],"rel")=="" || $this->getProp($itemLinkProps[0][$k],"rel")=="alternate" ) {
							$itemLink = $this->getProp($itemLinkProps[0][$k],"href");
							break;
						}
					}
					/*	$itemLinkProps=$this->getProps($parts[$i],"link");
						$itemLink=$this->getProp($itemLinkProps,"href");
					*/
				}
				
				$itemId=$this->getVal($parts[$i],"guid");
				if ($itemId=="") $itemId=$itemLink.$itemTitle;
				//if item is already loaded, stop the loading process
				if (substr($itemId,0,255)==$lastLoaded) break;
				$arr[$inc]["id"]=$itemId;
				if ($itemTitle!="") $arr[$inc]["title"]=$this->formatData($itemTitle);
			
				if ($itemLink!="") {if ($itemLink{0}==chr(13)){$itemLink=$this->getProp($parts[$i],"href");};}
				$arr[$inc]["link"]=$this->formatData($itemLink);

				$itemSource=$this->getVal($parts[$i],"source");
				$arr[$inc]["source"]=($itemSource==''?'':$this->formatData($itemSource));
			
				$itemDesc=$this->getVal($parts[$i],"content:encoded");
				if ($itemDesc=="") $itemDesc=$this->getVal($parts[$i],"description");
				if ($itemDesc=="") $itemDesc=$this->getVal($parts[$i],"summary");
				$arr[$inc]["desc"]=$this->formatData($itemDesc);
				
				$itemDate=$this->getVal($parts[$i],"pubDate");
				if ($itemDate=="") $itemDate=$this->getVal($parts[$i],"pubdate");
				if ($itemDate=="") $itemDate=$this->getVal($parts[$i],"dc:date");
				if ($itemDate=="") $itemDate=$this->getVal($parts[$i],"published");
				if ($itemDate=="") $itemDate=$this->getVal($parts[$i],"date");
				if ($itemDate=="" && $date!="") $itemDate=$date;
				$arr[$inc]["date"]=$this->formatData($itemDate);
				
				$itemEnclosure=$this->getProps($parts[$i],"enclosure");
				if ($itemEnclosure!="")
				{
					$type=$this->getProp($itemEnclosure,"type");
					if ($type!="")
					{
						$url=$this->getProp($itemEnclosure,"url");
						if ($url!="")
						{
							$url=$this->formatData($url);
							if ($type=="audio/mpeg") $arr[$inc]["audio"]=$url;
							else if ($type=="video/x-mp4" || $type=="video/mp4" || $type=="video/mpeg") $arr[$inc]["video"]=$url;
							else if ($type=="image/jpeg" || $type=="img/jpeg" || $type=="image/png" || $type=="image/gif") $arr[$inc]["image"]=$url;
						}
					}
				}
				$inc++;
			}
		}
		else 
		{
			echo "<error><![CDATA[Returned file is empty !]]></error>";
		}
		return $arr;
	}
	/*
		getVal : get value of a xml tag in a string
		Inputs :
			$s (string) : string XML
			$tag (string) : tag searched
		Output :
			string, first value for the searched string
	*/
	function getVal($s,$tag){
		//return preg_split("/<\/?".$tag."(>| )/",$s);
		$spart=preg_split("/<\/?".$tag."/",$s);
		if (count($spart)==1)
		{
			return "";
		}
		else
		{
			$value=$spart[1];
			if (strpos($value,"/>")===false)
			{
				if ($value{0}==" " || $value{0}==">")
				{
					//if the tag in closed without properties
					if ($value{0}==">"){return substr($value,1);}
					if ($value{0}==" ")
					{
						$pos=strpos($value,">");
						return substr($value,$pos+1);
					}
				}
				else
				{
					return "";
				}
			}
			else
			{
				$pos=strpos($value,"/>");
				if (strpos($value,">")==$pos+1)
				{
					return "";
				}
				else
				{
					return substr($value,strpos($value,">")+1);
				}
			}
		}
	}
	/*
		getProps : get properties of a tag
	*/
	function getProps($s,$tag)
	{
		$spart=preg_split("/<\/?".$tag."/",$s);
		if (count($spart)==1)
		{
			return "";
		}
		else
		{
			$value=$spart[1];
			if ($value{0}==" ")
			{
				if ($value{0}==" ")
				{
					$pos=strpos($value,">");
					return substr($value,0,$pos);
				}
			}
			else
			{
				return "";
			}
		}
	}
	function getProp($s,$prop)
	{
		if (preg_match('/('.$prop.'=("|\'))(.*?)(("|\'))/is',$s,$pattern))
		{
			return $pattern[3];
		}
		else return "";
	}
	/*
		formatData : apply formating rules to xml data
		inputs:
			$s (string) : data to format
		Output :
			string, formated data
	*/
	function formatData($s)
	{
		$s=str_replace("]]>","",str_replace("<![CDATA[","",$s));
		//if ($this->encoding!="utf8"){
//			$s= preg_replace('/([^\x09\x0A\x0D\x20-\x7F]|[\x21-\x2F]|[\x3A-\x40]|[\x5B-\x60])/e', '"&#".ord("$0").";"', $s);
//			$s=str_replace(array("?"?"?"?"?"?"?"?"?"?,array("&eacute;","&egrave;","&agrave;","&ugrave;","&ccedil;","&ecirc;","&icirc;","&ocirc;","&ucirc;","&acirc;"), $s);
		//}
		return $s;
	}
}
?>