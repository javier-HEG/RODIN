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
# Save newspaper show type information
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$nb=(isset($_POST["nb"]))?$_POST["nb"]:exit();
$prof=(isset($_POST["prof"]))?$_POST["prof"]:exit();

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_newspaper.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

launch_hook('scr_newspaper');

$file=new xmlFile();

$file->header();

//change the showtype variable
$chk=$DB->execute($scrnewspaper_setNbNews,$DB->escape($nb),$DB->escape($_SESSION['user_id']),$DB->escape($prof));
$file->status($chk);

$file->footer();

$DB->close();
?>