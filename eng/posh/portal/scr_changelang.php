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
# Change user language
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************
	

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_changelang.php";
$granted="I";

//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');
launch_hook('scr_changelang');


$file=new xmlFile();
$file->header();


if (isset($_POST['lang']))
$lang=$_POST['lang'];


// Change language in user information table
$chk=$DB->execute($scrChangeLang,$DB->quote($lang),$DB->escape($_SESSION['user_id']));
//modify the current language in the session
$_SESSION['lang']=$lang;

$file->status($chk);
$file->footer();

$DB->close();

?>