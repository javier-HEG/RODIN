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
# OPML export
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlopmlexport.php";
//includes
require_once('includes.php');

header("Content-disposition: attachment; filename=opmlexport.opml");
header("Content-Type: application/force-download; application/xml");
header("Content-Transfer-Encoding: application/xml\n"); // Surtout ne pas enlever le \n
//header("Content-Length: ".filesize($chemin . $Fichier_a_telecharger)); 
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
header("Expires: 0");
readfile("opmlexport.opml");


echo '<opml version="1.0">';
echo '<head>';
echo '<title>'.__APPNAME.' - '.$_SESSION['username'].' Subscriptions</title>';
echo '<dateCreated>'.date("d/m/Y").'</dateCreated>';
echo '</head>';
echo '<body>';

$rows = $DB->select(FETCH_ARRAY,$opmlexport_getPortal,$DB->escape($_SESSION['user_id']));
foreach($rows as $row)
{
	echo '<outline title="'.$row["name"].'" text="'.$row["name"].'">'.chr(13).chr(10);
	
	$DB->getResults($opmlexport_getModules,$DB->escape($row["id"]),$DB->escape($_SESSION['user_id']));
	while ($row2=$DB->fetch(0))
	{
		$title="";
		$url="";
		$vars=explode("&",$row2["variables"]);
		for ($i=0;$i<count($vars);$i++)
		{
			$par=explode("=",$vars[$i]);
			if ($par[0]=="title") $title=$par[1];
			if ($par[0]=="rssurl") $url=$par[1];
		}
		$url=urldecode($url);
		$name=$title==""?$row2["name"]:$title;
		echo '<outline text="'.$name.'" htmlUrl="'.urlencode($row2["website"]).'" language="'.__LANG.'" title="'.$name.'" type="rss" version="RSS" xmlUrl="'.urlencode($url).'" />'.chr(13).chr(10);
	}
	$DB->freeResults();
	echo '</outline>'.chr(13).chr(10);
}
echo '</body>';
echo '</opml>';

$DB->close();
?>