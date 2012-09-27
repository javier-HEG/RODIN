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
# POSH Admin - set interface language
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

if (!isset($_POST["lang"])) exit();

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_admin_lang.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

launch_hook('admin_scr_admin_lang');

$file=new xmlFile();

$file->header();

// save admin lang
$chk=$DB->execute($admin_setLang,$DB->quote($_POST["lang"]),$DB->escape($_SESSION['user_id']));
$file->status($chk);

$file->footer();
?>