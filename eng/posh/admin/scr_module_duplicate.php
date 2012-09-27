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
# POSH Module management - duplicate widget
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$id=isset($_POST["itemid"])?$_POST["itemid"]:exit();
$icon=isset($_POST["icon"])?$_POST["icon"]:exit();
$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_module_duplicate.php";
//includes
require_once("includes.php");
require_once('../includes/refreshcache.inc.php');
require_once('../includes/misc.inc.php');
require_once('../includes/xml.inc.php');

launch_hook('admin_scr_module_duplicate');

$chk=0;
$chk1=0;

//create duplicated widget
$chk=$DB->execute($scr_moduleDuplicate,$DB->escape($id)); 
$newid=$DB->getId();

//insert into dir_item_external
$DB->execute($scr_moduleDuplicateExternal,$DB->escape($newid),$DB->escape($id));
//insert into dir_item_external_language 
$DB->execute($scr_moduleDuplicateExternalLanguage,$DB->escape($newid),$DB->escape($id)); 

//get the original widget categoy
$DB->getResults($module_getModuleCategory,$DB->escape($id));
$row=$DB->fetch(0);
$module_cat=$row['category_id'];
$module_first=$row['first'];

//create duplicated widget category
$chk1=$DB->execute($scr_moduleDuplicateCategory,$DB->escape($newid),$DB->escape($module_cat),$DB->quote($module_first));

//refresh the cache
refresh_item($newid,$template_folder);
refresh_directory($module_cat,$template_folder,"");

//copy the icon
$extensionlogo = strrchr($icon,'.');
if( strlen($extensionlogo)>5 ) {
	$extensionlogo = ".gif";
}
copy($icon,"../modules/pictures/box0_".$newid.$extensionlogo);


//update icon widget
$DB->execute($scr_moduleUpdateIcon,$DB->quote("../modules/pictures/box0_".$newid.$extensionlogo),$DB->escape($newid)); 

$file=new xmlFile();
$file->header("duplicate");
$file->status($chk*$chk1);
$file->footer();
?>