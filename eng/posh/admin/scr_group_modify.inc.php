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
# POSH Users management - Apply group modifications
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

require_once('../includes/xml.inc.php');

$groupname="";
$group=0;
if (isset($_POST["groupname"])) { $groupname=$_POST["groupname"]; }
else if (isset($_GET["groupname"])) { $groupname=$_GET["groupname"]; }
if (isset($_POST["groupid"])) { $group=$_POST["groupid"]; }
else if (isset($_GET["groupid"])) { $group=$_GET["groupid"]; }

$file=new xmlFile();
$file->header("groupmodify");

launch_hook('admin_scr_group_modify');

$chk=$DB->execute($module_updateGroupName,$DB->quote($groupname),$DB->escape($group));

$file->status($chk);
$file->footer();
?>
