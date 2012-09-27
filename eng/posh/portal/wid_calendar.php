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
# Favorites module PHP scripts
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

header("content-type: application/xml"); 
$folder="";
$not_access=1;
$pagename="portal/wid_calendar.php";
$granted="I";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'><channel><status>1</status>';

$act=$_POST["act"];

if ($act=="sup")
{
	$DB->execute($widcal_removeEvent,$DB->escape($_POST["calid"]),$DB->escape($_SESSION['user_id']));
	if ($DB->nbAffected()==0)
	{
		$DB->execute($widcal_createNewId,$DB->escape($_SESSION['user_id']));
		$oldid=$id;
		$id=$DB->getId();
		$DB->execute($widcal_createNewEvent,$id,$DB->escape($oldid),$DB->escape($_POST["calid"]));
		echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
	}
}
if ($act=="add")
{
	$id=$_POST["modid"];
	if ($id==0)
	{
		$DB->execute($widcal_newModuleId,$DB->escape($_SESSION['user_id']));
		$id=$DB->getId();
		$DB->execute($widcal_newModuleEvent,$DB->escape($id),$DB->noHTML($_POST["t"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["d"]),$DB->noHTML($_POST["h"]),$DB->noHTML($_POST["end"]));
	}
	else
	{
		$DB->execute($widcal_addEvent,$DB->escape($id),$DB->noHTML($_POST["t"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["d"]),$DB->noHTML($_POST["h"]),$DB->noHTML($_POST["end"]),$DB->escape($id),$DB->escape($_SESSION['user_id']));
		if ($DB->nbAffected()==0)
		{
			$DB->execute($widcal_addNewModId,$DB->escape($_SESSION['user_id']));
			$oldid=$id;
			$id=$DB->getId();
			$DB->execute($widcal_addNewModEvent,$id,$DB->escape($oldid));
			$DB->execute($widcal_addNewModOldEvents,$DB->escape($id),$DB->noHTML($_POST["t"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["d"]),$DB->noHTML($_POST["h"]),$DB->noHTML($_POST["end"]));
		}
	}
	echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
}
if ($act=="get")
{
	$id=$_POST["modid"];
	$DB->getResults($widcal_getEvents,$DB->escape($id),$DB->noHTML($_POST["d"]));
	while ($row = $DB->fetch(0))
	{
		echo '<event>';
		echo '<id>'.$row["cal_id"].'</id>';
		echo '<title><![CDATA['.$row["title"].']]></title>';
		echo '<comment><![CDATA['.$row["comments"].']]></comment>';
		echo '<time>'.$row["time"].'</time>';
		echo '<endtime>'.$row["endtime"].'</endtime>';
		echo '</event>';
	}
	$DB->freeResults();
}
if ($act=="month")
{
	$id=$_POST["modid"];
	$DB->getResults($widcal_getMonthEvents,$DB->escape($id),$DB->noHTML($_POST["m"]),$DB->noHTML($_POST["y"]));
	while ($row = $DB->fetch(0))
	{
		echo '<event>';
		echo '<id>'.$row["cal_id"].'</id>';
		echo '<title><![CDATA['.$row["title"].']]></title>';
		echo '<day>'.$row["day"].'</day>';
		echo '<time>'.$row["time"].'</time>';
		echo '<endtime>'.$row["endtime"].'</endtime>';
		echo '</event>';
	}
	$DB->freeResults();
}
$DB->close();

echo "</channel>";
?>