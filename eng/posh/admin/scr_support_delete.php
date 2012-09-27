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
# POSH Support - delete logs
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_support_delete.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();
$file->header("supportdelete");

if (isset($_POST['delay'])) {
    $last_week = date("Y-m-d", time()-7*24*3600);
    $chk=$DB->execute($support_deleteOldLogs,$DB->quote($last_week)); 
}
else {
    $chk=$DB->execute($support_deleteAllLogs);
}

$file->status($chk);
$file->footer();
?>