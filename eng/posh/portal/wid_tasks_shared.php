<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of COLLABORATION SUITE of POSH http://sourceforge.net/projects/posh/.

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
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

header("content-type: application/xml"); 
$folder="";
$not_access=1;
$pagename="portal/wid_tasks.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'><channel><status>1</status>';

$act = isset($_POST["act"])?$_POST["act"]:"";
$taskid = isset($_POST["taskid"])?$_POST["taskid"]:-1;
	
if ($act=="sup")
{
	//suppress task
	$DB->execute($widtaskshared_removeTask,$DB->escape($taskid));
}
if ($act=="add")
{
	//add new task
	$id=$_POST["modid"];
	$cat = isset($_POST["cat"])?$_POST["cat"]:"";
	$name = isset($_POST["name"])?$_POST["name"]:"";
	if ($id==0)
	{
		$DB->execute($widtaskshared_addNewTaskId);
		$id=$DB->getId();
		$DB->execute($widtaskshared_addNewTask,$DB->escape($id),$DB->noHTML($cat),$DB->noHTML($name));
	}
	else
	{
		$DB->execute($widtaskshared_insertTask,$DB->escape($id),$DB->noHTML($cat),$DB->noHTML($name));
	}
	echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
}
if ($act=="done")
{
	$val = isset($_POST["val"])?$_POST["val"]:"";
	// check a task
	$DB->execute($widtaskshared_updStatus,$DB->quote($val),$DB->escape($taskid));
}

$DB->close();
echo "</channel>";
?>