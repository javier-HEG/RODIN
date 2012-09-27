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
# Save tabs position 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$id=(isset($_POST["id"]))?$_POST["id"]:exit();

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_movetab.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');
launch_hook('scr_movetab');

$file=new xmlFile();

$file->header();

$old = $_POST["old"];
$new = $_POST["new"];

// Move the previous tabs (keep order of the following actions !!)
$chk3=$DB->execute($scrmovetab_moveLeft,$DB->escape($_SESSION['user_id']),$DB->escape($old));
// move the next tabs
$chk1=$DB->execute($scrmovetab_moveRight,$DB->escape($_SESSION['user_id']),$DB->escape($new));	
// updated moved tab position
$chk2=$DB->execute($scrmovetab_movedTab,$DB->escape($new),$DB->escape($_SESSION['user_id']),$DB->escape($id));

$file->status($chk1&&$chk2&&$chk3);

$file->footer();

$DB->close();
?>