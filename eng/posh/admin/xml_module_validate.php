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
# POSH Module management - User Module validation form
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_module_validate.php";
$tabname="modulestab";

$itemid=$_GET["itemid"];

require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("modValidate");

//get directory information for this module
$DB->getResults($module_getTempCategory,$DB->escape($itemid));
if ($DB->nbResults()!=0)
{
	$row=$DB->fetch(0);
	$dirid=$row["id"];
	$dirname=$row["name"];
}
$DB->freeResults();

echo "<dirid>".$dirid."</dirid>";
echo "<dirname>".$dirname."</dirname>";

//get module information
$DB->getResults($module_getModulesToValidate,$DB->escape($itemid));
$row = $DB->fetch(0);
$minwidth=$row["minwidth"];
$url=$row["url"];
//$urlLastCar=substr($url,-1);
$keywords=$row["keywords"];
$username=$row["username"];
$long_name=$row["long_name"];
$id=$row["id"];
$description=$row["description"];
$name=$row["name"];
$website=$row["website"];
$height=$row["height"];
$lang=$row["lang"];
$format=$row["format"];
$defvar=$row["defvar"];
$nbvariables=$row["nbvariables"];
$views=$row["views"];
$l10n=$row["l10n"];
$icon=$row["logo"];


echo "<minwidth>".$minwidth."</minwidth>";
echo "<url><![CDATA[".$url."]]></url>";
echo "<keywords><![CDATA[".$keywords."]]></keywords>";
echo "<username><![CDATA[".$username."]]></username>";
echo "<long_name><![CDATA[".$long_name."]]></long_name>";
echo "<id>".$id."</id>";
echo "<description><![CDATA[".$description."]]></description>";
echo "<name><![CDATA[".$name."]]></name>";
echo "<website>".$website."</website>";
echo "<height>".$height."</height>";
echo "<lang>".$lang."</lang>";
echo "<format>".$format."</format>";
echo "<defvar><![CDATA[".$defvar."]]></defvar>";
echo "<nbvariables>".$nbvariables."</nbvariables>";
echo "<icon>".$icon."</icon>";
echo "<views><![CDATA[".$views."]]></views>";
echo "<l10n>".$l10n."</l10n>";  
//if ($urlLastCar=="?" OR $urlLastCar=="&") $url=substr($url,0,-1);

$file->footer();
?>
