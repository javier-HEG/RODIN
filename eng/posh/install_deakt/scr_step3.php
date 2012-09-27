<?php
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
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
$not_access=0;
require_once('confinstall.inc.php');
require_once('includes.php');
require_once('../includes/file.inc.php');
require_once('functions.inc.php');

$appname=trim($_POST["appname"]);
$DB->execute($install_setAppname,$DB->quote($appname));

$managegroups=(isset($_POST["managegroups"]))?"true":"false";
$DB->execute($install_setUseGroup,$DB->quote($managegroups));

$registerFeeds=(isset($_POST["registerfeeds"]))?$_POST["registerfeeds"]:"true";
$DB->execute($install_setRegisterFeeds,$DB->quote($registerFeeds));

$localfolder=trim($_POST["localfolder"]);
if (substr($localfolder,-1)!="/") $localfolder.="/";
// Bug : issue on some servers
//if (@fopen($localfolder."includes/config.js","r")==0){header("location:step3.php?err=1");exit();}
$DB->execute($install_setLocalFolder,$DB->quote($localfolder));

$usermodule=(isset($_POST["usermodule"]))?"I":"A";
$DB->execute($install_setUserModule,$DB->quote($usermodule));

$DB->execute($install_setUserModuleJs,$DB->quote($usermodule));

$DB->execute($install_setStep4);

$DB->execute($install_setMenuPosition,$DB->quote($_POST["menuposition"]));

$useproxy=(isset($_POST["useproxy"]))?"true":"false";
$DB->execute($install_setUseproxy,$DB->quote($useproxy));

//reset the default theme
if ($_POST["resettheme"]=="1")
{
	$DB->execute($install_setDefTheme);
	if (file_exists("../../app/exposh/styles/main1.css.php")) {
		unlink("../../app/exposh/styles/main1.css.php");
	}
	if (!copy("../styles/themes/classic_blue.thm", "../../app/exposh/styles/main1.css.php")) {
		echo "error in the copy of the theme. Check the access rights of the /styles folder and refresh this page!";
		exit();
	}
	if (file_exists("../styles/module1.css")) {
		unlink("../styles/module1.css");
	}
	if (!copy("../styles/themes/module.thw", "../styles/module1.css")) {
		echo "error in the copy of the theme. Check the access rights of the /styles folder and refresh this page!";
		exit();
	}
}

$poshversion=(defined('__POSHVERSION')?__POSHVERSION:"1.0.0");

//v1.3 - modules icons are no more .gif file, but .ico files
if (version_compare($poshversion, "1.3.2", "<=")){
	require('patches/1.3.2_gif_to_icons.php');
}
if (version_compare($poshversion, "1.4.0", "<=")){
	require('patches/1.4.0_add_feed_id_in_variables.php');
}
if (version_compare($poshversion, "1.5.2", "<=")){
	require('patches/1.5.2_change_widget_file_name.php');
}
if (version_compare($poshversion, "2.0.0", "<=")){
	require('patches/2.0.0_add_feed_id_in_module_table.php');
}
if (version_compare($poshversion, "2.0.0", "<=") && I_APPLICATION_ID==2){
	require('patches/2.0.0_user_keywords_summarized_in_user_table.php');
}
if (version_compare($poshversion, "2.1.0", "<=")){
	require('patches/2.1.0_remove_ico_ext.php');
}

// private key can not be empty 
$key=getParam($DB,"KEY","");
if ($key=="")
{
	//generate a private key used in application
	$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$pkey="";
	srand((double)microtime()*1000000);
	for($i=0;$i<10;$i++) $pkey.= $str[rand()%62]; 

	$DB->execute($install_setKey,$DB->quote($pkey));
    
    $md5key = md5($pkey);
	$DB->execute($install_setmd5Key,$DB->quote($md5key));
}


//for new installation of enterprise version, activate plugin
if (I_APPLICATION_ID==2 && __INSTALLTYPE==2)
{
	$DB->execute($install_activateEnterprisePlugin);
}

//generate config file
generateConfigFile(false,"","","","");

$DB->close();

header("location:step4.php");
?>