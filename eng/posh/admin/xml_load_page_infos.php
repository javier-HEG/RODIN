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
# POSH Admin - load page informations 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/xml_load_page_infos.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');



$file=new xmlFile();

$file->header("loadpage");

$pageid=$_GET["pageid"];
$group=$_GET["group"];
$DB->getResults($pages_getPagesList,$DB->escape($pageid));
$row = $DB->fetch(0);
$name=$row["name"];
$desc=$row["description"];
$mode=$row["position"];
$typep=$row["type"];
$param=$row["param"];
$nbcol=$row["nbcol"];
$showtype=$row["showtype"];
$npnb=$row["npnb"];
$style=$row["style"];
$moduleAlign=$row["modulealign"];
$controls=$row["controls"];
$icon=$row["icon"];
$removable=$row["removable"];

echo "<pageid>".$pageid."</pageid>";
echo "<group>".$group."</group>";
echo "<name><![CDATA[".$name."]]></name>";
echo "<description><![CDATA[".$desc."]]></description>";
echo "<position>".$mode."</position>";
echo "<type>".$typep."</type>";
echo "<param><![CDATA[".$param."]]></param>";
echo "<nbcol>".$nbcol."</nbcol>";
echo "<showtype>".$showtype."</showtype>";
echo "<npnb>".$npnb."</npnb>";
echo "<style>".$style."</style>";
echo "<modulealign>".$moduleAlign."</modulealign>";
echo "<controls>".$controls."</controls>";
echo "<icon><![CDATA[".$icon."]]></icon>";
echo "<removable>".$removable."</removable>";
$DB->freeResults();

if ($typep==1)
{
    $DB->getResults($pages_getModules,$DB->escape($pageid));
    while($row = $DB->fetch(0))
    {
        echo "<module>";
            echo "<posx>".$row["posx"]."</posx>";
            echo "<posy>".$row["posy"]."</posy>";
            echo "<posj>".$row["posj"]."</posj>";
            echo "<height>".$row["height"]."</height>";
            echo "<item_id>".$row["item_id"]."</item_id>";
            echo "<website><![CDATA[".$row["website"]."]]></website>";
            echo "<name><![CDATA[".$row["name"]."]]></name>";
            echo "<variables><![CDATA[".$row["variables"]."]]></variables>";
            echo "<minwidth>".$row["minwidth"]."</minwidth>";
            echo "<sizable>".$row["sizable"]."</sizable>";
            echo "<x>".$row["x"]."</x>";
            echo "<y>".$row["y"]."</y>";
            echo "<url><![CDATA[".$row["url"]."]]></url>";
            echo "<uniq>".$row["uniq"]."</uniq>";
            echo "<format>".$row["format"]."</format>";
            echo "<nbvariables>".$row["nbvariables"]."</nbvariables>";
            echo "<blocked>".$row["blocked"]."</blocked>";
            echo "<minimized>".$row["minimized"]."</minimized>";  
            /*
            echo "<usereader>".$row["usereader"]."</usereader>";
    		echo "<autorefresh>".$row["autorefresh"]."</autorefresh>";
    		echo "<icon>".$row["icon"]."</icon>";
            echo "<views>".$row["views"]."</views>";
            echo "<l10n>".$row["l10n"]."</l10n>";  
            */
        echo "</module>";
    }
    $DB->freeResults();
}

$file->footer();
?>