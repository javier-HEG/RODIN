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
# POSH Module management - Apply module modifications
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_module_modify.php";
//includes
require_once("includes.php");
require_once('../includes/refreshcache.inc.php');
require_once('../includes/misc.inc.php');
require_once('../includes/xml.inc.php');

launch_hook('admin_scr_module_modify');

// Get the ID of the treated module
$itemid=isset($_POST["itemid"])?$_POST["itemid"]:-1;
$catid=isset($_POST["catid"])?$_POST["catid"]:-1;
$oldcatid=isset($_POST["oldcatid"])?$_POST["oldcatid"]:-1;

$icon=isset($_POST["icon"])?$_POST["icon"]:"";

// Save module changes
$minwidth=isset($_POST["minwidth"])?$_POST["minwidth"]:0;
$sizable=1;
if ($minwidth==400) $sizable=0;
$url=isset($_POST["url"])?$_POST["url"]:"";
if (strpos($url,"?")===false) {
	$url.="?";
}
//else
//{
//	$url.="&";
//}
$name = isset($_POST["name"])?$_POST["name"]:"";
$desc = isset($_POST["desc"])?$_POST["desc"]:"";
$typ = isset($_POST["typ"])?$_POST["typ"]:"";
$status = isset($_POST["status"])?$_POST["status"]:"";
$size = isset($_POST["size"])?$_POST["size"]:"";
$website = isset($_POST["website"])?$_POST["website"]:"";
$views = isset($v["views"])?$_POST["views"]:"";

if(0) //FRI
{
print "<br>FRI: itemid=$itemid";
print "<br>FRI: catid=$catid";
print "<br>FRI: oldcatid=$oldcatid";
print "<br>FRI: icon=$icon";
print "<br>FRI: minwidth=$minwidth";
print "<br>FRI: name=$name";
print "<br>FRI: desc=$desc";
print "<br>FRI: typ=$typ";
print "<br>FRI: size=$size";
print "<br>FRI: url=$url";
print "<br>FRI: website=$website";
print "<br>FRI: views=$views";
}


$DB->execute($module_updateModule,$DB->quote($url),$DB->quote($name),
								$DB->quote($desc),
								$DB->quote($typ),
								$DB->quote($status),
								$DB->escape($size),
								$DB->escape($minwidth),
								$DB->escape($sizable),
								$DB->quote($website),
								$DB->quote($views ),
								$DB->escape($itemid)
							);

if ( $status=="S" ) {
    $DB->execute($module_deleteModuleCategory,$DB->escape($itemid),$DB->escape($catid));
}
else {
    if ( $catid!=0 ) {
    	//check if the category mapping is already existing
    	$DB->getResults($module_getModuleDirectory,$DB->escape($itemid));
    	if ($DB->nbResults()==0)    {		
    		$DB->execute($module_addModuleDirectory,$DB->escape($itemid));
    	}
    	else {
    		//update the mapping
    		$DB->execute($module_updateModuleDirectory,$DB->escape($catid),$DB->escape($itemid));
    	}
    }
}

// Suppress the module keywords
$DB->execute($module_removeKeywords,$DB->escape($itemid));

// add the module new keywords
for ($i=0;$i<40;$i++)
{
	if (!empty($_POST["kw".$i]))
	{
		$kw=$_POST["kw".$i];
		$kwsimplified=trim(suppress_accent($kw));
		$kwsimplified=strtolower($kwsimplified);
		$DB->getResults($module_getKeyword,$DB->quote($kwsimplified));
		if ($DB->nbResults()==0)
		{
			$DB->execute($module_addKeyword,$DB->quote($kw),$DB->quote($kwsimplified));
			$kwid=$DB->getId();
		}
		else
		{
			$row = $DB->fetch(0);
			$kwid=$row["id"];
		}
		$DB->freeResults();

		$DB->execute($module_addModuleKeyword,$kwid,$DB->escape($itemid),$DB->escape($_POST["w".$i]));
	}
}

$defaultIcon = "../modules/pictures/_deficon10.gif";
$urlIconBox = "../modules/pictures/box0_";
$urlPicture = "../modules/pictures/";

if( $icon != $urlIconBox.$itemid && $icon!=$defaultIcon ) {
	$extensionlogo = strrchr($icon,'.');
	if( strlen($extensionlogo)>5 )
		$extensionlogo = "";
	if ( $icon != $urlIconBox.$itemid.$extensionlogo )
	{
	
		//print"<br> FRI: copy(($urlPicture)($icon),($urlIconBox)($itemid)($extensionlogo))";
	
		if( copy($urlPicture.$icon,$urlIconBox.$itemid.$extensionlogo) ) {
			//update icon widget
			$DB->execute($scr_moduleUpdateIcon,$DB->quote($urlIconBox.$itemid.$extensionlogo),$DB->escape($itemid));
		}
	}
}
refresh_item($itemid,$template_folder);
refresh_directory($catid,$template_folder,"");


// category changes
if ($catid!=$oldcatid)
{
	refresh_directory($oldcatid,$template_folder,"");
}

$file=new xmlFile();
$file->header("module");
$file->status(1);
$file->footer();


?>