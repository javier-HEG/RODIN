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
# Users tabs
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmltabs.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("tabs");
	
//dummy tab
if (isset($_GET["dumtab"]))
{
	echo "<tab>
	<number>0</number>
	<name><![CDATA[".$_GET["dumtab"]."]]></name>
	<type>1</type>
	<action></action>
	<locked>0</locked>
	<seq>0</seq>
	<style>1</style>
	<edit>0</edit>
	<move>0</move>
	<icon>".(isset($_GET["dumicon"])?$_GET["dumicon"]:"")."</icon>
    <removable>1</removable>
	</tab>";
}

include('selections/tabs_connected.inc.xml');

//get the user profile
$DB->getResults($xmltabs_getTabs,$DB->escape($_SESSION["user_id"]));
while($row = $DB->fetch(0))
{
	echo "<tab>
	<number>".$row["id"]."</number>
	<name><![CDATA[".$row["name"]."]]></name>
	<type>".$row["type"]."</type>
	<action><![CDATA[";
	if ($row["type"]==1)
	{
		echo "$"."p.app.pages.change(".$row["id"].")";
	}
	else if ($row["type"]==2)
	{
		echo "$"."p.app.pages.frame('".$row["param"]."',".$row["id"].")";
	}
    else if ($row["type"]==4)
	{
		echo "$"."p.app.pages.redirect('".$row["param"]."',".$row["id"].")";
	}
	else
	{
		echo $row["param"];
	}
	echo "]]></action>";
	if ($row["md5pass"]=="")
	{
		echo "<locked>0</locked>";
	}
	else
	{
		echo "<locked>1</locked>";
	}
	
	echo "<seq>".$row["seq"]."</seq>
	<edit>1</edit>
	<move>1</move>
	<icon>".$row["icon"]."</icon>
	<style>".$row["style"]."</style>
	<loadstart>".$row["loadonstart"]."</loadstart>
	<status>".$row["status"]."</status>
	<shared>".$row["shared"]."</shared>
    <removable>".$row["removable"]."</removable>
	<param><![CDATA[".$row["param"]."]]></param>
	</tab>";
}
$DB->freeResults();






$file->footer("tabs");

$DB->close();
?>