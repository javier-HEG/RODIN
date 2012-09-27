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
# POSH Users management - XML List of the groups
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_group.php";
$list_groupId="";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

$id=$DB->escape(isset($_GET["group"])?$_GET["group"]:0);

$list_groupId="";

//get  all group of the connected admin
$DB->getResults($admin_getUserGroup,$DB->escape($_SESSION['user_id']));
while ($row = $DB->fetch(0))
{
	$list_groupId.="'".$row["group_id"]."',";
}
$list_groupId=substr($list_groupId,0,strlen($list_groupId)-1);
$DB->freeResults();

//get user groups in subgroups
if( !empty($list_groupId) ) {
	$DB->getResults($adm_userIdSubGroup,$list_groupId);
	while ($row = $DB->fetch(0))
	{
        $findme   = $row["id"];
        $pos = strpos($list_groupId, $findme);
        if ($pos === false) {
            $list_groupId.=",'".$row["id"]."'";
        }	
	}
	$DB->freeResults();
}

// selected group information
if($_SESSION['user_id']<=1) {
	$DB->getResults($users_getAllGroupXml,$id);
}
elseif( !empty($list_groupId) ) {
	$DB->getResults($users_getGroupXml,$id,$list_groupId);
}
else { 
    $file->footer(); 
    exit;
}

$row=$DB->fetch(0);
echo "<selgroupname><![CDATA[".$row['name']."]]></selgroupname>";
$DB->freeResults();


//childs groups information
if($_SESSION['user_id']<=1) {
	$DB->getResults($users_getChildrenAllGroupsXml,$id);
}
elseif( !empty($list_groupId) ) {
	$DB->getResults($users_getChildrenGroupsXml,$id,$list_groupId);
}

$number = $DB->nbResults();
echo "<nb>".$number."</nb>";
if ($number>0)
{
	while ($row = $DB->fetch(0))
	{
		echo "<group>";
		echo "<groupid>".$row['id']."</groupid>";
		echo "<groupname><![CDATA[".$row['name']."]]></groupname>";
		echo "</group>";
	}
}
$DB->freeResults();

$DB->getResults($users_getUsersOfGroupXml,$id);
if ($DB->nbResults()>0)
{
	while ($row = $DB->fetch(0))
	{
		echo "<user>";
		echo "<id>".$row["id"]."</id>";
		echo "<username><![CDATA[".$row["username"]."]]></username>";
		echo "<name><![CDATA[".$row["long_name"]."]]></name>";
		echo "<typ><![CDATA[".$row["typ"]."]]></typ>";
		echo "</user>";
	}
}
$DB->freeResults();

$file->footer("channel");
?>