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
# Favorites module PHP scripts
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

header("content-type: application/xml"); 
$folder="";
$not_access=1;
$pagename="portal/wid_calendar.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'><channel><status>1</status>';

$act = isset($_POST["act"])?$_POST["act"]:"";
$calid = isset($_POST["calid"])?$_POST["calid"]:-1;
$t = isset($_POST["t"])?$_POST["t"]:"";
$c = isset($_POST["c"])?$_POST["c"]:"";
$d = isset($_POST["d"])?$_POST["d"]:"";
$h = isset($_POST["h"])?$_POST["h"]:"";
$end = isset($_POST["end"])?$_POST["end"]:"";
$m = isset($_POST["m"])?$_POST["m"]:"";
$y = isset($_POST["y"])?$_POST["y"]:"";

if ($act=="sup")
{
	$DB->execute($widcalshared_removeEvent,$DB->escape($calid));
}
if ($act=="add")
{
	$id=$_POST["modid"];
	if ($id==0)
	{
		$DB->execute($widcalshared_addModId);
		$id=$DB->getId();
		$DB->execute($widcalshared_firstEvent,$DB->escape($id),$DB->noHTML($t),$DB->noHTML($c),$DB->quote($d),$DB->quote($h),$DB->quote($end));
	}
	else
	{
		$DB->execute($widcalshared_addEvent,$id,$DB->noHTML($t),$DB->noHTML($c),$DB->quote($d),$DB->quote($h),$DB->quote($end));
	}
	echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
}
$id = isset($_POST["modid"])?$_POST["modid"]:-1;
if ($act=="get")
{
	$DB->getResults($widcalshared_getEvents,$DB->escape($id),$DB->quote($d));
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
	$DB->getResults($widcalshared_getMonthEvents,$DB->escape($id),$DB->quote($m),$DB->quote($y));
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