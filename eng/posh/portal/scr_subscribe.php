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
# User subscription
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$user=(isset($_POST["u"]))?$_POST["u"]:exit();
$name=(isset($_POST["l"]) && !empty($_POST["l"]))?$_POST["l"]:$user;
$mail=$user;
$errormsg=" ";
if (!isset($_POST["nbSpecificFields"]) || ($_POST["nbSpecificFields"]==0)) 
	$nbSpecificFields=0;
else
	$nbSpecificFields=$_POST["nbSpecificFields"]; 

//check the password
if(Empty($_POST["p"])){$errormsg.=lg("passwordNotTyped").".<BR />";}
$password=$_POST["p"];

$folder="";
$not_access=0;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_subscribe.php";
//includes
require_once('includes.php');
require_once('../includes/misc.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../includes/xml.inc.php');
require_once('../includes/mail.inc.php');

launch_hook('scr_subscribe',$user,$password);

$file=new xmlFile();
$file->header();
$md5user = "";
//check the email validity
if (__accountType=="mail")
{
	if(!is_email($user))
		$errormsg.=lg("erroneousEmail").".<br />";
}
//check that the user is not yet existing
$DB->getResults($scrsubscribe_checkUser,$DB->noHTML($user));
if ($DB->nbResults()>0)		$errormsg=lg("alreadyMember");
$DB->freeResults();
		
//register the new user in the DB
if($errormsg==" ")
{
	launch_hook('register_new_user',$user,$password);
	$md5pass=md5($password);
	$md5user=md5($user.$password);
	$DB->execute($scrsubscribe_addUser,$DB->noHTML($user),$DB->quote($md5pass),$DB->noHTML($name),$DB->quote($md5user),$DB->quote(__LANG));
	$id=$DB->getId();
    
    $_SESSION['temp_id']=$id;
    
	launch_hook('register_more_info_for_user',$id);
	
	//autoconnection
	if (isset($_POST["auto"]))
	{
		setcookie('autoi',$id,time()+31536000);
		setcookie('autop',$md5pass,time()+31536000);
	}

	$DB->execute($scrsubscribe_log,$DB->escape($id));

	//specific criterias for the user
	if ($nbSpecificFields!=0)   {
		for ($i=1;$i<=$nbSpecificFields;$i++)
		{
			$parameters="";
			$infoid=$_POST["c_id".$i];
			if (isset($_POST["userinfo".$i]))   {
					if (is_array($_POST["userinfo".$i]) && count($_POST["userinfo".$i])>0)    {
                        if(count($_POST["userinfo".$i])==1 && ereg(';',$_POST["userinfo".$i][0])) {
                            $parameters=str_replace(";", ",", $_POST["userinfo".$i][0]);
                        }
                        else {
                            for ($j=0;$j<count($_POST["userinfo".$i]);$j++)
    						{	
    							if ($j==0)	{ 
                                    $parameters = $_POST["userinfo".$i][$j]; 
                                }
    							else { 
                                    $parameters .= ",".$_POST["userinfo".$i][$j];
                                }
    						}
                        }
					}	
					else    {
						if ($_POST["userinfo".$i]!="")	$parameters=$_POST["userinfo".$i];
					}
			}					
			//sql query to add	
			$DB->execute($users_addUserInfos,$DB->escape($id),$DB->escape($infoid),$DB->quote($parameters));
		}
	}
    
	$file->status(1);
	echo '<ret>user</ret>';

	//confirm registration by email
	$DB->getResults($config_getNotification,$DB->quote(__LANG),'emailConfirmation');
	$row = $DB->fetch(0);
	$notif_subject=stripslashes($row["subject"]);
	$notif_message=stripslashes($row["message"]);
	$notif_sender=$row["sender"];
	$notif_copy=$row["copy"];
	$DB->freeResults();

	$s_mail = new mail();
	$s_mail->addSender($notif_sender);

	$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$key="";
	srand((double)microtime()*1000000);
	for($i=0;$i<10;$i++) $key.= $str[rand()%62];
	//add unsubscribe to the message
	$unsubscribeLink = __LOCALFOLDER.'portal/login.php?id='.$id.'&md5='.$md5user;
	$unsubscribe = lg('accountUnsubscribe').lg('lblClickHere').' : '.$unsubscribeLink;
	$value = array($id, $key, __APPNAME, __LOCALFOLDER, $unsubscribe);
	$alias = array("%id", "%key", "%site", "%link", "%unsubscribe");

	$s_mail->addSubject($notif_subject,$value,$alias);
	$s_mail->addMessage($notif_message,$value,$alias);
	$s_mail->configArray($notif_copy,'1');
	$s_mail->configArray($mail,'2');
	$s_mail->sendMail();

	//set user Key for validation
	$DB->execute($scrsubscribe_setUserValidationKey,$key,$id);	
}
else
{
	$file->status(1);
	$file->error($errormsg);
}

$file->footer();
$DB->close();
?>