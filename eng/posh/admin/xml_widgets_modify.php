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
# POSH Admin - get widgets informations (for modification)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/xml_widgets_modify.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("widmodify");

$itemid=$_GET["itemid"];
if (!is_array($_GET) || ! array_key_exists('itemid',$_GET)) exit();

//get directory information for this module
$DB->getResults($module_getCategory,$DB->escape($itemid));
if ($DB->nbResults()!=0)
{
	$row=$DB->fetch(0);
    $dirid=$row["id"];
    $dirname=$row["name"];
	echo "<dirid>".$dirid."</dirid>";
	echo "<dirname>".$dirname."</dirname>";
}
$DB->freeResults();


//get module information
$DB->getResults($module_getModuleAndView,$DB->escape($itemid));

$row = $DB->fetch(0);
$minwidth=$row["minwidth"];
$typ=$row["typ"];
$status=$row["status"];
$defvar=$row["defvar"];
$url=$row["url"];
$id=$row["id"];
$name=$row['name'];
$description=$row['description'];
$website=$row['website'];
$format=$row['format'];
$height=$row['height'];
$defvar=$row['defvar'];
$nbvariables=$row['nbvariables'];
$icon=$row['icon'];
$views=$row['views'];
$DB->freeResults();
$DB->getResults($module_getItemId,$DB->escape($itemid));
$editable = 0;
if( $DB->nbResults() > 0 )
	$editable = 1;
$DB->freeResults();
//$urlLastCar=substr($url,-1);
//if ($urlLastCar=="?" OR $urlLastCar=="&") $url=substr($url,0,-1);

echo "<minwidth>".$minwidth."</minwidth>";
echo "<typ>".$typ."</typ>";
echo "<status>".$status."</status>";
echo "<url><![CDATA[".$url."]]></url>";
echo "<id>".$id."</id>";
echo '<name><![CDATA['.$name.']]></name>';
echo '<description><![CDATA['.$description.']]></description>';
echo "<website>".$website."</website>";
echo "<format>".$format."</format>";
echo "<height>".$height."</height>";
echo "<icon><![CDATA[".$icon."]]></icon>";
echo "<views><![CDATA[".$views."]]></views>";
echo "<defvar><![CDATA[".$defvar."]]></defvar>";
echo "<editable>".$editable."</editable>";
$DB->freeResults();

//get module keywords
$DB->getResults($module_getKeywords,$DB->escape($itemid));
$i=0;
while ($row=$DB->fetch(0))
{    
    $kid=$row["id"];
    $kweight=$row["weight"];
    $klabel=$row["label"];
    echo "<keyword>";
        echo "<kid>".$kid."</kid>";
        echo "<kweight>".$kweight."</kweight>";
        echo "<klabel>".$klabel."</klabel>";
    echo "</keyword>";
    
    $i++;
}
$DB->freeResults();

$file->footer();
?>