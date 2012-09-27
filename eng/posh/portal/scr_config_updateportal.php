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

if (!isset($_GET["prof"])) exit();
$prof=$_GET["prof"];
if (!isset($_GET["modid"])) exit();
$modid=$_GET["modid"];

$folder="";
$pagename="portal/scr_config_updateportal.php";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="I";
$md5="";
//includes
require_once('includes.php');

launch_hook('scr_config_updateportal');

$vars=isset($_GET["v"])?$vars=$_GET["v"]:"";
if ( isset($_GET["md5"]) && $_GET["md5"]!='') {
    $md5=$_GET["md5"];
}

//change widget profile information
$uniq=1;
$DB->getResults($xmlconfig_getMaxUniq,$DB->escape($_SESSION['user_id']),$DB->escape($prof));
if ($DB->nbResults()>0) {
    $row = $DB->fetch(0);
    $uniq = $row['uniq'];
    $DB->freeResults();
}
if ($uniq=="") { $uniq=1; }

$DB->execute($scrconfig_addNewModule,$DB->escape($_SESSION['user_id']),$DB->escape($prof),$DB->quote($vars),$DB->escape($uniq),$DB->escape($_GET["f"]),$DB->quote($md5),$DB->escape($modid));

$DB->close();

header("location:mypage.php");
?>