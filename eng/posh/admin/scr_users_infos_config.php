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
# POSH Users criteria management 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/scr_users_infos_config.php";
$tabname="userstab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("criterias");

//data set
$label="";
$type="";
$mandatory="";
$editable="";
$displayon="";
$options="";
$errLog="";
$err=0;

if (isset($_POST['checkpoint']) && isset($_POST['totalCriteria']))
{
	//total number of criteria
	$all = $_POST['totalCriteria'];
	for ($i=0;$i<$all;$i++)
	{
		$label = stripslashes($_POST['label'.$i]);	
		$type = $_POST['type'.$i];
		$options = $_POST['options'.$i];
		$mandatory = $_POST['mandatory'.$i];
		$editable = $_POST['editable'.$i];
		//if options are specified for the wrong type, the $option variable is cleared
		if (($type==1) || ($type==5)) $options="";
		$DB->execute($user_addUserInformations,$DB->quote($label),$DB->escape($type),$DB->quote($options),$DB->escape($mandatory),$DB->escape($editable));		
	}
}

if (isset($_POST['updateAction']))
{
	//total number of criteria to update
	$totalUpdate=$_POST['updateAction'];
	for ($i=0;$i<$totalUpdate;$i++)
	{
		$id = $_POST['updateId'.$i];
		$label = stripslashes($_POST['updateLab'.$i]);	
		//$type = $_POST['updateTyp'.$i];
		//$options = $_POST['updateOpt'.$i];
		$mandatory = $_POST['updateMan'.$i];
		$editable = $_POST['updateEdi'.$i];
		if (($type==1) || ($type==5)) $options="";
		//$DB->execute($user_updateUserInformations,$DB->quote($label),$DB->escape($type),$DB->quote($options),$DB->escape($mandatory),$DB->escape($editable),$DB->escape($id));
		$DB->execute($user_updateUserInformations,$DB->quote($label),$DB->escape($mandatory),$DB->escape($editable),$DB->escape($id));	
	}
}

$file->status(1);
$file->footer();
?>