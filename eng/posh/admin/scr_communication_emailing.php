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
# POSH Configuration - send mailing
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_communication_emailing.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("emailing");

launch_hook('admin_scr_communication_emailing');
$list_groupId="";
$list_usersId="";

//check email
function checkEmail($v_email)
{
	if(strpos($v_email,"@")===false OR strpos($v_email,".")===false OR strpos($v_email," ")===true) return false;
	else return true;
}

//get emails address
$emaillist=array();
if (isset($_GET["emailtype"]) && $_GET["emailtype"]=="1")
{
	$receiver=$_GET["emaillist"];
	$emaillist=explode(",",$receiver);
}
else
{
	$receiver="All";
	if($_SESSION['user_id']>1)
	{
		//get  all group of the connected admin
		$DB->getResults($admin_getUserGroup,$DB->escape($_SESSION['user_id']));
		while ($row = $DB->fetch(0))
		{
			$list_groupId.="'".$row["group_id"]."',";
		}
		$list_groupId=substr($list_groupId,0,strlen($list_groupId)-1);
		$DB->freeResults();
		//get user groups in subgroups
		$DB->getResults($adm_userIdSubGroup,$list_groupId);
		while ($row = $DB->fetch(0))
		{
			$list_groupId.=",'".$row["id"]."'";
		}
		$DB->freeResults();
		$DB->getResults($adm_userIdSubGroup,$list_groupId);
		while ($row = $DB->fetch(0))
		{
			$list_groupId.=",'".$row["id"]."'";
		}
		$DB->freeResults();
		//get users ids by group
		$DB->getResults($communication_getUsersIdByGroup,$list_groupId);
		while ($row = $DB->fetch(0))
		{
			$list_usersId.="'".$row["user_id"]."',";
		}
		$DB->freeResults();
		$list_usersId=substr($list_usersId,0,strlen($list_usersId)-1);
		//get active users emails
		$DB->getResults($communication_getUsersListByGroup,$list_usersId);
	} else {
		$DB->getResults($communication_getUsersList);
	}
	
	while ($row=$DB->fetch(0))
	{
		if (checkEmail($row["username"])) array_push($emaillist,$row["username"]);
	}
	$DB->freeResults();
	
}
//send email

for ($i=0;$i<count($emaillist);$i++)
{

	launch_hook('admin_communication_send_mail',$emaillist[$i],$_GET["subject"],$_GET["message"],$_GET["sender"]);
	
    if (
        mail($emaillist[$i],stripslashes(utf8_decode($_GET["subject"])),stripslashes(utf8_decode($_GET["message"])),"From: ".$_GET["sender"]."\r\n")
    ) 
    {
		echo "<state>1</state><label>emailSent</label>";
    } else {
        echo "<state>2</state><label>noEmailSent</label>";
    }

}

// save copy
if (isset($_GET["emailcopy"]))
{
	$DB->execute($communication_addSentItem,$DB->quote($_GET["sender"]),$DB->quote($_GET["subject"]),$DB->quote($_GET["message"]),$DB->quote($receiver));
	
	launch_hook('admin_communication_save_mail_copy',$_GET["sender"],$_GET["subject"],$_GET["message"],$receiver);
    $DB->close();
}

//$file->status(1);

$file->footer();
//header("location:communication_emailing.php?sent=1");
?>