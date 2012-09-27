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
# POSH groups tabs informations
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_get_tabs_infos.php";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("tabs");

$group=isset($_GET["group"])?$_GET["group"]:0;


if (__useGroup && __defaultmode=="connected")
{
	if($_SESSION['user_id']>1)
	{
		//get user groups
        $list_groupId="";
		$DB->getResults($adm_getGroupId,$DB->escape($_SESSION['user_id']));
		while ($row = $DB->fetch(0))
		{
			$list_groupId.="'".$row["group_id"]."',";
		}
		$list_groupId=substr($list_groupId,0,strlen($list_groupId)-1);
		$DB->freeResults();
		
		$DB->getResults($pages_selectAdminGroups,$list_groupId);
		
	} else {
		$DB->getResults($pages_selectMainGroups);
	}
	while ($row=$DB->fetch(0))
	{
        $id=$row['id'];
        $name=$row['name'];
		echo '<option>';
        echo '<id>'.$id.'</id>';
    	echo '<name>'.$name.'</name>';
		echo '</option>';
	}
	$DB->freeResults();
}
	
if($_SESSION['user_id']>1 && $group==0) {
    $file->footer("tabs");
    exit;
}	
else
	$DB->getResults($pages_getTabsList,$DB->escape($group));

$nbr_result = $DB->nbResults();
echo "<nb>".$nbr_result."</nb>";
while($row = $DB->fetch(0)){
    $t_id=$row["id"];
    $t_name=$row["name"];
    $t_desc=$row["description"];
    $t_mode=$row["mode"];
    $t_type=$row["type"];
    $t_param=$row["param"];
    echo "<tab>";
    echo "<id>".$t_id."</id>";
    echo "<name>".$t_name."</name>";
    echo "<description>".$t_desc."</description>";
    echo "<mode>".$t_mode."</mode>";
    echo "<type>".$t_type."</type>";
    echo "<param>".$t_param."</param>";
    echo "</tab>";
}
$DB->freeResults();

$file->footer("tabs");
?>