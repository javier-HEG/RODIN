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
# POSH Modules management - XML List of the directories
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_directory.php";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');
$list_groupId="";
$list_categoryGroupId="";

$file=new xmlFile();

$file->header("channel");

$id=$DB->escape(isset($_GET["catid"])?$_GET["catid"]:0);
$access=0;

//get user groups
if($_SESSION['user_id']>1)
{
	$DB->getResults($adm_getGroupId,$DB->escape($_SESSION['user_id']));
	while ($row = $DB->fetch(0))
	{
		$list_groupId.="'".$row["group_id"]."',";
	}
	$list_groupId=substr($list_groupId,0,strlen($list_groupId)-1);
	$DB->freeResults();
	
    if ($list_groupId=="") {
        $file->footer(); 
        exit;
    }
    
	$DB->getResults($users_getGroupCategoryMapXml,$list_groupId);
	while ($row = $DB->fetch(0))
	{
		$list_categoryGroupId.="'".$row["category_id"]."',";
	}
	$list_categoryGroupId=substr($list_categoryGroupId,0,strlen($list_categoryGroupId)-1);
	$DB->freeResults();
	
    if ($list_categoryGroupId!="") {
        $DB->getResults($module_getAdminAllowedGroupsId,$id,$list_categoryGroupId);
        $access=$DB->nbResults();
        $DB->freeResults();

        $DB->getResults($module_getChildrenAdminDirectoryXml,$list_categoryGroupId,$id);	
    }
    else {
        $file->footer(); 
        exit;
    }
} else {
	$DB->getResults($module_getChildrenDirectoryXml,$id);	
}

$number = $DB->nbResults();
if ($number>0)
{
	while ($row = $DB->fetch(0))
	{
		echo "<dir>";
		echo "<dirid>".$row['id']."</dirid>";
		echo "<dirname><![CDATA[".$row['name']."]]></dirname>";
		echo "<quantity>".$row['quantity']."</quantity>";
		echo "<secured>".$row["secured"]."</secured>";
		echo "</dir>";
	}
}
$DB->freeResults();
    
if($_SESSION['user_id']<=1 || $access!=0) {
    $DB->getResults($module_getModuleInfoOfDirectoryXml,$id );
    if ($DB->nbResults()>0)
    {
    	while ($row = $DB->fetch(0))
    	{
    		echo "<item>";
    		echo "<id>".$row['id']."</id>";
    		echo "<name><![CDATA[".$row['name']."]]></name>";
    		echo "<icon><![CDATA[".$row['icon']."]]></icon>";
    		echo "</item>";
    	}
        $DB->freeResults();
    }
}

$file->footer("channel");
?>