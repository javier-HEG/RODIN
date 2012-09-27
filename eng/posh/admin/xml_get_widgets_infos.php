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
# POSH Admin - get widgets informations (stats)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/xml_get_widgets_infos.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("widgets");

if ($_SESSION['user_id']>1) {
    //Available widgets
    $DB->getResults($module_getAdminAllowedWidgets,$DB->escape($_SESSION['user_id']),$DB->escape((1-1)*21));
    echo "<availableWidgets>".$DB->nbResults()."</availableWidgets>";
    $DB->freeResults();    
    
    //Waiting to be validated widgets
    $DB->getResults($module_getAdminAllowedWidgetsToValidate,$DB->escape($_SESSION['user_id']),$DB->escape(((1-1)*21)));  
    echo "<awaitingWidgets>".$DB->nbResults()."</awaitingWidgets>";
    $DB->freeResults();    
}
else {
    //Available widgets
    $DB->getResults($index_getNbOfModules);
    $row = $DB->fetch(0);
    echo "<availableWidgets>".$row['nb']."</availableWidgets>";
    $DB->freeResults();

    //Waiting to be validated widgets
    $DB->getResults($index_getNbOfModulesToValidate);
    $row = $DB->fetch(0);
    echo "<awaitingWidgets>".$row['nb']."</awaitingWidgets>";
    $DB->freeResults();
}




$mdkey=md5(__KEY);
echo "<key>".__KEY."</key>";
echo "<md5key>".$mdkey."</md5key>";

$filename = '../cache/rssadmin'.$mdkey.'.xml';
if (file_exists($filename)) {
    echo "<activity>1</activity>";
}

$file->footer();
?>