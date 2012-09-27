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
# POSH notifications management - 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/scr_config_notification.php";
$errLog="";
$tabname="configstab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("notification");

$notif_id=$_POST['id'];
$notif_lang=isset($_POST['nlang']) ? $_POST['nlang'] : '' ;

$type=$_POST['type'];
$notif_sender=stripslashes($_POST['sender']);
$notif_subject = $_POST['subject'];
$notif_message=$_POST['message'];	
$notif_copy=trim($_POST['copy']);
if (  $notif_id > 0 )  {    
    $chk=$DB->execute($config_setNotification,$DB->quote($notif_subject),$DB->quote($notif_message),$DB->quote($notif_sender),$DB->quote($notif_copy),$DB->escape($notif_id));
} else {
    $chk=$DB->execute($config_setNewNotification,$DB->quote($notif_subject),
                                                    $DB->quote($notif_message),
                                                    $DB->quote($notif_sender),
                                                    $DB->quote($notif_copy),
                                                    $DB->quote($notif_lang),
                                                    $DB->quote($type)
                                                    );
}
$file->status($chk);

$file-> footer();