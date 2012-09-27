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
# POSH Configuration - save general parameters 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_config_general.php";
//includes
require_once('includes.php');
global $DB;

require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("config");

launch_hook('admin_scr_config_general');


/*
queryManager($alias,$value,$type,$DB);
inputs:
	$alias : The alias of the query
	$value : the value to add
	$type  :
		1='quote'
		2='escape
	$DB : the object to acess the class's methods (connection_mysql.inc.php)
	
output:
	(int) number of lines affected by the query
*/

function queryManager($alias,$value,$type,$DB)
{
	switch ($type)
	{
		case 1:
		{
			$DB->execute($alias,$DB->quote($value));
			break;
		}
		
		case 2:
		{
			$DB->execute($alias,$DB->escape($value));
			break;
		}
	}
	return $DB->nbAffected();	
}
function adminSetParam($param,$value,$type,$destination)
{
	global $config_setParam,$config_insertParam,$DB;

	switch ($type)
	{
		case 'str':
		{
			$value=$DB->quote($value);
			break;
		}
		case 'int':
		{
			$value=$DB->quote($value);
			break;
		}
		case 'arr':
		{
			$value=$DB->quote($value);
			break;
		}
	}
	$DB->execute($config_setParam,$value,$DB->quote($param));
	if ($DB->nbAffected()==0)
	{
		$DB->execute($config_insertParam,$DB->quote($param),$value,$type,$destination);
	}
}
$captcha=isset($_POST["captcha"])?"true":"false";
adminSetParam('captcha',$captcha,'int','A');

adminSetParam('numberOfTry',$_POST["numberOfTry"],'str','P');

adminSetParam('connectionDateRange',$_POST["connectionDateRange"],'str','P');

adminSetParam('NOTIFICATIONEMAIL',$_POST["NOTIFICATIONEMAIL"],'str','P');

adminSetParam('maxModNb',$_POST["maxModNb"],'int','J');

adminSetParam('maxPageNb',$_POST["maxPageNb"],'int','J');
	
$addPagePermission=isset($_POST["addPagePermission"])?"true":"false";
adminSetParam('addPagePermission',$addPagePermission,'int','J');	
	
$footer=stripslashes ($_POST["footer"]);
adminSetParam('footer',$footer,'str','J');

adminSetParam('SERVER',$_POST["SERVER"],'str','P');

adminSetParam('LOGIN',$_POST["LOGIN"],'str','P');

if ( isset($_POST["PASS"]) && $_POST["PASS"]!="xxxxxxx")
{	
	adminSetParam('PASS',$_POST["PASS"],'str','P');
}

adminSetParam('DB',$_POST["DB"],'str','P');
	
adminSetParam('LOCALFOLDER',$_POST["LOCALFOLDER"],'str','P');

adminSetParam('SUPPORTEMAIL',$_POST["SUPPORTEMAIL"],'str','P');

adminSetParam('APPNAME',$_POST["APPNAME"],'str','P');

//adminSetParam('apname',$_POST["APPNAME"],'str','J');
	
$usegroup=isset($_POST["useGroup"])?"true":"false";
adminSetParam('useGroup',$usegroup,'str','A');

$usermodule=isset($_POST["USERMODULE"])?"I":"A";
adminSetParam('USERMODULE',$usermodule,'str','P');

adminSetParam('userModuleJs',$usermodule,'str','J');

$useconditions=isset($_POST["useConditions"])?"true":"false";
adminSetParam('useConditions',$useconditions,'str','J');

adminSetParam('showHomeBar',$_POST["showHomeBar"],'int','J');

$txtnote=str_replace("<","&lt;",$_POST["txtnote"]);
$txtnote=str_replace(">","&gt;",$txtnote);
$txtnote=str_replace("\r\n","<br>",$txtnote);
$txtnote=str_replace("&","&amp;",$txtnote);
$txtnote=str_replace("  ","&nbsp; ",$txtnote);


adminSetParam('txtnote',$txtnote,'str','J');

adminSetParam('menuposition',$_POST["menuposition"],'str','J');

adminSetParam('moduleAlignDefault',$_POST["moduleAlignDefault"],'int','J');
	
$rssrefminuts=$_POST["rssrefreshdelay"];
adminSetParam('rssrefreshdelay',$rssrefminuts,'int','J');

adminSetParam('defaultmode',$_POST["defaultmode"],'str','P');

$useoverview=isset($_POST["useoverview"])?"true":"false";
adminSetParam('useoverview',$useoverview,'int','J');

$displayrssdesc=isset($_POST["displayrssdesc"])?"true":"false";
adminSetParam('displayrssdesc',$displayrssdesc,'int','J');

$showicon=isset($_POST["showicon"])?"true":"false";
adminSetParam('showicon',$showicon,'int','J');

$debugmode=isset($_POST["debugmode"])?"true":"false";
adminSetParam('debugmode',$debugmode,'int','J');

if (defined('__useproxy') &&__useproxy && isset($_POST["proxypass"]) && $_POST["proxypass"]!="xxxxxxx")
{
	adminSetParam('PROXYSERVER',$_POST["PROXYSERVER"],'str','P');
	adminSetParam('PROXYPORT',$_POST["PROXYPORT"],'str','P');
	$proxyconnection=base64_encode ($_POST["proxyuser"].":".$_POST["proxypass"]);
	adminSetParam('PROXYCONNECTION',$proxyconnection,'str','P');	
	adminSetParam('proxypacfile',$_POST["proxypacfile"],'str','J');
}

adminSetParam('accountType',$_POST["accountType"],'str','A');

$blockedModulePreventPageRemoval=isset($_POST["blockedModulePreventPageRemoval"])?"true":"false";
adminSetParam('blockedModulePreventPageRemoval',$blockedModulePreventPageRemoval,'int','J');

adminSetParam('loadlatestpageonstart',$_POST["loadlatestpageonstart"],'int','J');

adminSetParam('menuDefaultStatus',$_POST["menuDefaultStatus"],'int','J');

$displayAllLanguageModules=isset($_POST["displayAllLanguageModules"])?"true":"false";
adminSetParam('displayAllLanguageModules',$displayAllLanguageModules,'int','J');

$showModuleRefresh=isset($_POST["showModuleRefresh"])?"true":"false";
adminSetParam('showModuleRefresh',$showModuleRefresh,'int','J');

$showModuleClose=isset($_POST["showModuleClose"])?"true":"false";
adminSetParam('showModuleClose',$showModuleClose,'int','J');

$showModuleConfigure=isset($_POST["showModuleConfigure"])?"true":"false";
adminSetParam('showModuleConfigure',$showModuleConfigure,'int','J');

$showModuleMinimize=isset($_POST["showModuleMinimize"])?"true":"false";
adminSetParam('showModuleMinimize',$showModuleMinimize,'int','J');

$showModuleTitle=isset($_POST["showModuleTitle"])?"true":"false";
adminSetParam('showModuleTitle',$showModuleTitle,'int','J');

$userChangePermission=isset($_POST["userChangePermission"])?"true":"false";
adminSetParam('userChangePermission',$userChangePermission,'int','J');

$passwordChangePermission=isset($_POST["passwordChangePermission"])?"true":"false";
adminSetParam('passwordChangePermission',$passwordChangePermission,'int','J');

$DB->execute($config_setDisplayrssdesc,$DB->quote($_POST["displayrssdesc"]));
if ($DB->nbAffected()==0)
{
	$DB->execute($config_insertDisplayrssdesc,$DB->quote($_POST["displayrssdesc"]));
}

$displayrsssource=isset($_POST["displayrsssource"])?"true":"false";
adminSetParam('displayrsssource',$displayrsssource,'str','J');


$DB->execute($config_setDisplayrssimages,$DB->quote($_POST["displayrssimages"]));
if ($DB->nbAffected()==0)
{
	$DB->execute($config_insertDisplayrssimages,$DB->quote($_POST["displayrssimages"]));
}

$file->status(1);

$file->footer();

//header("location:scr_config_generate_configfiles.php?redirect=config");
?>