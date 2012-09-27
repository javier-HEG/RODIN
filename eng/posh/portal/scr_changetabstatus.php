<?php
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
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é

$folder="";
$pagename="portal/scr_changetabstatus.php";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');
launch_hook('scr_changetabstatus');

$file=new xmlFile();
$file->header();

//variables
if (!isset($_SESSION["user_id"])) exit();
if (!isset($_POST["new"])) exit();
else $new=$_POST["new"];
if (!isset($_POST["tabId"])) exit();
else $tabId=$_POST["tabId"];

$chk = $DB->execute($scrconfig_updatetabstatus,$DB->escape($new),$DB->escape($tabId),$DB->escape($_SESSION['user_id'])); 

$file->status($chk);
$file->footer();
$DB->close();
?>