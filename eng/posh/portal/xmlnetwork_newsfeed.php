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
# Get news of my profile
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlnetwork_newsfeed.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("updates");

$id=(isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id']);

//get my latest news
$DB->getResults($xmlnetworksummary_getUserUpdates,$DB->escape($id));
while($row=$DB->fetch(0))
{
	echo "<update>";
	echo "<id>".$row["id"]."</id>";
	echo "<pubdate><![CDATA[".$row["pubdate"]."]]></pubdate>";
	echo "<type>".$row["type"]."</type>";
	echo "<title><![CDATA[".$row["title"]."]]></title>";
	echo "<link><![CDATA[".$row["link"]."]]></link>";
	echo "<long_name><![CDATA[".$row["long_name"]."]]></long_name>";
	echo "<picture><![CDATA[".$row["picture"]."]]></picture>";
	echo "</update>";
	
}
$DB->freeResults();

$file->footer("updates");

$DB->close();
?>