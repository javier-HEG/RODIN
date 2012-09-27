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
# POSH Module management - Directory suppression form
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_directory_suppress.php";

require_once("includes.php");
require_once('../includes/refreshcache.inc.php');
require_once('../includes/xml.inc.php');

$catid=$_GET["catid"];

$DB->getResults($module_getSubCategoryNumber,$DB->escape($catid));
$row = $DB->fetch(1);
$nbcat=$row[0];
$DB->freeResults();

$DB->getResults($module_getTempSubCatNumber,$DB->escape($catid));
$row = $DB->fetch(1);
$nbitem=$row[0];
$DB->freeResults();

if (($nbitem!=0 || $nbcat!=0))  {  
    $error=1;
}
else {
    $error=0;
	$DB->getResults($module_getParentDirectory,$DB->escape($catid));
	$row=$DB->fetch(0);
	$parentid=$row["parent_id"];
	$catlang=$row["lang"];
	$DB->freeResults();
	$DB->execute($module_removeDirectory,$DB->escape($catid));
	refresh_directory($parentid,$template_folder,$catlang);
}

$file=new xmlFile();
$file->header("dirdelete");
echo "<nbcat>".$nbcat."</nbcat>";
echo "<nbitem>".$nbitem."</nbitem>";
echo "<error>".$error."</error>";
$file->footer();
?>