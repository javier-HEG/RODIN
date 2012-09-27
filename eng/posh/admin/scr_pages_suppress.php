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
# POSH Pages management - Suppress a tab
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_pages_suppress.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();
$file->header("pagesupp");

launch_hook('admin_scr_pages_suppress');

$pageid=$_POST["id"];
$group=$_POST["group"];

// change pages sequence
$DB->getResults($pages_getPageSequence,$DB->escape($pageid));
$row = $DB->fetch(0);
$seq=$row["seq"];
$DB->freeResults();

$DB->execute($pages_updatePageSeqForGroup,$DB->escape($seq),$DB->escape($group));

// suppress the page and modules from 'pages' and 'profile' 
$DB->execute($pages_removePage,$DB->escape($pageid));
$DB->execute($pages_removeModules,$DB->escape($pageid));

$DB->getResults($users_getProfileId,$DB->escape($pageid));
while ($row=$DB->fetch(0))
{
  $profileid = $row['id'];
  $DB->execute($users_deleteProfileModules,$DB->escape($profileid));
}
$DB->execute($users_deleteToUpdateTabsByPageId,$DB->escape($pageid));

$file->status(1);
$file->footer();
//header("location:pages_tabs.php?group=".$group);
?>