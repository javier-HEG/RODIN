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
# POSH Module management - Validate all modules
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_module_validateall.php";

//includes
require_once("includes.php");
require_once('../includes/refreshcache.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');
require_once('../includes/misc.inc.php');
require_once('../includes/mail.inc.php');
require_once('../includes/xml.inc.php');
require_once('../includes/admin_tools.php');

launch_hook('admin_scr_module_validateall');

//loop on each module to validate
if($_SESSION['user_id']>1) {
    $DB->getResults($scr_modulevalidateall_getAllowedItems,$DB->escape($_SESSION['user_id']));
}
else {
    $DB->getResults($scr_modulevalidateall_getItems);
}

while ($row = $DB->fetch(0)) {

	$id=$row["id"];
	$url=$row["url"];
	$name=$row["name"];
	$desc=$row["description"];
	$size=$row["height"];
	$minwidth=$row["minwidth"];
	$website=$row["website"];
	$catid=$row["category_id"];
	$typ=$row["typ"];
	$format=$row["format"];
	$var=$row["defvar"];
	$lang=$row["lang"];
	$keywords=$row["keywords"];
	$username=$row["username"];
	$logo=$row["logo"];
	$views=$row["views"];
    $l10n=$row["l10n"];
	$sizable=$row["sizable"];
	$moduleIdValidated = $row["id_dir_item"];
	
	if($moduleIdValidated=="" || $moduleIdValidated==0 ) {
		// validate module information
		$DB2->execute($module_validateModule,
							$DB2->quote($url),
							$DB2->quote($name),
							$DB2->quote($desc),
							$DB2->escape($size),
							$DB2->escape($minwidth),
							$DB2->quote($website),
							$DB2->escape($id)
					);
		$newid = $DB2->getId();
	} else {
		$DB2->execute($module_updateModule,$DB2->quote($url),$DB2->quote($name),
							$DB2->quote($desc),
							$DB2->quote($typ),
							$DB2->quote("O"),
							$DB2->escape($size),
							$DB2->escape($minwidth),
							$DB2->escape($sizable),
							$DB2->quote($website),
							$DB2->quote($views),
							$DB2->escape($moduleIdValidated)
					);
		$DB2->execute($module_removeTempModule,$DB2->escape($id));
		$DB2->execute($modules_deleteDirItemExternal,$DB2->escape($moduleIdValidated));
		$DB2->execute($module_addDirItemExternal,$DB2->escape($moduleIdValidated),$DB2->escape($id));
		$DB2->execute($modules_deleteTempDirItemExternal,$DB2->escape($id));
        //copyFromTempExternalLanguage($moduleIdValidated,$id);

		$url = preg_replace(
						'/pitem=\d+/xmsi',
						"pitem=$moduleIdValidated",
						$url
							);
		$DB2->execute($module_setNewUrl,$DB2->quote($url),$DB2->escape($moduleIdValidated));
		$newid = $moduleIdValidated;	
	}
	$extensionlogo = strrchr($logo,'.');
	if( substr($logo,0,1)=="_" ) {
		$newlogo = "../modules/pictures/".$logo;
	} else {
		$newlogo = "../modules/pictures/box0_".$newid.$extensionlogo;
	}
	
	//copy the files
	if( @copy($logo,$newlogo) )
		unlink($logo);
	// update icon in dir_item
	$DB2->execute($dir_item_setIcon,$DB2->quote($newlogo),$DB2->escape($newid));
	// set id icon in dir_rss
	$DB2->execute($dir_rss_setIconId,$DB2->quote($newlogo),$DB2->quote($logo));
	if ($DB2->nbAffected()>0)    {
        $DB2->execute($module_removeTempModule,$DB2->escape($id));
    }
	
	if ($format!="R" && ( $moduleIdValidated=="" || $moduleIdValidated==0 ) ) {
		$DB2->execute($module_addDirItemExternal,$DB2->escape($newid),$DB2->escape($id));
        $DB2->execute($modules_deleteTempDirItemExternal,$DB2->escape($id));
        $DB2->execute($modules_deleteTempDirItem,$DB2->escape($id));
    }
        
    if ( $format!="R" ) {  
        copyFromTempExternalLanguage($newid,$id);
        loadDatasToGenerateCacheFiles($newid,$format);
        if ($format!='M') {	
    		//move widget from quarantine to final folder
            if ( file_exists("../modules/tmp_module".$id."_param.xml" )) {
                copy("../modules/tmp_module".$id."_param.xml","../modules/module".$newid."_param.xml");
                @chmod("../modules/module".$newid."_param.xml", 0766);
                copyParamL10nFiles($id,$newid);
            }
        }
        if ($format!="U") {
			$newUrl="../modules/module".$newid.".php";
			if ( file_exists("../modules/tmp_module".$id.".php" )) {
	            copy("../modules/tmp_module".$id.".php",$newUrl);
	        }
			
			@chmod($newUrl, 0755);
	        $url = preg_replace(
	                    '/pitem=\d+/xmsi',
	                    "pitem=$newid",
	                    $url
	                        );
			$DB2->execute($module_setNewUrl,$DB2->quote($url),$DB2->escape($newid));
		}
	}

	// add directory information
	$DB2->execute($module_addDirectoryTempModule,$DB2->escape($newid),$DB2->escape($id));

	if ($catid>0) {
		$DB2->execute($module_addModuleSubDirectory,$DB2->escape($newid),$DB2->escape($catid),$DB2->escape($id));
		if ($DB2->nbAffected()>0) {
			$DB2->execute($module_removeTempDirectory,$DB2->escape($id));
		}
	}

	//if module linked with rss feed, set the link
	if ($typ=="R")
	{
		$fid=0;
		$parameter=explode("&",$var);
		for ($i=0;$i<count($parameter)-1;$i++)
		{
			$pair=explode("=",trim($parameter[$i]));
			if ($pair[0]=="fid") $fid=$pair[1];
		}
		if ($fid!=0)
		{
			$DB2->execute($module_addRedactorFeed,$DB2->escape($newid),$DB2->escape($fid));
		}
	}

	//add the new keywords
	$keysarr=explode(",",$keywords);
	for ($i=0;$i<count($keysarr);$i++)
	{
		$kw=$keysarr[$i];
		$kwsimplified=trim(suppress_accent($kw));
		$kwsimplified=strtolower($kwsimplified);
		if (!Empty($kw)) {
			$DB2->getResults($module_getValidationKeyword,$DB2->quote($kwsimplified));
			if ($DB2->nbResults()==0) {
				$DB2->execute($module_addValidationKeyword,$DB2->quote($kw),$DB2->quote($kwsimplified));
				$kwid=$DB2->getId();
			}
			else {
				$row2 = $DB2->fetch(0);
				$kwid=$row2["id"];
			}
			$DB2->freeResults();
			$DB2->execute($module_addValidationModuleKeyword,$DB2->escape($kwid),$DB2->escape($newid),2);
		}
	}
  
	refresh_item($newid,$template_folder);
	refresh_directory($catid,$template_folder,$lang);
	
	//add unsubscribe to the message
	// get user md5
	$DB2->getResults($users_getMd5user,$DB2->quote($username));
	$row2 = $DB2->fetch(0);
	$unsubscribeLink = __LOCALFOLDER.'portal/login.php?id='.$row2['id'].'&md5='.$row2['md5user'];
	$unsubscribe = lg('accountUnsubscribe').lg('lblClickHere').' : '.$unsubscribeLink;
	$DB2->freeResults();	
	//tab with all the php values to include into the mail 
	$val = array($username, $name, __APPNAME, __LOCALFOLDER, $unsubscribe);
	//tab with all the pseudoCode tags
	$tab = array("%username", "%description", "%site", "%link", "%unsubscribe");
	//send a mail to thewidget'a author
	$lang=$_SESSION['lang'];
	$DB2->getResults($config_getNotification,$DB2->quote($lang),"validWidget");	
    while ($row2 = $DB2->fetch(0))
    {
        $notif_subject=stripslashes($row2["subject"]);
        $notif_message=stripslashes($row2["message"]);
        $notif_sender=$row2["sender"];
        $notif_copy=$row2["copy"];
    }
    $DB2->freeResults();
	$s_mail = new mail();
	$s_mail->addSender($notif_sender);
	$s_mail->addSubject($notif_subject,$val,$tab);
	$s_mail->addMessage($notif_message,$val,$tab);
	$s_mail->configArray($notif_copy,'1');
	$s_mail->configArray($username,'2');
	$s_mail->sendMail();
}

$file=new xmlFile();
$file->header("directory");
launch_hook('admin_scr_module_validateall_end');
//header("location:modules_mgmt.php");
$file->status(1);
$file->footer();
?>