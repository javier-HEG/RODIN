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
# 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

if (!isset($_GET["prof"])){exit();}

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_exchangemod.php";
$granted="I";
$erreur="";
//includes
require_once('includes.php');

launch_hook('scr_exchangemod');

$prof=$_GET["prof"];

//exchange the module ID
$DB->execute($screxchangemod_update,$DB->escape($_GET["id2"]),$DB->escape($_SESSION['user_id']),$DB->escape($prof),$DB->escape($_GET["id1"]));

$DB->close();

header("location:mypage.php?added=1&start=" . $prof);
?>