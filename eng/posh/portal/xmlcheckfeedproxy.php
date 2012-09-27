<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of COLLABORATION SUITE of POSH http://sourceforge.net/projects/posh/.

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
# Check feed and add new in DB (proxy version)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=0;
$pagename="portal/xmcheckfeedproxy.php";
//includes
require_once('includes.php');
require_once('../includes/feed.inc.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$xmlfile=new xmlFile();
$moduleIcon = "../modules/quarantine/rss";
$imgType =  ".ico";

$xmlfile->header("check");

$url=$_POST["url"];
if (isset($_POST["proxy"]))
{
	$proxy=$_POST["proxy"];
	$pos=strpos($proxy,":");
	$proxyserver=substr($proxy,0,$pos);
	$proxyport=substr($proxy,$pos+1);
}
else
{
	$proxyserver=__PROXYSERVER;
	$proxyport=__PROXYPORT;
}

$DB->getResults($xmlcheckfeedproxy_getRssAndIcon,$DB->quote($url));
if ($DB->nbResults()==0)
{
	//check if rss page exists
	$file = fsockopen($proxyserver,$proxyport);
	fputs($file, "GET $url HTTP/1.0\r\n");
	fputs($file, "Proxy-Authorization: Basic ".__PROXYCONNECTION."\r\n\r\n");

	if (!$file)
	{
		echo "<error>1</error>";
	}
	else
	{
		//add feed in dir_rss
		$DB->execute($xmlcheckfeedproxy_addFeed,$DB->quote($url),$DB->quote($proxyserver.":".$proxyport));
		$id=$DB->getId();

		//get icon
		$pos=strpos($url,"/",10);
		$url=substr($url,0,$pos)."/favicon.ico";
		$file = fsockopen($proxyserver,$proxyport);
		fputs($file, "GET $url HTTP/1.0\r\n");
		fputs($file, "Proxy-Authorization: Basic ".__PROXYCONNECTION."\r\n\r\n");
		if (!$file)
		{
			$moduleIcon="../modules/pictures/rss".$id.".gif";
			copy("../modules/pictures/rss.gif",$moduleIcon);
			// need to analyze response to know if return file is an icon or not ??
		}
		else
		{
			$cont="";
			
			while(!feof($file)) {$cont .= fgets($file,100000);}
			$h = new http( $url );
			$imgType = $h->getImageType( $url );
			$moduleIcon="../modules/pictures/rss".$id.$imgType;
			$cont = substr($cont, strpos($cont,"\r\n\r\n")+4);
			fclose($file);
			if (empty($cont) || strlen($cont)<4 || substr($cont,0,1)==" " || substr($cont,0,1)=="<")
			{
				copy("../modules/pictures/rss.gif",$moduleIcon);
			}
			else
			{
				$handle=fopen($moduleIcon,"w");
				if (!$handle) exit();
				if ( !flock ( $handle , LOCK_EX + LOCK_NB )) exit();
				fwrite ( $handle , $cont , strlen ( $cont ) );
				flock ( $handle , LOCK_UN );
				fclose ( $handle );
			}

			$DB->execute($xmlcheckfeedproxy_setIcon,$DB->quote($moduleIcon),$DB->escape($id));
		}
		echo "<id>".$id."</id>";
		echo "<icon>".$moduleIcon."</icon>";
	}
}
else
{
	$row=$DB->fetch(0);
	echo "<id>".$row["id"]."</id>";
	echo "<icon>".$row["iconid"]."</icon>";
}
$DB->freeResults();

$xmlfile->footer("check");

$DB->close();
?>