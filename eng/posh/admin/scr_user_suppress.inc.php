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
# POSH Users management - Suppress a user
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("userSupp");

if (!isset($userid)) {
    $userid=isset($_POST["id"]) ? $_POST["id"] : 0 ;
}

launch_hook('admin_scr_user_suppress',$userid);
$chk1 = 0; $chk2 = 0; $chk3 = 0; $chk4 = 0; $chk5 = 0; $chk6 = 0;

switch($_POST["action"])
{
	case "delete":
		//Suppress general user information
		$chk1 = $DB->execute($users_removeUser,$DB->escape($userid));
		$chk2 = $DB->execute($users_removeUserInfos,$DB->escape($userid));
		// Suppress user from groups
		$chk3 = $DB->execute($users_removeFromGroups,$DB->escape($userid));
		// Suppress useless objects
		$chk4 = $DB->execute($users_deleteTabs,$DB->escape($userid));
		$chk5 = $DB->execute($users_deleteModules,$DB->escape($userid));
		// Suppress from admin tabs map
		$chk6 = $DB->execute($users_removeFromTabs,$DB->escape($userid));

		break;
	case "activate":
		$chk1 = $DB->execute($users_activateAdmin,$DB->escape($userid));
		$chk2 = $DB->execute($users_activateUser,$DB->escape($userid));
		$chk3 = 1; $chk4 = 1; $chk5 = 1; $chk6 = 1;

		//echo "<i>".lg("userActivated")."</i>";
		break;
	case "inactivate":
		$chk1 = $DB->execute($users_inactivateAdmin,$DB->escape($userid));
		$chk2 = $DB->execute($users_inactivateUser,$DB->escape($userid));
		$chk3 = 1; $chk4 = 1; $chk5 = 1; $chk6 = 1;
		//echo "<i>".lg("userInactivated")."</i>";
		break;
}

$file->status($chk1 && $chk2 && $chk3 && $chk4 && $chk5 && $chk6);

$file->footer();        
?>
