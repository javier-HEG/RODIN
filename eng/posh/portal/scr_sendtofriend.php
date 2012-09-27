<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of COLLABORATION SUITE of POSH http://sourceforge.net/projects/posh/.

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
# Send the user's portal or module to his friends (XML return)
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_sendtofriend.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header();

echo '<msg><![CDATA[';

$ret="";
$desttype=(isset($_POST["desttype"])?$_POST["desttype"]:1);

if ($desttype==1)
{
	// if shared object is a module
	if ($_POST["obj"]=="m")
	{
		if (!isset($_POST["id"])) exit();
		if (!isset($_POST["v"])) exit();
		$id=$_POST["id"];
		$var=urlencode($_POST["v"]);
		$chk=md5(uniqid(0));

		//add module in shared objects list
		$DB->execute($scrsendtofriend_shareModule,$DB->quote($chk),$DB->escape($id),$DB->quote($var));

		$link=__LOCALFOLDER."portal/addtoapplication.php?id=".$id."&chk=".$chk;
		$subj=$_SESSION['longname'].lg("invitesYouOn").__APPNAME;
		$txt=lg("invitesYouOnBody",$_SESSION['username']).lg("invitesYouOnBody2",$link).__APPNAME.".";
	}
	elseif ($_POST["obj"]=="p")
	{ // if shared object is a portal
		if (!isset($_POST["prof"]) || !isset($_POST["nbcol"]) || !isset($_POST["style"]) || !isset($_POST["mode"])) exit();
		$prof=$_POST["prof"];
		$nbcol=$_POST["nbcol"];
		$style=$_POST["style"];
		$mode=$_POST["mode"];
		$label=$_POST["label"];

		//add portal to shared objects list
		$DB->execute($scrsendtofriend_sendPortalEmail,$DB->noHTML($label),$DB->escape($_SESSION['user_id']),$DB->escape($nbcol),$DB->escape($style),$DB->escape($mode));
		$portalid=$DB->getId();
		
	//	//if public shared
	//	//$check=md5($portalid.$_SESSION['username']);
	//	if (isset($_POST["portname"])){
//	//		$ret.=lg("proposedSharing")."<br />";
	//		$check="";
	//		
	//		$DB->execute($scrsendtofriend_sharePublicPortal,$DB->quote($_POST["portname"]),$DB->quote($_POST["portdesc"]),$check,$portalid);
	//		
	//		//add keywords
	//		if ($_POST["kw"]!=""){
	//			$keyword=explode(",",$_POST["kw"]);
	//			for ($i=0;$i<count($keyword);$i++){
	//				$selkw=$keyword[$i];
	//				$DB->getResults($scrsendtofriend_getKeyword,$selkw);
	//				if ($DB->nbResults()==0){
	//					$DB->execute($scrsendtofriend_addNewKeyword,$selkw);
	//					$kwid=$DB->getId();
	//				} else {
	//					$row = $DB->fetch(0);
	//					$kwid=$row["id"];
	//				}
	//				$DB->freeResults();
	
	//				$DB->execute($scrsendtofriend_insertKeyword,$portalid,$kwid);
	//			}
	//		}
	//	} else {

		$check=md5(uniqid(0));
		$DB->execute($scrsendtofriend_sharePrivPortal,$DB->quote($check),$DB->escape($portalid));

	//	}

		//add widgets in shared objects list
		$DB->execute($scrsendtofriend_sharePortalModules,$DB->escape($portalid),$DB->escape($_SESSION['user_id']),$DB->escape($prof));

		//$link=__LOCALFOLDER."portal/index.php?i=".$_SESSION['user_id']."&p=".$prof."&n=".$_SESSION['username']."&s=".$style."&m=".$mode."&c=".$nbcol."&e=".$enc;
		$subj=$_SESSION['longname'].lg("invitesYouOn").__APPNAME;
		$txt=lg("invitesYouOnPortalBody",$_SESSION['username']).lg("invitesYouOnPortalBody2",__LOCALFOLDER."/portal/addportaltoapplication.php?id=".$portalid."&chk=".$check).__APPNAME.".";
	}
	else
	{ // if shared object is an article
		if (!isset($_POST["title"])) exit();
		if (!isset($_POST["link"])) exit();
		$subj=$_SESSION['longname'].lg("invitesYouOnArticle").__APPNAME;
		$txt=lg("hello").",\r\n\r\n ".$_SESSION['username'].lg("invitesYouOnArticleBody",$_POST["title"]).lg("invitesYouOnArticleBody2",$_POST["link"]).__APPNAME.".";
//		$msg="<A href='".$_POST["link"]."' target=_blank>".$_POST["title"]."</A> (".lg("sentBy").$_SESSION['username'].")";
	}
}

$inc=0;
$emailsent=false;
//$msgsent=false;
while (isset($_POST["em".$inc]))
{
//	$DB->getResults("SELECT id FROM users WHERE username='".$_POST["em".$inc]."' AND typ='I' ");
//	if ($DB->nbResults()==0){
		// if no portaneo user with this email address, send the link by email
		mail($_POST["em".$inc],utf8_decode($subj),utf8_decode($txt),"From: ".__FRIENDEMAIL." \r\n");
		if (!$emailsent) $ret.=lg("emailSent");
		$emailsent=true;
//	} else {
//		// add message in the friends list
//		$row=$DB->fetch(0);
//		$uid=$row["id"];
//		$DB->execute('INSERT INTO users_messages (user_id,title,description,date,status,sender_id,linked_message,folder_id) VALUES ('.$uid.',"'.$msg.'","",CURRENT_DATE,"U",'.$_SESSION['user_id'].',0,0) ');
//		if (!$msgsent) $ret.=lg("messageSent");
//		$msgsent=true;
//	}
//	$DB->freeResults();
	$inc++;
}

$DB->close();

$file->status(1);

$file->footer();
?>