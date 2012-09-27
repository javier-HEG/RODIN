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

$folder="";
$not_access=1;
$pagename="admin/wid_calendar.php";
$granted="A";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

$act=$_POST["act"];

if ($act=="sup")
{
	$DB->execute($widcal_remove,$DB->escape($_POST["calid"]));
	launch_hook('calendar_sup',$DB->escape($_POST["calid"]));
}
if ($act=="add")
{
	$id=$DB->escape($_POST["modid"]);
	if ($id==0)
	{
		$DB->execute($widcal_addId);
		$id=$DB->getId();
		$DB->execute($widcal_addEvent,$id,$DB->quote($_POST["t"]),$DB->quote($_POST["c"]),$DB->quote($_POST["d"]),$DB->quote($_POST["h"]),$DB->quote($_POST["end"]));
	}
	else
	{
		$DB->execute($widcal_addEvent,$id,$DB->quote($_POST["t"]),$DB->quote($_POST["c"]),$DB->quote($_POST["d"]),$DB->quote($_POST["h"]),$DB->quote($_POST["end"]));
	}
	launch_hook('calendar_add',$id);
	echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
}
if ($act=="get")
{
	$id=$_POST["modid"];
	$DB->getResults($widcal_getEvent,$id,$DB->quote($_POST["d"]));
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
	$DB->getResults($widcal_getMonthEvents,$id,$DB->quote($_POST["m"]),$DB->quote($_POST["y"]));
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

$file->status("1");

$file->footer("channel");
?>