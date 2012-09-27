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
/* UTF8 encoding : é ù à ô */
set_time_limit(300);
if (!isset($_POST["server"])) exit();
if (!isset($_POST["login"])) exit();
if (!isset($_POST["pass"])) exit();
if (!isset($_POST["db"])) exit();
if (!isset($_POST["dbtype"])) exit();
$dbtype=$_POST["dbtype"];
$server=trim($_POST["server"]);
$login=trim($_POST["login"]);
$pass=trim($_POST["pass"]);
$db=trim($_POST["db"]);
$collab_suite = isset($_POST['collab_suite']) ? $_POST['collab_suite'] : '';

//error_log("collab suite :! $collab_suite -> " . $_POST['collab_suite']);
//exit;

define("__DBTYPE",$dbtype);
define("__SERVER",$server);
define("__LOGIN",$login);
define("__PASS",$pass);
define("__DB",$db);

$not_access=0;
require_once('includes.php');
require_once('../includes/file.inc.php');
require_once('functions.inc.php');



if ($collab_suite == "on") {
   // require_once('confinstall_collabsuite.inc.php');
    //add these to items to confinstall faile
    //define("I_APPLICATION_ID",2);
    //define("I_ADDITIONAL_SCRIPT","createdbpee.sql");
    copy( 'confinstall_collabsuite.inc.php','confinstall.inc.php' );
}
    require_once('confinstall.inc.php');


$installtype=__INSTALLTYPE;
$errors=false;

//server connection
if (!$DB->id)
{
	header("location:step2.php?err=1&installtype=".$installtype."");
	exit();
}

//database creation
$selectdb=$DB->selectDB($db);

if (!$selectdb){
	$createdb=$DB->execute($install_createDB,$db);
	if (!$createdb)
	{
		header("location:step2.php?err=2&installtype=".$installtype."");
		exit();
	}
	$DB->selectDB($db);
}

//check if objects are already existing
if (__INSTALLTYPE==2){
	$DB->getResults($install_listExistingTables);
	
	if ($DB->nbResults()>0 AND !isset($_POST["reprev"]))
	{
		header("location:step2.php?err=3&installtype=".$installtype."");
		exit();
	}
	$DB->freeResults();
}

$currversion="0.0.0";
$installingversion="0.0.0";
//correct bug on v1.0.1
$DB->execute($install_bugcorrection101);
// if posh upgrade, get the old version
if (__INSTALLTYPE==1){
	$DB->getResults($install_getPreviousVersion);

	if ($DB->nbResults()>0){
		$row=$DB->fetch(0);
		$currversion=$row["value"];
	} else {
		$currversion="1.0.0";
	}

	$DB->freeResults();
}

//get the sql commands for installed versions
$SQL=Array();

for ( $inc = 0 ; $inc < I_MAX_SCRIPT ; $inc++ ) //added due to file sorting issues
{

$handle=opendir("../install/");

	while ($file = readdir($handle))
	{
		if (substr($file,0,10)=='createdb_'.$inc && substr($file,-4)=='.sql' && !(strpos($file,$dbtype)===false))
		{

			$sqlfile=new file($file);
			$sqlcontent=$sqlfile->read();
			$sqlcontent = preg_replace('/\r\n|\r/', "\n", $sqlcontent); //win, mac newlines to nix.
	
			$sql=explode(";\n",$sqlcontent);

			$treat=true;
			$scriptversion='0.0.0';
			$SQL[$scriptversion]=Array();
			
			for ($i=0;$i<count($sql);$i++)
			{
				$strsql=trim($sql[$i]);
				if (!empty($strsql) && $strsql[0] == ">")
				{
					// check that db upgrade section needs to be apply
					$scriptversion = trim(substr($strsql,1));

					if (!array_key_exists($scriptversion,$SQL))
						$SQL[$scriptversion]=Array();
					if (__INSTALLTYPE==1)
					{
						if (version_compare($currversion, $scriptversion, ">="))
						{
							$treat=false;
						}
						else
						{
							$treat=true;
						}
					}
					if (version_compare($scriptversion, $installingversion, ">"))
						$installingversion=$scriptversion;
				}
				else if (!Empty($strsql) AND $treat)
				{
					array_push($SQL[$scriptversion],$strsql);
				}
			}
		}
	}
}
closedir($handle);

//treat the sql commands
ksort($SQL);

while (list($key, $val) = each($SQL))
{
	while (list($key2,$val2) = each($SQL[$key]))
	{
		if (!$DB->execute($val2))
		{
			echo "<div style='border:1px solid #c6c3c6;background-color:#efefef;font-size:9pt;'><b>Error :</b> ".$DB->showError()."<br />On following SQL command : ".$val2."</div><br />";
			$errors=true;
		}
	}
}

//additional objects creation
//if (I_ADDITIONAL_SCRIPT!=""){
//	$sqlfile=new file(I_ADDITIONAL_SCRIPT);
//	$sqlcontent=$sqlfile->read();
//	$sql=explode(";",$sqlcontent);
//	$treat=true;
//	for ($i=0;$i<count($sql);$i++){
//		$strsql=trim($sql[$i]);
//		if ($strsql!="" AND $strsql{0}==">"){
//			if (__INSTALLTYPE==1){
//				// check that db upgrade section needs to be apply
//				if (version_compare($currversion, substr($strsql,1), ">=")) {$treat=false;} else {$treat=true;}
//			}
//		} else if (!Empty($strsql) AND $treat){
//			if (!mysql_query($strsql,$conn)){
//				echo "error on following SQL command: <b>".$strsql."</b> (".mysql_error().")<br />";
//				$errors=true;
//			}
//		}
//	}
//}

//save configuration to db
if (!$DB->execute($install_setServer,$DB->quote($server)))
{
	header("location:step2.php?err=4&installtype=".$installtype."");
	exit();
}
if (!$DB->execute($install_setLogin,$DB->quote($login)))
{
	header("location:step2.php?err=4&installtype=".$installtype."");
	exit();
}
if (!$DB->execute($install_setPass,$DB->quote($pass)))
{
	header("location:step2.php?err=4&installtype=".$installtype."");
	exit();
}
if (!$DB->execute($install_setDB,$DB->quote($db)))
{
	header("location:step2.php?err=4&installtype=".$installtype."");
	exit();
}

$DB->execute($install_updDBType,$DB->quote($dbtype));
if ($DB->nbAffected()<1)
{
	$DB->execute($install_insertDBType,$DB->quote($dbtype));
}

//add in adm_config the old version
if (!$DB->execute($install_setPoshVersion,$DB->quote($currversion)))
{
	header("location:step2.php?err=4&installtype=".$installtype."");
	exit();
}

// add in adm_config the new version
if (version_compare(I_POSH_INSTALLING_VERSION, $installingversion, ">"))
	$installingversion=I_POSH_INSTALLING_VERSION;  //avoid errors when posh installing version is not correct in conf files
	
$DB->execute($install_updInstallingPoshVersion,$DB->quote($installingversion),"'INSTALLINGPOSHVE%'");
if ($DB->nbAffected()<1)
{
	$DB->execute($install_insertInstallingPoshVersion,$DB->quote($installingversion));
}

$DB->execute($install_updInstallType,$DB->quote(__INSTALLTYPE));
if ($DB->nbAffected()<1)
{
	$DB->execute($install_insertInstallType,$DB->quote(__INSTALLTYPE));
}

$DB->execute($install_setStep3);
if ($DB->nbAffected()<1)
{
	$DB->execute($install_insertStep3);
}
//for enterprise solution, update version
if (I_APPLICATION_ID==2)
{
	$DB->execute($install_updateEnterpriseVersion,$DB->quote(I_ENTERPRISE_INSTALLING_VERSION));
}

//generate config file
generateConfigFile(false,$server,$login,$pass,$db);

// update widget icon into the database
$dataIcon = array();
$i = 0;
$dirImg = "../modules/pictures/";
$DB->getResults($install_getWidgetIdWithoutIcon);
while($row=$DB->fetch(0))
{
	$dataIcon[$i]['id'] = $row["id"];
	if( file_exists($dirImg."box0_".$row["id"]) ) {
		$dataIcon[$i]['icon'] = $dirImg."box0_".$row["id"];
	} elseif( file_exists($dirImg."box0_".$row["id"].".ico") ) {
		$dataIcon[$i]['icon'] = $dirImg."box0_".$row["id"].".ico";
	} elseif( file_exists($dirImg."box0_".$row["id"].".gif") ) {
		$dataIcon[$i]['icon'] = $dirImg."box0_".$row["id"].".gif";
	} elseif( file_exists($dirImg."box0_".$row["id"].".bmp") ) {
		$dataIcon[$i]['icon'] = $dirImg."box0_".$row["id"].".bmp";
	} elseif( file_exists($dirImg."box0_".$row["id"].".jpg") ) {
		$dataIcon[$i]['icon'] = $dirImg."box0_".$row["id"].".jpg";
	}
	// Get image type
	$data = @getImageSize( $dataIcon[$i]['icon'] );
	$extensionlogo = ".ico";
		
	switch( $data[2] )
	{
		case 1 : $extensionlogo = ".gif"; break;
		case 2 : $extensionlogo = ".jpg"; break;
		case 3 : $extensionlogo = ".png"; break;
		case 4 : $extensionlogo = ".swf"; break;
		case 5 : $extensionlogo = ".psd"; break;
		case 6 : $extensionlogo = ".bmp"; break;
		default : break;
	}
	$w_icon = $dirImg."box0_".$row["id"].$extensionlogo;
	copy($dataIcon[$i]['icon'],$w_icon);
	$dataIcon[$i]['icon'] = $w_icon;
	$i++;
}
$DB->freeResults();
for($i=0;$i<sizeof($dataIcon);$i++) {
	$DB->execute($install_updateWidgetIcon,$DB->quote($dataIcon[$i]['icon']),$DB->escape($dataIcon[$i]['id']));
}
$dataIcon = array();
$DB->getResults($install_getWidgetIdFromTempDirItem);
$i = 0;
while($row=$DB->fetch(0))
{
	$extensionlogo = ".ico";
	$w_icon = $dirImg."rss".$row["id"].$extensionlogo;
	$dataIcon[$i]['id'] = $row["id"];
	// Get image type
	$data = @getImageSize( $w_icon );
	switch( $data[2] )
	{
		case 1 : $extensionlogo = ".gif"; break;
		case 2 : $extensionlogo = ".jpg"; break;
		case 3 : $extensionlogo = ".png"; break;
		case 4 : $extensionlogo = ".swf"; break;
		case 5 : $extensionlogo = ".psd"; break;
		case 6 : $extensionlogo = ".bmp"; break;
		default : break;
	}
	if( !file_exists($w_icon) ) {
		if( file_exists("../modules/quarantine/icon".$row["id"].$extensionlogo) ) {
			$w_icon = "../modules/quarantine/icon".$row["id"].$extensionlogo;
		}elseif( file_exists("../modules/pictures/rss".$row["id"]."ico") ) {
			$w_icon = "../modules/pictures/rss".$row["id"].$extensionlogo;
		}elseif( file_exists("../modules/pictures/rss".$row["id"]) ) {
			$w_icon = "../modules/pictures/rss".$row["id"].$extensionlogo;
		}
	}
	$dataIcon[$i]['icon'] = $w_icon;
	$i++;
}
$DB->freeResults();
for($i=0;$i<sizeof($dataIcon);$i++) {
	$DB->execute($install_updateWidgetIconTempDirItem,$DB->quote($dataIcon[$i]['icon']),$DB->escape($dataIcon[$i]['id']));
}


if ($errors)
{
	echo "<br /><a href='step2.php'>Restart</a> OR <a href='step3.php'>Continue with next step (potential information missing)</a>";
	$DB->close();
}
else
{
	// change the directory of pictures of users
	if( !is_dir("../upload/profile/") ) {
			mkdir("../upload/profile/","0600");
	}
	// if posh upgrade, get the old version
	if (__INSTALLTYPE==1){
		$DB->getResults($install_getUsersInfo);
		$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$pkey="";
		$i = 0;
        $data = array();
		while($row=$DB->fetch(0))
		{
			$data[$i]["id"] = $row["id"];
			$data[$i]["picture"] = substr($row["picture"], 0, ( strpos($row["picture"],"jpg") + 3 ));
			$data[$i]["file_loc"] = "../upload/profile/".md5($row['longname'].$data[$i]["id"].time());
			$i++;
		}
		$DB->freeResults();
		for($i=0;$i<sizeof($data);$i++) {				
			if( copy($data[$i]["picture"], $data[$i]["file_loc"]) ) {
				//generate a private key used in application
				srand((double)microtime()*1000000);
				for($j=0;$j<10;$j++) $pkey.= $str[rand()%62]; 
				$DB->getResults($install_setUsersPictureUrl, $DB->quote($data[$i]["file_loc"]."?rand=".$pkey), $data[$i]["id"]);
				$DB->freeResults();
				unlink($data[$i]["picture"]);
			}
		}
	}
	
	$DB->close();
    header("location:step3.php?lang=".__LANG);
	exit();
	
}
?>