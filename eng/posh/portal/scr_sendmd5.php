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
# resend user password to his email address
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$lost_email=(isset($_POST["username"]))?$_POST["username"]:exit();

$folder="";
$not_access=0;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_sendmd5.php";
$password_missing="portal/password_missing.php";


//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../includes/xml.inc.php');
require_once('../includes/mail.inc.php');

launch_hook('scr_sendmd5_php');

$message='';

// Change password in user information table
$DB->getResults($authentif_getUserByName, $DB->quote($DB->escape($lost_email)));


if ($DB->nbResults()==0) {

	$status=1;
	
} else {
	
	$row = $DB->fetch(0);
	$DB->freeResults();
	//error_log($authentif_getUserByName ." -> ". $row['password']);
	resendPassword(array(
						'row' => $row,
						'lost_email' => $lost_email,
						'pagename'=>$pagename,
						'req' => array (
							'mail_getNotification' => $mail_getNotification
							)
						)
					);
  
	$message="<email>$lost_email</email>";
	$status=0;
}

if (isset($_POST["redirect"]))
{
	header('location:'.$_POST["redirect"].'.php');
}
else
{	
	$file=new xmlFile();
	
	$file->header();

	$file->status($status);
	echo $message;
	
	$file->footer();
}

function resendPassword ($values) {
      
	global $DB;
	$mail_getNotification = $values['req']['mail_getNotification'];
	$lost_email = $values['lost_email'];
	$pagename = $values['pagename'];
	$row = $values['row'];
	$md5pass = $row['password'];
	$lang = $row['lang'];
	$md5user = $row['md5user'];
	$userId = $row['id'];
	$password_missing="portal/password_missing.php";

	$linktosetpassword = __LOCALFOLDER . $password_missing . "\?mdp=$md5pass&email=$lost_email";
	
	//add unsubscribe to the message
	$unsubscribeLink = __LOCALFOLDER.'portal/login.php?id='.$userId.'&md5='.$md5user;
	$unsubscribe = lg('accountUnsubscribe').lg('lblClickHere').' : '.$unsubscribeLink;
	//tab with all the php values to include into the mail 
	$val = array(__APPNAME, __LOCALFOLDER, $linktosetpassword, $unsubscribe);
	//tab with all the pseudoCode tags
	$tab = array("%site", "%link","%setnewpwd", "%unsubscribe");
	$DB->getResults($mail_getNotification,$DB->quote($lang),$DB->quote('getNewPassword'));


	while ($row = $DB->fetch(0))
	{
		$notif_subject=stripslashes($row["subject"]);
		$notif_message=stripslashes($row["message"]);
		$notif_sender=$row["sender"];
		$notif_copy=$row["copy"];
	}
	
	$s_mail = new mail();
	$s_mail->addSender($notif_sender);
	$s_mail->addSubject($notif_subject,$val,$tab);
	$s_mail->addMessage($notif_message,$val,$tab);
	$s_mail->configArray($notif_copy,'1');
	$s_mail->configArray($lost_email,'2');
	
	$s_mail->sendMail();
	
   
	
        
	$DB->freeResults();

}


?>