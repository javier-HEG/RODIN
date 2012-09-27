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
# Suppress a user portal
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$seq=(isset($_POST["seq"]))?$_POST["seq"]:exit();
$id=(isset($_POST["id"]))?$_POST["id"]:exit();

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_suppersonal.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

launch_hook('scr_subpersonal',$id,$seq,$_SESSION['user_id']);

$file=new xmlFile();

$file->header();

// suppress selected tab
$DB->execute($scrsuppersonal_deleteTab,$DB->escape($_SESSION['user_id']),$id);
if ($DB->nbAffected()>0)
{
	$DB->execute($scrsuppersonal_deleteModules,$DB->escape($_SESSION['user_id']),$DB->escape($id));
	$file->status(1);
}
else $file->status(0);

// Change tabs order
$DB->execute($scrsuppersonal_updateTabPos,$DB->escape($_SESSION['user_id']),$DB->escape($seq));

$file->footer();

$DB->close();
?>