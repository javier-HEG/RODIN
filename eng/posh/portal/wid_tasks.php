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
# Tasks module PHP scripts
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

header("content-type: application/xml"); 
$folder="";
$not_access=1;
$pagename="portal/wid_tasks.php";
$granted="I";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'><channel><status>1</status>';

$act=$_POST["act"];

if ($act=="sup")
{
	$id=$_POST["modid"];
	$DB->execute($widtask_RemoveTask,$DB->escape($_POST["taskid"]),$DB->escape($_SESSION['user_id']));
	if ($DB->nbAffected==0)
	{
		$DB->execute($widtask_createNewId,$DB->escape($_SESSION['user_id']));
		$oldid=$id;
		$id=$DB->getId();
		$DB->execute($widtask_copyTasks,$id,$DB->escape($oldid),$DB->escape($_POST["taskid"]));
		echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
	}
}
if ($act=="add")
{
	$id=$_POST["modid"];
	$taskid=$_POST["taskid"];
	if ($id==0)
	{
		$DB->execute($widtask_addNewTaskId,$DB->escape($_SESSION['user_id']));
		$id=$DB->getId();
		$DB->execute($widtask_addNewTask,$DB->escape($id),$DB->noHTML($_POST["cat"]),$DB->noHTML($_POST["name"]));
	}
	else
	{
		if ($taskid=="")
		{
			$DB->execute($widtask_addNewTaskOnExistingMod,$DB->escape($id),$DB->noHTML($_POST["cat"]),$DB->noHTML($_POST["name"]),$DB->escape($id),$DB->escape($_SESSION['user_id']));
		}
		else
		{
			$DB->execute($widtask_updateTask,$DB->noHTML($_POST["name"]),$DB->noHTML($_POST["cat"]),$taskid,$DB->escape($_SESSION['user_id']));
		}
		if ($DB->nbAffected()==0)
		{
			$DB->execute($widtask_createNewMod,$DB->escape($_SESSION['user_id']));
			$oldid=$id;
			$id=$DB->getId();
			$DB->execute($widtask_insertExistingTasks,$id,$DB->escape($oldid));
			if ($taskid=="")
			{
				$DB->execute($widtask_newModInsertTask,$DB->escape($id),$DB->noHTML($_POST["cat"]),$DB->noHTML($_POST["name"]));
			}
			else
			{
				$DB->execute($widtask_newModUpdateTask,$DB->noHTML($_POST["name"]),$DB->noHTML($_POST["cat"]),$DB->escape($taskid));
			}
		}
	}
	echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
}
if ($act=="done")
{
	$DB->execute($widtask_changeStatus,$DB->noHTML($_POST["val"]),$DB->escape($_POST["taskid"]));
}

$DB->close();
echo "</channel>";
?>