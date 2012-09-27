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
# POSH Modules management - XML List of modules waiting for validation
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_tovalidate.php";
$list_groupId="";
//includes
require('includes.php');
global $DB;

require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

//get user groups
$DB->getResults($adm_getGroupId,$DB->escape($_SESSION['user_id']));
while ($row = $DB->fetch(0))
{
	$list_groupId.="'".$row["group_id"]."',";
}
$list_groupId=substr($list_groupId,0,strlen($list_groupId)-1);
$DB->freeResults();

if($_SESSION['user_id']>1 && empty($list_groupId)) {
    echo "<return>0</return>";
}

//get user groups in subgroups
if( !empty($list_groupId) ) {
	$DB->getResults($adm_userIdSubGroup,$list_groupId);
	while ($row = $DB->fetch(0))
	{
		$list_groupId.=",'".$row["id"]."'";
	}
	$DB->freeResults();
}


if($_SESSION['user_id']>1 && !empty($list_groupId)) {
	$DB->getResults($users_getNbXml,$list_groupId);
    $row = $DB->fetch(0);
    echo "<return>".$row["nb"]."</return>";
    $DB->freeResults();
}
else {
	$DB->getResults($users_getAllNbXml);
    $row = $DB->fetch(0);
    echo "<return>".$row["nb"]."</return>";
    $DB->freeResults();
}

$file->footer("channel");
?>