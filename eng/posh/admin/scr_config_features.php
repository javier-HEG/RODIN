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
# POSH Configuration - save interface personnalization 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_config_features.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("setfeatures");

launch_hook('admin_scr_config_features');
if (isset($_POST["menuheader"]))
{
	$i=0;
	$DB->execute($configfeatures_deleteHeadLinks);
	while (isset($_POST["id".$i]))
	{
		$DB->execute($configfeatures_insertHeadLinks,$DB->escape($_POST["id".$i]),$DB->quote($_POST["uniq_id".$i]),$DB->quote($_POST["type".$i]),$DB->quote($_POST["label".$i]),$DB->quote($_POST["comment".$i]),$DB->quote($_POST["clss".$i]),$DB->quote($_POST["image".$i]),$DB->quote($_POST["fct".$i]),$DB->escape($_POST["seq".$i]),$DB->escape($_POST["anonymous".$i]),$DB->escape($_POST["connected".$i]),$DB->escape($_POST["admin".$i]));
		launch_hook('admin_menuheader_insert',$i,$DB->getId());
		$i++;
	}
}

if (isset($_POST["menuedit"]))
{
	$usereader=isset($_POST["usereader"])?"true":"false";
	$DB->execute($configfeatures_setUseReader,$DB->quote($usereader));
	if ($DB->nbAffected()==0)
		$DB->execute($configfeatures_insertUseReader,$DB->quote($usereader));
	
	$showtabicon=isset($_POST["showtabicon"])?"true":"false";
	$DB->execute($configfeatures_setShowTabIcon,$DB->quote($showtabicon));
	if ($DB->nbAffected()==0)
		$DB->execute($configfeatures_insertShowTabIcon,$DB->quote($showtabicon));
	
	$columnchange=isset($_POST["columnchange"])?"true":"false";
	$DB->execute($configfeatures_setColumnChange,$DB->quote($columnchange));
	if ($DB->nbAffected()==0)
		$DB->execute($configfeatures_insertColumChange,$DB->quote($columnchange));
	
	$ctrlhiding=isset($_POST["ctrlhiding"])?"true":"false";
	$DB->execute($configfeatures_setCtrlHiding,$DB->quote($ctrlhiding));
	if ($DB->nbAffected()==0)
		$DB->execute($configfeatures_insertCtrlHiding,$DB->quote($ctrlhiding));
	
	$doubleprotection=isset($_POST["doubleprotection"])?"true":"false";
	$DB->execute($configfeatures_setDoubleProtection,$DB->quote($doubleprotection));
	if ($DB->nbAffected()==0)
		$DB->execute($configfeatures_insertDoubleProtection,$DB->quote($doubleprotection));
	
	$moduleAlign=isset($_POST["modulealign"])?"true":"false";
	$DB->execute($configfeatures_setModuleAlign,$DB->quote($moduleAlign));
	if ($DB->nbAffected()==0)
		$DB->execute($configfeatures_insertModuleAlign,$DB->quote($moduleAlign));
}
	
if (isset($_POST["menuadd"]))
{
	$showrsscell=isset($_POST["showrsscell"])?"true":"false";
	$DB->execute($configfeatures_setShowRssCell,$DB->quote($showrsscell));
	if ($DB->nbAffected()==0)
		$DB->execute($configfeatures_insertShowRssCell,$DB->quote($showrsscell));

	$showModuleSearch=isset($_POST["showModuleSearch"])?"true":"false";
	$DB->execute($configfeatures_setShowModuleSearch,$DB->quote($showModuleSearch));
	if ($DB->nbAffected()==0)
		$DB->execute($configfeatures_insertShowModuleSearch,$DB->quote($showModuleSearch));

	$showModuleExpl=isset($_POST["showModuleExpl"])?"true":"false";
	$DB->execute($configfeatures_setShowModuleExpl,$DB->quote($showModuleExpl));
	if ($DB->nbAffected()==0)
		$DB->execute($configfeatures_insertShowModuleExpl,$DB->quote($showModuleExpl));
}

//header("location:scr_config_generate_configfiles.php?redirect=config");

$file->status(1);

$file->footer();
?>