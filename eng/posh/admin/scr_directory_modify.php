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
# POSH Module management - Apply directory modifications
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_directory_modify.php";
//includes
require_once("includes.php");
require_once('../includes/refreshcache.inc.php');
require_once('../includes/xml.inc.php');

launch_hook('admin_scr_directory_modify');

$catid=$_POST["dirid"];

//update directory
$secured=(isset($_POST["group0"]))?1:0;
$DB->execute($module_updateDirectory,$DB->quote($_POST["dirname"]),$secured,$DB->escape($catid));

//group management
//Suppress the old group mapping
$DB->execute($directory_removeGroups,$DB->escape($catid));
$i=0;
while (isset($_POST["group".$i]))
{
	$DB->execute($directory_addGroup,$DB->escape($_POST["group".$i]),$DB->escape($catid));
	$i++;
}

refresh_directory($catid,$template_folder,$_POST["dirlang"]);

$file=new xmlFile();
$file->header("directory");
$file->status(1);
$file->footer();
?>