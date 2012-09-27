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
# APPLICATION INDEX (generic portal)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

//manage autoconnection
if (!empty($_COOKIE['autoi'])){
header("location:scr_authentif.php");
exit();

}

$folder="";
$not_access=0;
$isScript=false;
$isPortal=true;
$pagename="portal/index.php";
$useTabs=true;
//includes
require_once('includes.php');

launch_hook('index_php');

//[ 1750237 ] Displaying mypage even if accessing to the index
if ($_SESSION["user_id"]){header("location:".($_SESSION['type']=="A"?"../admin/":"mypage.php"));exit();}

//load theme
require_once('../../app/exposh/templates/'.__template.'/index.php');

?>