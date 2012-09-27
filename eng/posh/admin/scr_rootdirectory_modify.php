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
# POSH Module management - Apply root directory modifications
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_rootdirectory_modify.php";
//includes
require_once('includes.php');
require_once('../includes/refreshcache.inc.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("directoryModify");

launch_hook('admin_scr_rootdirectory_modify');

$i=0;
While (isset($_POST["catid".$i]))
{
	//is it a new directory ? id=0 => new
	$id=$_POST["catid".$i];
	if ($id==0)
	{
		$DB->execute($rootdirectory_addDirectory,$DB->quote($_POST["catname".$i]),$DB->quote($_POST["catlg".$i]));
		$id=$DB->getId();
		$DB->execute($rootdirectory_addProperties,$DB->escape($id),$DB->escape($_POST["catseq".$i]));
	
		launch_hook('create_dir_category',$id);	
	}
	else
	{
		$DB->execute($rootdirectory_updateSeq,$DB->quote($_POST["catseq".$i]),$DB->escape($_POST["oldcatid".$i]),$DB->quote($_POST["oldcatseq".$i]));
		$DB->execute($rootdirectory_updateName,$DB->quote($_POST["catname".$i]),$DB->quote($_POST["catlg".$i]),$DB->escape($_POST["oldcatid".$i]));
			
		launch_hook('update_dir_category',$id);
		
	}
	refresh_directory($id,$template_folder,$_POST["catlg".$i]);
	$i++;
}

//refresh cache
refresh_directory(0,$template_folder,"");

//header("location:scr_rootdirectory_setconfig.php");

$file->status(1);
$file->footer();
?>