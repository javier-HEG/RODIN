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
# Suppress a widget created by user
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_mymodules_supp.php";
$granted="I";
//includes
require_once('includes.php');
launch_hook('scr_mymodules_supp');

// suppress a widget I created
$DB->execute($scrmymodules_removeTempMod,$DB->escape($_SESSION['user_id']),$DB->escape($_GET["id"]));
if ($DB->nbAffected()>0)
{
	$DB->execute($scrmymodules_removeTempModDirectory,$DB->escape($_GET["id"]));
}

$DB->close();

header("location:mypage.php");
?>