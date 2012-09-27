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
# POSH Module management - Suppress a root directory
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_rootdirectory_suppress.php";
//includes
require_once('includes.php');;
require_once('../includes/refreshcache.inc.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("dirSuppress");

launch_hook('admin_scr_rootdirectory_suppress');

$DB=new connection(__SERVER,__LOGIN,__PASS,__DB);

$DB->execute($rootdirectory_removeProperties,$DB->escape($_POST["id"]),$DB->escape($_POST["seq"]));
$DB->execute($rootdirectory_updateProperties,$DB->escape($_POST["seq"]));

//refresh cache
refresh_directory(0,$template_folder,"");

//header("location:scr_rootdirectory_setconfig.php");

$file->status(1);
$file->footer();
?>