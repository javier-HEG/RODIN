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
# POSH Admin - load the number of users informations 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/xml_get_users_infos.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("users");
$list_groupId="";
$list_userId="";

echo "<id>".$_SESSION['user_id']."</id>";
if($_SESSION['user_id']>1) {
	//get  all group of the connected admin
	$DB->getResults($admin_getUserGroup,$DB->escape($_SESSION['user_id']));
	while ($row = $DB->fetch(0))
	{
		$list_groupId.="'".$row["group_id"]."',";
        echo "<groups>";
        echo "<group>".$row["group_id"]."</group> ";
        echo "</groups>";
	}
	$list_groupId=substr($list_groupId,0,strlen($list_groupId)-1);
	$DB->freeResults();
    
    if ($list_groupId=="") {
        $file->footer(); 
        exit;
    }
    
	//get user groups in subgroups
	$DB->getResults($adm_userIdSubGroup,$list_groupId);
	while ($row = $DB->fetch(0))
	{
		$list_groupId.=",'".$row["id"]."'";
        echo "<groups>";
        echo "<group>".$row["id"]."</group> ";
        echo "</groups>";
	}
	$DB->freeResults();

	//get user ids by group
	$DB->getResults($communication_getUsersIdByGroup, $list_groupId);
	while ($row = $DB->fetch(0))
	{
		$list_userId.="'".$row["user_id"]."',";
	}
	$DB->freeResults();
	$list_userId=substr($list_userId,0,strlen($list_userId)-1);
	
    if ($list_userId!="") {
        $DB->getResults($index_getNbOfUsersByGroup, $list_userId);
        $row = $DB->fetch(0);
        echo "<nbUsers>".$row["nb"]."</nbUsers> ";
        $DB->freeResults();
    } 
}
else {
	$DB->getResults($index_getNbOfUsers);
    $row = $DB->fetch(0);
    echo "<nbUsers>".$row["nb"]."</nbUsers> ";
    $DB->freeResults();
}


$file->footer();
?>