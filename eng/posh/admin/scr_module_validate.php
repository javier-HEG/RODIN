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
# POSH Module management - Validate module
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
#
# inputs
#	username(POST) : user mail adress for notification
#	itemid (POST) : module temp id
#	catid (POST) : module directory id
#	url (POST)
#	size (POST) : module height
#	minwidth(POST) : module minimal width
#	desc (POST) : module description
#	website(POST) : module linked website
#	name (POST) : module name
#	kwx(POST) : keywords label
#	wx(POST) : keywords weight
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_module_validate.php";
//includes
require_once("includes.php");
require_once('../includes/refreshcache.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');
require_once('../includes/misc.inc.php');
require_once('../includes/mail.inc.php');
require_once('../includes/xml.inc.php');
require_once('../includes/file.inc.php');
require_once('../includes/admin_tools.php');

launch_hook('admin_scr_module_validate');

$id = $_POST["itemid"];
$catid = $_POST["catid"];

$DB->getResults($module_getTempModuleToValidate,$DB->escape($id));
$row = $DB->fetch(0);
$url = $_POST["url"];
$username = $_POST['username'];
$size = $_POST["size"];
$minwidth = $_POST["minwidth"];
$sizable = $row["sizable"];
$logo = $row["logo"];
$desc = $_POST["desc"];
$website = $_POST["website"];
//format for Google search
$website = str_replace("http://www.","",$website);
$website = str_replace("http://fr.","",$website);
$website = str_replace("http://","",$website);
$keys = $row["keyword"];
$format = $row["format"];
$typ = $row["typ"];
$var = $row["defvar"];
$editor = $row["edemail"];
$lang = $row["lang"];
$name = $_POST["name"];
$views = $_POST["views"];
$l10n = $row["l10n"];
$moduleIdValidated = $row["id_dir_item"];
$DB->freeResults();	

//url preparation to be shown in personal portals with variables
if ( strpos($url, "?")===false) {
	$url.="?";
}
else {
    if ( strpos($url, "&")===false ) {
        $url.="&";
    }
}

//add unsubscribe to the message
// get user md5
$DB->getResults($users_getMd5user,$DB->quote($username));
$row = $DB->fetch(0);
$unsubscribeLink = __LOCALFOLDER.'portal/login.php?id='.$row['id'].'&md5='.$row['md5user'];
$unsubscribe = lg('accountUnsubscribe').lg('lblClickHere').' : '.$unsubscribeLink;
//tab with all the php values to include into the mail 
$val = array($username, $name, __APPNAME, __LOCALFOLDER, $unsubscribe);
//tab with all the pseudoCode tags
$tab = array("%username", "%description", "%site", "%link","%unsubscribe");

$DB->freeResults();	

$defvar = ($format=="R") ? "nb=5" : "";
if($moduleIdValidated=="" || $moduleIdValidated==0 ) {
	$DB->execute($module_validateModule,
                    $DB->quote($url),
                    $DB->quote($name),
	                $DB->quote($desc),
					$DB->escape($size),
					$DB->escape($minwidth),
	                $DB->quote($website),
					$DB->escape($id)
	                );
	$newid = $DB->getId();
} else {
	$DB->execute($module_updateModule,$DB->quote($url),
                        $DB->quote($name),
						$DB->quote($desc),
						$DB->quote($typ),
						$DB->quote("O"),
						$DB->escape($size),
						$DB->escape($minwidth),
						$DB->escape($sizable),
						$DB->quote($website),
						$DB->quote($views),
						$DB->escape($moduleIdValidated)
				);
	$DB->execute($module_removeTempModule,$DB->escape($id));
	$DB->execute($modules_deleteDirItemExternal,$DB->escape($moduleIdValidated));
	$DB->execute($module_addDirItemExternal,$DB->escape($moduleIdValidated),$DB->escape($id));
	$DB->execute($modules_deleteTempDirItemExternal,$DB->escape($id));
    //copyFromTempExternalLanguage($moduleIdValidated,$id);
 
	$url = preg_replace(
                    '/pitem=\d+/xmsi',
                    "pitem=$moduleIdValidated",
                    $url
                        );
	$DB->execute($module_setNewUrl,$DB->quote($url),$moduleIdValidated);
    
	$newid = $moduleIdValidated;
}

$extensionlogo = strrchr($logo,'.');
if( substr($logo,0,1)=="_" ) {
	$newlogo = "../modules/pictures/".$logo;
} else {
	$newlogo = "../modules/pictures/box0_".$newid.$extensionlogo;
}

if( @copy($logo,$newlogo) )
	unlink($logo);
// update icon in dir_item
$DB->execute($dir_item_setIcon,$DB->quote($newlogo),$DB->escape($newid));
// set id icon in dir_rss
$DB->execute($dir_rss_setIconId,$DB->quote($newlogo),$DB->quote($logo));

// remove widget from temp table
if ($DB->nbAffected()>0) {
	$DB->execute($module_removeTempModule,$DB->escape($id));
}

if ( $format!="R" && ( $moduleIdValidated=="" || $moduleIdValidated==0 ) ) {
    
    $DB->execute($module_addDirItemExternal,$DB->escape($newid),$DB->escape($id));
    $DB->execute($modules_deleteTempDirItemExternal,$DB->escape($id));
    $DB2->execute($modules_deleteTempDirItem,$DB2->escape($id));
 
}

if ( $format!="R" ) {  
    copyFromTempExternalLanguage($newid,$id);
    loadDatasToGenerateCacheFiles($newid,$format);
    
	//move widget from quarantine to final folder
    
    //achanger en  !="U" vu que ça concerne tous les format sauf U
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
		$DB->execute($module_setNewUrl,$DB->quote($url),$newid);

        //update dir_external_module
        $url_source = preg_replace(
                    '/getsource=1/xmsi',
                    "getparam=1",
                    $url
                        );
        $DB->execute($module_updateSource,
                        $newid,
                        $DB->quote($url_source),
                        $DB->quote('validated'),
                        $id
                    );
        //if file doesn't exist create file from db source
        
        //cette partie peut sauter , est créé juste au dessus
        $DB->getResults($module_getSource,$id);
        if ($DB->nbResults()>0) {
            $row = $DB->fetch(0);
 
            $xml_source= $row['xmlmodule'];
            //../modules/module"+tab[v_tab].module[v_id].id+"_param.xml
            if ( !file_exists($newUrl) ) {
                $paramfile=new file("../modules/module".$newid."_param.xml");
                $paramfile->write($xml_source);
                @chmod("../modules/module".$newid."_param.xml", 0766);
                //createParamL10nFiles($id,$newid);
            }
        }
        $DB->freeResults();
	}
}

// define directory for the widget
$DB->execute($module_addDirectoryTempModule,$newid,$DB->escape($id));

if ($catid > 0) {
	$DB->execute($module_addModuleSubDirectory,$newid,$DB->escape($catid),$DB->escape($id));
	if ($DB->nbAffected()>0) {
		$DB->execute($module_removeTempDirectory,$DB->escape($id));
	}
}

//if module linked with rss feed, set the link
if ($typ=="R"){
	$fid=0;
	$parameter=explode("&",$var);
	for ($i=0;$i<count($parameter)-1;$i++)
	{
		$pair=explode("=",trim($parameter[$i]));
		if ($pair[0]=="fid") $fid=$pair[1];
	}
	if ($fid!=0) {
		$DB->execute($module_addRedactorFeed,$DB->escape($newid),$DB->escape($fid));
	}
}

//add the new keywords
for ($i=0;$i<40;$i++){
	if (!empty($_POST["kw".$i])) {
		$kw=$_POST["kw".$i];
		$kwsimplified=trim(suppress_accent($kw));
		$kwsimplified=strtolower($kwsimplified);

		$DB->getResults($module_getValidationKeyword,$DB->quote($kwsimplified));
		if ($DB->nbResults()==0) {
			$DB->execute($module_addValidationKeyword,$DB->quote($kw),$DB->quote($kwsimplified));
			$kwid=$DB->getId();
		}
		else {
			$row = $DB->fetch(0);
			$kwid=$row["id"];
		}
		$DB->freeResults();
		$DB->execute($module_addValidationModuleKeyword,$DB->escape($kwid),$DB->escape($newid),$DB->escape($_POST["w".$i]));
	}
}

//notify the user that his widget id approved
$lang=$_SESSION['lang'];
$DB->getResults($config_getNotification,$DB->quote($lang),'validWidget');
    
while ($row = $DB->fetch(0))
{
    $notif_subject=stripslashes($row["subject"]);
    $notif_message=stripslashes($row["message"]);
    $notif_sender=$row["sender"];
    $notif_copy=$row["copy"];
}
$DB->freeResults();

$s_mail = new mail();
$s_mail->addSender($notif_sender);
$s_mail->addSubject($notif_subject,$val,$tab);
$s_mail->addMessage($notif_message,$val,$tab);
$s_mail->configArray($notif_copy,'1');
$s_mail->configArray($username,'2');
$s_mail->sendMail();

refresh_item($newid,$template_folder);
refresh_directory($catid,$template_folder,$lang);

$DB->freeResults();

launch_hook('admin_scr_module_validate_end');

$file=new xmlFile();
$file->header("modulevalidate");
$file->status(1);
$file->footer();

?>