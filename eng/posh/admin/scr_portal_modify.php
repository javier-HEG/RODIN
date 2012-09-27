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
# POSH Portal management - Apply portal changes
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_portal_modify.php";
//includes
require_once('includes.php');
require_once('../includes/refreshcache.inc.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');

$id=$_POST["id"];

$DB->execute($scrportalmodify_updatePortal,$DB->quote($_POST["name"]),$DB->quote($_POST["desc"]),$DB->quote($_POST["status"]),$DB->escape($id));

refresh_portal($id,$template_folder,$DB);

$DB->close();

header("location:frm_portal_modify.php?id=".$id."&reload=1");
?>