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
# HTML modules PHP scripts
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$pagename="admin/html.php";
$granted="A";
//includes
require_once('includes.php');;
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

if ($_POST["htmlid"]==0){
	$DB->execute($widhtml_newContent,$DB->quote($_POST["html"]));
	echo '<ret>'.$DB->getId().'</ret>';
} else {
	$DB->execute($widhtml_updateContent,$DB->quote($_POST["html"]),$DB->escape($_POST["htmlid"]));
}

$file->status("1");

$file->footer("channel");
?>