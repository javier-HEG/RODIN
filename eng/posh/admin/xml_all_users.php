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
# POSH Users management - XML List of all the users
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_all_users.php";
$list_groupId="";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("users");

$p=$_GET["p"];

echo "<page>".$p."</page>";

if($_SESSION['user_id']=="1") {
    $DB->getResults($users_getAllXml,$DB->escape(($p-1)*21));
    while ($row = $DB->fetch(0))
    { 
        echo "<user>";
        echo "<id>".$row["id"]."</id>";
        echo "<username><![CDATA[".$row["username"]."]]></username>";
        echo "<name><![CDATA[".$row["long_name"]."]]></name>";
        echo "<typ><![CDATA[".$row["typ"]."]]></typ>";
        echo "</user>";
    }
    $DB->freeResults();
}
else {
    $groupList="";
    $DB->getResults($adm_getGroupId,$DB->escape($_SESSION['user_id']));
    while ($row = $DB->fetch(0)) {
        $groupList.="'".$row['group_id']."',";
    }
    $groupList=substr($groupList,0,strlen($groupList)-1);
    $DB->freeResults();
  
    if ($_SESSION['user_id']>1 && $groupList=="") {
        $file->footer(); 
        exit;
    }

    $DB->getResults($adm_getUserIdByGroup,$groupList,$DB->escape(($p-1)*21));
    while ($row = $DB->fetch(0))
    {
            echo "<user>";
            echo "<id>".$row["id"]."</id>";
            echo "<username><![CDATA[".$row["username"]."]]></username>";
            echo "<name><![CDATA[".$row["long_name"]."]]></name>";
            echo "<typ><![CDATA[".$row["typ"]."]]></typ>";
            echo "</user>";
    }
    $DB->freeResults();
    
}

$file->footer("users");
?>