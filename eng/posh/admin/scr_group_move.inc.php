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
# POSH Users management - Apply group moves
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$parentid=0;
$groupid=0;
if (isset($_POST["parentid"])) { $parentid=$_POST["parentid"]; }
else if (isset($_GET["parentid"])) { $parentid=$_GET["parentid"]; }
if (isset($_POST["groupid"])) { $groupid=$_POST["groupid"]; }
else if (isset($_GET["groupid"])) { $groupid=$_GET["groupid"]; }

$file=new xmlFile();
$file->header("groupmove");

launch_hook('admin_scr_group_move');

$DB->getResults($users_getGroupParent,$DB->escape($groupid));
$row=$DB->fetch(0);
$oldparentid=$row["parent_id"];
$DB->freeResults();

$DB->execute($users_moveGroup,$DB->escape($parentid),$DB->escape($groupid));

$file->status(1);
$file->footer();
?>