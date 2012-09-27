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
# POSH - XML List of the directories
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="I";
$pagename="portal/xml_directory.php";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("directories");

$id=$DB->escape(isset($_GET["catid"])?$_GET["catid"]:0);
if ( ($_SESSION['type']=='A' && $_SESSION['user_id']>1) ) {
    $list_groupId="'',";
    $list_categoryGroupId="'',";
	$DB->getResults($adm_getGroupId,$DB->escape($_SESSION['user_id']));
	while ($row = $DB->fetch(0))
	{
		$list_groupId.="'".$row["group_id"]."',";
	}
	$list_groupId=substr($list_groupId,0,strlen($list_groupId)-1);
	$DB->freeResults();
	$DB->getResults($users_getGroupCategoryMapXml,$list_groupId);
	while ($row = $DB->fetch(0))
	{
		$list_categoryGroupId.="'".$row["category_id"]."',";
	}
	$list_categoryGroupId=substr($list_categoryGroupId,0,strlen($list_categoryGroupId)-1);
	$DB->freeResults();
	$DB->getResults($module_getChildrenAdminDirectoryXml,$list_categoryGroupId,$DB->escape($id));
	$DB->sql;
}
else if ($_SESSION['type']=='I' ) {
    $DB->getResults($users_getWidgetsCategories,$DB->escape($_SESSION['user_id']),$DB->escape($id));
}
else {
$DB->getResults($module_getChildrenDirectoryXml,$DB->escape($id));
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

$file->footer("directories");
?>