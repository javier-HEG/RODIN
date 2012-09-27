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
# POSH Admin - get directory informations
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/xml_directory_modify.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("dirmodify");

$catid=$_GET["catid"];
$DB->getResults($module_getCategoryName,$DB->escape($catid));
$row = $DB->fetch(0);
echo '<name><![CDATA['.$row["name"].']]></name>';
echo '<lang>'.$row["lang"].'</lang>';
$DB->freeResults();

$DB->getResults($directory_getDirGroups,$DB->escape($catid));
while ($row = $DB->fetch(0))
{
    echo "<directory>";
    echo "<did>".$row["id"]."</did>";
    echo "<dname><![CDATA[".$row["name"]."]]></dname>";
    echo "</directory>"; 
}
$DB->freeResults();

$file->footer();
?>