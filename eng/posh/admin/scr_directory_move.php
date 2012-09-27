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
# POSH Module management - Apply directory moves
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_directory_move.php";
//includes
require_once("includes.php");
require_once('../includes/refreshcache.inc.php');
require_once('../includes/xml.inc.php');

launch_hook("admin_scr_directory_move");

$catid=$_POST["catid"];
$parentid=$_POST["parentid"];

//find the old location of the directory
$DB->getResults($module_getDirectoryParent,$DB->escape($catid));
$row=$DB->fetch(0);
$oldparentid=$row["parent_id"];
$lg=$row["lang"];
$DB->freeResults();

//move directory to new location
$DB->execute($module_moveDirectory,$DB->escape($parentid),$DB->escape($catid));

//refresh old parent directory
refresh_directory($oldparentid,$template_folder,$lg);
//refresh new parent directory and ancestors
refresh_directory($catid,$template_folder,$lg);

$file=new xmlFile();
$file->header("groupmodify");
$file->status(1);
$file->footer();
?>