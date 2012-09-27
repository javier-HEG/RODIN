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
# POSH Module management - Update config file with root directory modifications
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_rootdirectory_setconfig.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("dirsetconfig");

launch_hook('admin_scr_rootdirectory_setconfig');

$arr=Array();
$DB->getResults($rootdirectory_getDirectory);
while($row = $DB->fetch(0))
{
	array_push($arr,'{"seq":"'.$row["seq"].'","name":"'.$row["name"].'","id":'.$row["id"].',"lg":"'.$row["lang"].'"}');
}
$DB->freeResults();

$value=implode(',',$arr);
$value.=',{"seq":"'.count($arr).'","name":lg("lblList"),"id":0,"lg":""}';
$DB->execute($rootdirectory_getDimension,$DB->quote($value));
if ($DB->nbAffected()==0)
{
	$DB->execute($rootdirectory_insertDimension,$DB->quote($value));
}

$file->status(1);
$file->footer();

//header("location:scr_config_generate_configfiles.php?redirect=scr_reload_all");
?>