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
# POSH get themes 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_stats_load.php";
$tabname="statstab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("stats");

if (isset($_GET["month"]))  {
    $ref="month";
    $month=$_GET["month"];
}
else if (isset($_GET["year"]))  {
    $ref="year";
    $year=$_GET["year"];
}
else    {
    $ref="month";
    $month=date("m");
}
   
if ($ref=="month")
{
	$DB->getResults($stats_getDailyVisitors,$DB->quote($month));
	$day=1;
	$currday=($month==date("m"))?date("d"):31;
	$row=$DB->fetch(0);
	while ($day<$currday)
	{
        echo "<stat1>";
        echo "<month>1</month>";
		if ($row["absc"]==$day) {
			echo "<ord>".$row["ord"]."</ord>";
			echo "<absc>".$row["absc"]."</absc>";
            $row=$DB->fetch(0);
		}
		else    {
           echo "<ord>0</ord>";
		   echo "<absc>".$day."</absc>";
		}
        echo "</stat1>";
		$day++;
	}
}
else
{
	$DB->getResults($stats_getMonthlyVisitors,$DB->quote($year));
	$month=1;
	$currmonth=date("m");
	$row=$DB->fetch(0);
	while ($month<=$currmonth)
	{
        echo "<stat1>";
        echo "<month>0</month>";
		if ($row["absc"]==$month)   {
  
            echo "<ord>".$row["ord"]."</ord>";
            echo "<absc>".date("M",mktime(0, 0, 0, $month, 1, 2000))."</absc>";
			$row=$DB->fetch(0);
		}
		else    {
            echo "<ord>0</ord>";
            echo "<absc>".date("M",mktime(0, 0, 0, $month, 1, 2000))."</absc>";
		}
        echo "</stat1>";
		$month++;
	}
}


if ($ref=="month")
{
	$DB->getResults($stats_getDailyNewUsers,$DB->quote($month));
	$day=1;
	$currday=($month==date("m"))?date("d"):31;
	$row=$DB->fetch(0);
	while ($day<$currday)
	{
        echo "<stat2>";
        echo "<month>1</month>";
		if ($row["absc"]==$day)  {
            echo "<ord>".$row["ord"]."</ord>";
            echo "<absc>".$row["absc"]."</absc>";
			$row=$DB->fetch(0);
		}
		else    {
            echo "<ord>0</ord>";
            echo "<absc>".$day."</absc>";
		}
        echo "</stat2>";
		$day++;
	}
}
else
{
	$DB->getResults($stats_getMonthlyNewUsers,$DB->quote($year));
	$month=1;
	$currmonth=date("m");
	$row=$DB->fetch(0);
	while ($month<=$currmonth)
	{
        echo "<stat2>";
        echo "<month>0</month>";
		if ($row["absc"]==$month)   {
            echo "<ord>".$row["ord"]."</ord>";
            echo "<absc>".date("M",mktime(0, 0, 0, $month, 1, 2000))."</absc>";
            $row=$DB->fetch(0);
		}
		else    {
            echo "<ord>0</ord>";
            echo "<absc>".date("M",mktime(0, 0, 0, $month, 1, 2000))."</absc>";
		}
        echo "</stat2>";
		$month++;
	}
}

if ($ref=="month"){
	$DB->getResults($stats_getDailyOpenings,$DB->quote($month));
	$day=1;
	$currday=($month==date("m"))?date("d"):31;
	$row=$DB->fetch(0);
	while ($day<$currday)
	{   
        echo "<stat3>";
        echo "<month>1</month>";
		if ($row["absc"]==$day) {
            echo "<ord>".$row["ord"]."</ord>";
            echo "<absc>".$row["absc"]."</absc>";
			$row=$DB->fetch(0);
		}
		else    {
            echo "<ord>0</ord>";
            echo "<absc>".$day."</absc>";
		}
        echo "</stat3>";
		$day++;
	}
}
else
{
	$DB->getResults($stats_getMonthlyOpenings,$DB->quote($year));
	$month=1;
	$currmonth=date("m");
	$row=$DB->fetch(0);
	while ($month<=$currmonth)
	{
        echo "<stat3>";
        echo "<month>0</month>";
		if ($row["absc"]==$month)   {
            echo "<ord>".$row["ord"]."</ord>";
            echo "<absc>".date("M",mktime(0, 0, 0, $month, 1, 2000))."</absc>";
			$row=$DB->fetch(0);
		}
		else    {
            echo "<ord>0</ord>";
            echo "<absc>".date("M",mktime(0, 0, 0, $month, 1, 2000))."</absc>";
		}
        echo "</stat3>";
		$month++;
	}
}

$file->footer();

?>