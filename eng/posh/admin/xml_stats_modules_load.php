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
# POSH get the modules stats 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_stats_modules_load.php";
$tabname="statstab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("stats");

//get time range for statistics
if (isset($_GET["month"])){
    $ref="month";
    $month=$_GET["month"];
    $year=date("Y");
}
else if (isset($_GET["year"])){
    $ref="year";
    $year=$_GET["year"];
}
else{
    $ref="month";
    $month=date("m");
    $year=date("Y");
}

if ($ref=="month")  {
    echo "<ref>month</ref>";
    echo "<refval>".$month."</refval>";
}
else    {
    echo "<ref>year</ref>";
    echo "<refval>".$year."</refval>";
}   
    
// generate best modules ranking
if ($ref=="month") {
	$DB->getResults($statsmodules_getDailyTopModules,$DB->quote('%'.$year.'-'.$month.'%'));
}
else {
	$DB->getResults($statsmodules_getMonthlyTopModules,$DB->quote('%'.$year.'%'));
}
$inc=1;
while ($row=$DB->fetch(0))
{
    $name=addslashes($row["name"]);
    $tot=addslashes($row["tot"]);
    echo "<topmodules>";
    echo "<inc>".$inc."</inc>";
    echo "<name><![CDATA[".$name."]]></name>";
    echo "<tot>".$tot."</tot>";
    echo "</topmodules>";
	$inc++;
}

//generate best rss widget (of library) ranking
if ($ref=="month") {
	$DB->getResults($statsmodules_getDailyTopDirRss,$DB->quote('%'.$year.'-'.$month.'%'));
}
else {
	$DB->getResults($statsmodules_getMonthlyTopDirRss,$DB->quote('%'.$year.'%'));
}
$inc=1;
while ($row=$DB->fetch(0))
{
    $name=addslashes($row["name"]);
    $tot=addslashes($row["tot"]);
    echo "<toprss>";
    echo "<inc>".$inc."</inc>";
    echo "<name><![CDATA[".$name."]]></name>";
    echo "<tot>".$tot."</tot>";
    echo "</toprss>";
	$inc++;
}

//generate best rss widgets ranking
if ($ref=="month") {
	$DB->getResults($statsmodules_getDailyTopRss,$DB->quote('%'.$year.'-'.$month.'%'));
}
else {
	$DB->getResults($statsmodules_getMonthlyTopRss,$DB->quote('%'.$year.'%'));
}
$inc=1;
while ($row=$DB->fetch(0))
{
    $result1=addslashes($row["result1"]);
    $tot=addslashes($row["tot"]);
    echo "<topusersrss>";
    echo "<inc>".$inc."</inc>";
    echo "<result1><![CDATA[".$result1."]]></result1>";
    echo "<tot>".$tot."</tot>";
    echo "</topusersrss>";
	$inc++;
}

$file->footer();
?>