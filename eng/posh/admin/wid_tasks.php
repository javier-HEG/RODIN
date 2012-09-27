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

$folder="";
$not_access=1;
$pagename="admin/wid_tasks.php";
$granted="A";
//includes
require_once('includes.php');;
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

$act=$_POST["act"];

if ($act=="sup"){
	$DB->execute($widtask_removeTask,$DB->quote($_POST["taskid"]));
}
if ($act=="add"){
	$id=$DB->escape($_POST["modid"]);
	if ($id==0){
		$DB->execute($widtask_addId);
		$id=$DB->getId();
		$DB->execute($widtask_addTask,$id,$DB->quote($_POST["cat"]),$DB->quote($_POST["name"]));
	} else {
		$DB->execute($widtask_addTask,$id,$DB->quote($_POST["cat"]),$DB->quote($_POST["name"]));
	}
	echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
}
if ($act=="done"){
	$DB->execute($widtask_changeStatus,$DB->quote($_POST["val"]),$DB->escape($_POST["taskid"]));
}

$file->status("1");

$file->footer("channel");
?>