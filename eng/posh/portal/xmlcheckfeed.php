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
# Check feed and add new in DB
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=0;
$pagename="portal/xmcheckfeed.php";
//includes

require_once('includes.php');
require_once('../includes/feed.inc.php');
require_once('../includes/http.inc.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();
$file->header("checks");

$url=isset($_POST["url"])?$_POST["url"]:exit("no url mentioned");
$moduleIcon = "../modules/quarantine/rss";
$imgType =  ".ico";

if(isset($_POST["id"])) {
	$moduleIcon="../modules/quarantine/icon".$_POST["id"];
}

$DB->getResults($xmlcheckfeed_getIconId,$DB->quote($url));
if ($DB->nbResults()==0)
{
	echo "<log><![CDATA[".$DB->quote($url)."]]></log>";

	//check if rss page exists
	$h = new http($url);
	$check=$h->get();
	if (empty($check))
	{
		echo "<error>1</error>";
	}
	else
	{
		//get icon
		$pos=strpos($url,"/",10);
		$icon=substr($url,0,$pos)."/favicon.ico";
		$hi = new http($icon);
		$iconfile=$hi->get();
		$imgType = $hi->getImageType($icon);
		//if iconfile empty or http error code return
		if (empty($iconfile) || strlen($iconfile)<4 || substr($iconfile,0,1)==" " || substr($iconfile,0,1)=="<")
		{
			//$moduleIcon="../modules/quarantine/icon".$feedid;
			$imgType = ".gif";
			copy("../modules/pictures/rss.gif",$moduleIcon.$imgType);
		}
		else
		{
			if ($f_w = fopen($moduleIcon.$imgType,'w+'))
			{
				fwrite($f_w,$iconfile);
				fclose($f_w);
			}
		}
	}
	//add feed in dir_rss
	$DB->execute($xmlcheckfeed_setUrlAndIcon,$DB->quote($url),$DB->quote($moduleIcon.$imgType));
	$feedid=$DB->getId();
	if(!isset($_POST["id"])) {
		rename($moduleIcon.$imgType,$moduleIcon.$feedid.$imgType);
		$moduleIcon .= $feedid;
		$DB->execute($xmlfeeds_setIconId,$DB->quote($moduleIcon.$imgType),$DB->escape($feedid));
	}
	echo "<id>".$feedid."</id>";
	echo "<icon><![CDATA[".$moduleIcon.$imgType."]]></icon>";
}
else
{	
	$row=$DB->fetch(0);
	$extensionlogo = strrchr($row["iconid"],'.');
	echo "<id>".$row["id"]."</id>";
	
	if( isset($row["iconid"]) && !empty($row["iconid"]) ) {
		if( !isset($_POST["id"]) ) {
			echo "<icon><![CDATA[".$row["iconid"]."]]></icon>";
		} else {
			copy($row["iconid"],$moduleIcon.$extensionlogo);
			echo "<icon><![CDATA[".$moduleIcon.$extensionlogo."]]></icon>";
		}
		
	}
}
$DB->freeResults();

$file->footer("checks");

$DB->close();
?>