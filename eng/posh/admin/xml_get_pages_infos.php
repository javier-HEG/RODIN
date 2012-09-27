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
# POSH Admin - load pages informations 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/xml_get_pages_infos.php.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("pages");

//default portal pages
if($_SESSION['user_id']>1) {
    $DB->getResults($index_getPagesNameAllowed,$DB->escape($_SESSION['user_id']));
}
else {
    $DB->getResults($index_getPagesName);
}

while ($row = $DB->fetch(0))
{
    $name=$row['name'];
    echo "<page>";
    echo "<name>".$name."</name>";
    echo "</page>";
}
$DB->freeResults();


//available portals
$DB->getResults($index_getNbOfPortals);
$row = $DB->fetch(0);
$nb_portals = $row["nb"];
echo "<availablePortals>".$nb_portals."</availablePortals> ";
$DB->freeResults();
  
//awaiting portals  
$DB->getResults($index_getNbOfPortalsToValidate);
$row = $DB->fetch(0);
$nb_portals_n= $row["nb"];
echo "<awaitingPortals>".$nb_portals_n."</awaitingPortals> ";
$DB->freeResults();

$file->footer();
?>