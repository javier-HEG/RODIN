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
# POSH applications management - modify an application
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_application_modify.php";
//includes
require_once("includes.php");


launch_hook('admin_scr_application_modify');

$appId=$_POST["appid"];

$DB->execute($application_updateInfo,$DB->noHTML($_POST["appname"]),$DB->noHTML($_POST["appdesc"]),$DB->escape($appId));

//group management
$DB->execute($application_removeGroups,$DB->escape($appId));
$i=0;
while (isset($_POST["group".$i]))
{
	$DB->execute($application_addGroup,$DB->escape($appId),$DB->escape($_POST["group".$i]));
	$i++;
}


header("location:frm_application_modify.php?treated=1&appid=".$appId);
?>