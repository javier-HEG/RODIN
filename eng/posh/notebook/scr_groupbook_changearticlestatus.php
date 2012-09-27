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
# Change article status
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$artid=isset($_POST["id"])?$_POST["id"]:exit();
$groupId = isset($_POST["group"])?$_POST["group"]:exit();

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="notebook/scr_groupbook_changearticlestatus.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');
$file=new xmlFile();

$file->header();

$status = isset($_POST["status"])?$_POST["status"]:"";

$check=$DB->execute($groupbook_changeArticleStatus, $DB->noHTML($status), $DB->escape($artid), $groupId);
if ($check)
{
	$file->status(1);
	$file->returnData($artid."_".$_POST["status"]."_".$groupId);
	
	if ("D"== $_POST["status"]) {
		$DB->execute($scrnotebook_decreaseTrackbackNb, $DB->escape($artid));
	}
	
}
else
{
	$file->status(0);
}


$file->footer();

$DB->close();
?>