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
# Invite user to join groups
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$not_access=1;
$pagename="portal/src_group_adduser.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');
require_once('../includes/mail.inc.php');

$file=new xmlFile();

$file->header();

$senderUserId=$_SESSION['user_id'];
$senderUserLongname=$_SESSION['longname'];
//invited user id
$userId = $_POST["id"];

//retreive user info (lang and mail adresse)
$DB->getResults($scrgroup_getUserLangUsername,$DB->escape($userId));
$row=$DB->fetch(0);
$lang=$row['lang'];
$username=$row['username'];
$DB->freeResults();

//define the subject and the message
$notif_subject=lg("groupNotifSubject");
$notif_message=lg("groupNotifMessage",$senderUserLongname);

$inc=0;
$totalGroups=0;
while (isset($_POST["gId".$inc]))
{
	$groupId = $_POST["gId".$inc];
    
	$DB->getResults($scrgroup_countuser, $DB->escape($userId), $DB->escape($groupId));
    $userIsMemberOfGroup = $DB->nbResults();
	$DB->freeResults();

	if (0 == $userIsMemberOfGroup) 
	{
		$DB->getResults($scrgroup_getGetGroupName,$DB->escape($groupId),1);
		$row=$DB->fetch(0);
		$groupName=$row['name'];
		$DB->freeResults();
	
		$DB->execute($scrgroup_adduser, $DB->escape($userId), $DB->escape($groupId), $DB->quote("I"), $DB->escape($_SESSION['user_id']), $DB->escape($groupId), $DB->escape($_SESSION['user_id']));	
		$totalGroups++;
		//list of the group put in the mail body
		$notif_message.="\n - ".$groupName;
	}
	$inc++;
}

//if the invitied user is not already in any proposed groups, send mail notification
if ($totalGroups!=0)
{
	if (__NOTIFICATIONEMAIL!="")
	{
        $notif_sender=__NOTIFICATIONEMAIL;
		$notif_message.="\n".lg('groupNotifMessage2',__LOCALFOLDER);
        $notif_message.="\n\n".lg("bestRegards")."\n".__APPNAME."\n".__LOCALFOLDER;
		$s_mail = new mail();
		$s_mail->addSender($notif_sender);
		$s_mail->addSubject($notif_subject);
		$s_mail->addMessage($notif_message);
		$s_mail->configArray($username,'2');
		if(!$s_mail->sendMail())	exit();
	}
}

$file->status(1);

$file->footer();

$DB->close();
?>