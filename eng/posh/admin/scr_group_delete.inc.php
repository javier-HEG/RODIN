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
# POSH Users management - Delete a user group
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

require_once('../includes/xml.inc.php');

$group=0;
if (isset($_POST["groupid"])) { $group=$_POST["groupid"]; }
else if (isset($_GET["groupid"])) { $group=$_GET["groupid"]; }

$file=new xmlFile();
$file->header("groupsupp");

//delete in user_group
$DB->execute($users_deleteGroup,$DB->escape($group));
//delete admin group mapping
$DB->execute($admin_removeFromGroupByGroup,$DB->escape($group));

//delete pages of that group / pages_module
$tabId = array ();
$DB->getResults($pages_getGroupPages,$DB->escape($group));
while ($row=$DB->fetch(0))
{
    $pageid=$row['id'];
    array_push ($tabId, $pageid);
}

for ($i=0;$i<count($tabId);$i++)
{
    //suppress the page and modules from 'pages' and 'profile' 
    $DB->execute($pages_removePage,$DB->escape($tabId[$i]));
    $DB->execute($pages_removeModules,$DB->escape($tabId[$i]));
    $DB->getResults($users_getProfileId,$DB->escape($tabId[$i]));
    while ($row=$DB->fetch(0))
    {
      $profileid = $row['id'];
      $DB->execute($users_deleteProfileModules,$DB->escape($profileid));
    }
    $DB->execute($users_deleteToUpdateTabsByPageId,$DB->escape($tabId[$i]));
}

$file->status(1);
$file->footer();
?>