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
$pagename="admin/xml_get_user_infos.php";
$list_groupId="";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("user");

$id_admin = false;
$user=(isset($_GET["id"])?$_GET["id"]:0);
//$user_type = $_GET["type"];
if($user<0)
{
	$user=$_SESSION['user_id'];
	$id_admin = true;
}

//get user personal informations
$DB->getResults($users_getUserInformation,$DB->escape($user));
$row = $DB->fetch(0);
$username=$row["username"];
$long_name=$row["long_name"];
$user_type=$row["typ"];
$sellang=$row["lang"];
echo "<username>".$username."</username>";
echo "<long_name>".$long_name."</long_name>";
echo "<user_type>".$user_type."</user_type>";
echo "<sellang>".$sellang."</sellang>";
$DB->freeResults();

//get user criterias
$DB->getResults($users_getCriteriasParameters,$DB->escape($user));
$total=$DB->nbResults();
echo "<totalCriterias>".$total."</totalCriterias>";
if ($DB->nbResults()!=0)  
{ 
    while ($row=$DB->fetch(0))
    {
        $infoID=$row["info_id"];
        $parameters=$row["parameters"];
        echo "<criteria>";
        echo "<id>".$infoID."</id>";
        echo "<parameters>".$parameters."</parameters>";
        echo "</criteria>";
    }
}
$DB->freeResults();

if( $id_admin || $user_type=="A") {
	//get user groups
	$DB->getResults($admin_getUserGroup,$DB->escape($user));
	while ($row = $DB->fetch(0))
	{
		$list_groupId.="'".$row["group_id"]."',";
	}
	$list_groupId=substr($list_groupId,0,strlen($list_groupId)-1);
	$DB->freeResults();
    
    if ($list_groupId=="") {
        //get admin tabs
        $DB->getResults($tabs_getUpdateAdminTabs,$DB->escape($user));
        if ($DB->nbResults()!=0) 
        {
                while ($row = $DB->fetch(0))
                {
                    $tab_id=$row["tab_id"];
                    echo "<tab>";
                    echo "<tab_id>".$tab_id."</tab_id>";
                    echo "</tab>";
                }  
        }
        $DB->freeResults();
        $file->footer(); 
        exit;
    }

	$DB->getResults($adm_getGroupNameByIdGroup,$list_groupId);
	if ($DB->nbResults()!=0) {
	    while ($row = $DB->fetch(0))
	    {
	        $group_id=$row["id"];
	        $group_name=$row["name"];
	        echo "<group>";
	        echo "<g_id>".$group_id."</g_id>";
	        echo "<g_name><![CDATA['".$group_name."']]></g_name>";
	        echo "</group>";
	    }  
        $DB->freeResults();
	}
}
else {
	//get user groups
	$DB->getResults($users_getUserGroup,$DB->escape($user));
	if ($DB->nbResults()!=0) {
	    while ($row = $DB->fetch(0))
	    {
	        $group_id=$row["id"];
	        $group_name=$row["name"];
	        echo "<group>";
	        echo "<g_id>".$group_id."</g_id>";
	        echo "<g_name><![CDATA['".$group_name."']]></g_name>";
	        echo "</group>";
	    }  
        $DB->freeResults();
	}
}  

//get admin tabs
$DB->getResults($tabs_getUpdateAdminTabs,$DB->escape($user));
if ($DB->nbResults()!=0) 
{
    while ($row = $DB->fetch(0))
    {
        $tab_id=$row["tab_id"];
        echo "<tab>";
        echo "<tab_id>".$tab_id."</tab_id>";
        echo "</tab>";
    }  
}
$DB->freeResults();
  
$file->footer();
?>