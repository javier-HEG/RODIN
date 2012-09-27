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
# save message
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$not_access=1;
$pagename="portal/scr_chat_message.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header();

$fid = isset($_POST["fid"])?$_POST["fid"]:-1;
$t = isset($_POST["t"])?$_POST["t"]:"";
$m = isset($_POST["m"])?$_POST["m"]:"";

if ($_POST["s"]=='N')
{
	// log new chat
	$DB->execute($scrchat_newchat,$DB->escape($_SESSION['user_id']),$DB->escape($fid),$DB->quote($t));
	$id=$DB->getId();
	//new chat notification
	$DB->execute($scrchat_newnotification,
                    $DB->escape($fid),
                    $DB->escape($id),
                    "'C'");
}
else
{
	$id=$DB->escape($_POST["id"]);
//	$DB->execute("UPDATE network_chat SET owner_status='w' WHERE id=%u AND owner_id=%u",$id,$_SESSION['user_id']);
	//new message notification
	$DB->execute($scrchat_newnotification,
                    $DB->escape($fid),
                    $DB->escape($id),
                    "'M'");
}

$DB->execute($scrchat_newmessage,
                $DB->escape($id),
                $DB->escape($_SESSION['user_id']),
                $DB->escape($fid),
                $DB->quote($m));

$file->status(1);
$file->returnData($id);

$file->footer();

$DB->close();
?>