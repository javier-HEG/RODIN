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
# POSH config features - 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_config_features.php";
$errLog="";
$tabname="configstab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("features");

//initialize edit & add menus
$usereader="true";
$showtabicon="true";
$columnchange="true";
$ctrlhiding="true";
$doubleprotection="true";
$showrsscell="true";
$showModuleSearch="true";
$showModuleExpl="true";
$moduleAlign="true";

$DB->getResults($configfeatures_getParameters);
while ($row=$DB->fetch(0))
{
    if ($row["parameter"]=="usereader") $usereader=$row["value"];
    if ($row["parameter"]=="showtabicon") $showtabicon=$row["value"];
    if ($row["parameter"]=="columnchange") $columnchange=$row["value"];
    if ($row["parameter"]=="ctrlhiding") $ctrlhiding=$row["value"];
    if ($row["parameter"]=="doubleprotection") $doubleprotection=$row["value"];
    if ($row["parameter"]=="showrsscell") $showrsscell=$row["value"];
    if ($row["parameter"]=="showModuleSearch") $showModuleSearch=$row["value"];
    if ($row["parameter"]=="showModuleExpl") $showModuleExpl=$row["value"];
    if ($row["parameter"]=="moduleAlign") $moduleAlign=$row["value"];
}
$DB->freeResults();

echo "<usereader>".$usereader."</usereader>";
echo "<showtabicon>".$showtabicon."</showtabicon>";
echo "<columnchange>".$columnchange."</columnchange>";
echo "<ctrlhiding>".$ctrlhiding."</ctrlhiding>";
echo "<doubleprotection>".$doubleprotection."</doubleprotection>";
echo "<showrsscell>".$showrsscell."</showrsscell>";
echo "<showModuleSearch>".$showModuleSearch."</showModuleSearch>";
echo "<showModuleExpl>".$showModuleExpl."</showModuleExpl>";
echo "<moduleAlign>".$moduleAlign."</moduleAlign>";

$file->footer();
?>