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
$pagename="admin/xml_config_notifications_titles.php";
$errLog="";
$tabname="configstab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("notification");

$DB->getResults($config_getNotificationTitles);
$i=0;
while ($row = $DB->fetch(0))
{
    $notif_libelle=stripslashes($row["libelle"]);
    echo "<naming>";
    echo "<label><![CDATA[".$notif_libelle."]]></label>";
    echo "<indice>".$i."</indice>";
    echo "</naming>";    
    $i++;
}
$DB->freeResults();

$file->footer();
?>