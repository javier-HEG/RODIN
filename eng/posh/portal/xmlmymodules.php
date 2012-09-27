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
# generate the XML users modules
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlmymodules.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("modules");

//get the validated user modules
$DB->getResults($xmlmymodules_getModules,$DB->escape($_SESSION['user_id']));
while($row = $DB->fetch(0))
{
	echo "<module>";
	echo "<id>".$row["id"]."</id>";
	echo "<name><![CDATA[".$row["name"]."]]></name>";
	echo "<typ><![CDATA[".$row["typ"]."]]></typ>";
	echo "<secured>".$row["secured"]."</secured>";
	echo "<status>O</status>";
	echo "</module>";
}
$DB->freeResults();

//get the waiting user modules
$DB->getResults($xmlmymodules_getTempModules,$DB->escape($_SESSION['user_id']));
while($row = $DB->fetch(0))
{
	echo "<module>";
	echo "<id>".$row["id"]."</id>";
	echo "<name><![CDATA[".$row["name"]."]]></name>";
	echo "<typ>M</typ>";
	echo "<status>W</status>";
	echo "</module>";
}
$DB->freeResults();

$file->footer("modules");

$DB->close();
?>