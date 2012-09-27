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
# Change user activity
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$not_access=1;
$pagename="portal/scr_chat_activity.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("notifications");

//Set activity status
if (isset($_GET["act"]))
{
	$DB->execute($scrchat_setactivity,
                    $DB->quote($_GET["act"]),
                    $DB->escape($_SESSION['user_id']));
    $_SESSION['availability'] = $_GET["act"];
}

//Send notifications
if (isset($_GET["inac"])) // user inactive
{
	$inac=explode(",",$_GET["inac"]);
	foreach ($inac AS $iid)
	{
		$DB->execute($scrchat_newnotification,
                        $iid,
                        $DB->escape($_SESSION['user_id']),
                        "'I'");
	}
}
if (isset($_GET["writing"])) // user writing
{
	$writing=explode(",",$_GET["writing"]);
	foreach ($writing AS $wid)
	{
		$DB->execute($scrchat_newnotification,
                        $wid,
                        $DB->escape($_SESSION['user_id']),
                        "'W'");
	}
}

//check if new notification
$userid=$DB->escape($_SESSION['user_id']);
$DB->getResults($scrchat_getNotification,$userid);
if ($DB->nbResults()!=0)
{
	$chats=array();
	$messages=array();
	while ($row=$DB->fetch(0))
	{
		if ($row["type"]=='C')
		{
			array_push($chats,$row["notification_id"]);
			array_push($messages,$row["notification_id"]);
		}
		if ($row["type"]=='M')
			array_push($messages,$row["notification_id"]);
		if ($row["type"]=='W' || $row["type"]=='I')
		{
			echo "<writing><userid>".$row["notification_id"]."</userid><type>".$row["type"]."</type></writing>";
		}
	}
	$DB->freeResults();

	$DB->execute($scrchat_deleteNotifications,$userid);

	//get New chats list
	if (count($chats)!=0)
	{
		$chatFilter=implode(',',$chats);
		$DB->getResults($scrchat_getNewChats,$userid);
		while ($row=$DB->fetch(0))
		{
			echo "<newchat><id>".$row["id"]."</id><userid>".$row["owner_id"]."</userid><name><![CDATA[".$row["long_name"]."]]></name></newchat>";
		}
		$DB->execute($scrchat_setChatsAreOpened,$userid);
	}

	//get New messages
	if (count($messages)!=0)
	{
		$messageFilter=implode(',',$messages);
		$DB->getResults($scrchat_getMessages,
                            $userid,
                            $messageFilter);
		while ($row=$DB->fetch(0))
		{
			echo "<message><chatid>".$row["chat_id"]."</chatid><content><![CDATA[".$row["message"]."]]></content></message>";
		}
		$DB->freeResults();

		$DB->execute($scrchat_setMessagesAsRead,
                        $userid,
                        $messageFilter);
	}
}

$file->footer();

$DB->close();
?>