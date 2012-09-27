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
# Insert a new module in a portal from other page
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$srcprof=$_POST["src"];
$destprof=$_POST["dest"];
$uniq=$_POST["uniq"];

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_addmodule_othertab.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

launch_hook('scr_othertab');

$file=new xmlFile();

$file->header();

//get the new place in destination tab
$DB->getResults($scrothertab_getNewPos,$DB->escape($destprof),$DB->escape($_SESSION['user_id']));
$row=$DB->fetch(0);
$newpos=$row["newpos"];
if (empty($newpos))$newpos=1;
$DB->freeResults();

//add the module in the new tabs
$DB->execute($scrothertab_updateModule,$DB->escape($destprof),$DB->escape($newpos),$DB->escape($_SESSION['user_id']),$DB->escape($uniq),$DB->escape($srcprof));

if ($DB->nbAffected()==1)
{
	$file->status(1);
	$file->returnData($uniq.'_'.$newpos.'_'.$_POST["tabdest"]);
}
else
{
	$file->status(0);
}

$file->footer();

$DB->close();
?>