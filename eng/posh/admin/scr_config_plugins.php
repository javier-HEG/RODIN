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
# POSH Configuration - set plugins list
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_config_plugins.php";
//includes
require_once("includes.php");
require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');
require_once('../includes/file.inc.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("plugins");

launch_hook('admin_scr_config_plugins');

$inc=0;
$dberror="";

while (isset($_POST["plugi".$inc]))
{
	$name=$_POST["name".$inc];
	// suppressed plugins
	// new plugins
	if (array_key_exists('file'.$inc,$_POST) 
		&& $_POST["plugi".$inc]=="1" 
		&& !isset($_POST["plug".$inc])){

		$DB->execute($plugin_removePlugin,$DB->quote($_POST["name".$inc]));
		
		launch_hook('uninstall_'.$_POST['file'.$inc]);
		unset($PLUGINS[array_search($_POST['link'.$inc],$PLUGINS)]);
        // old fashionned plugin
	}
	else if ($_POST["plugi".$inc]=="1" 
             && !isset($_POST["plug".$inc]) 
             && array_search($_POST['link'.$inc],$PLUGINS)!==false) {
             
		$DB->execute($plugin_removePlugin,$DB->quote($_POST["name".$inc]));
		
		// uninstall DB objects
		$uninstallfile="../includes/plugins/".$_POST["dir".$inc]."/uninstall.sql";
		if (is_file($uninstallfile)) {
			$sqlfile=new file($uninstallfile);
			$sqlcontent=$sqlfile->read();
			$sql=explode(";",$sqlcontent);
			for ($i=0;$i<count($sql);$i++)
			{
				$strsql=trim($sql[$i]);
				if ($strsql!="")    {
					$DB->sql = $strsql;
					if (!$DB->execute($DB->sql))    {
						$dberror.="uninstalling ".$name." - error on following SQL command : ".$strsql." (".mysql_error().")\r\n";
					}
				}
			}
			$DB->sql = "USE ".__DB." ";
			$DB->execute($DB->sql);
		}
		unset($PLUGINS[array_search($_POST['link'.$inc],$PLUGINS)]);
	}
	// added plugins
	// New plugins
	if (array_key_exists('file'.$inc,$_POST)  
        && isset($_POST["plug".$inc]) 
        && array_search($_POST['link'.$inc],$PLUGINS)===false 
        && $_POST["plugi".$inc]=="0")   {
        
		//register in the DB
		$DB->execute($plugin_addNewPlugin,$DB->quote($name),$DB->quote($_POST["link".$inc]),$DB->quote($_POST["depend".$inc]));
		
		// If the file really exists
		if (file_exists("../includes/plugins/".$_POST["link".$inc]))    {
			// EMA on 08/23/07  - on first installation, plugin is not loaded in plugin.inc.php, then artificially load it for install action
			include_once("../includes/plugins/".$_POST["link".$inc]);
			$PLUGINS[] = $_POST['link'.$inc];
			// Executes the installation
			launch_hook('install_'.$_POST['file'.$inc]);
		}
	// Old fashionned plugin
	}
    else if ($_POST["plugi".$inc]=="0" 
                && isset($_POST["plug".$inc]) 
                && array_search($_POST['link'.$inc],$PLUGINS)===false)  {	
		
        //register in the DB
		$DB->execute($plugin_addNewPlugin);
		
		// install DB objects
		$installfile="../includes/plugins/".$_POST["dir".$inc]."/install.sql";
		if (is_file($installfile))
		{
			$sqlfile=new file($installfile);
			$sqlcontent=$sqlfile->read();
			$sql=explode(";",$sqlcontent);
			for ($i=0;$i<count($sql);$i++)
			{
				$strsql=trim($sql[$i]);
				if ($strsql!="")
				{
					$DB->sql = $strsql;
					if (!$DB->execute($DB->sql))
					{
						$dberror.="installing ".$name." - error on following SQL command : ".$strsql." (".mysql_error().")\r\n";
					}
				}
			}
			$DB->sql = "USE ".__DB." ";
			$DB->execute($DB->sql);
		}
		$PLUGINS[]=$_POST['link'.$inc];
	}
	$inc++;
}

//generate the plugins file from the adm_plugins table
$pluginstr="";

$pluginstr.="<?"."php if (!defined('__relativeIncludePath')) define('__relativeIncludePath','../includes/');?".">";

$DB->getResults($plugin_getPlugins);
while ($row=$DB->fetch(0))
{
	$pluginstr.=(substr($row["link"],0,7)=="http://")?("<?"."php include_once('".$row["link"]."');?".">\n"):("<?"."php include_once(__relativeIncludePath.'plugins/".$row["link"]."');?".">\n");
}
$DB->freeResults();
$pluginstr .="<".'?php 
	global $PLUGINS;
	$PLUGINS = unserialize(\''.serialize($PLUGINS).'\');
?'.">\n";
$outfile=new file("../includes/plugins.inc.php");
$outfile->write($pluginstr);

if ($dberror!="")
{
	$outfile=new file("../includes/plugins.log");
	$outfile->write($dberror);
}

/*
if (isset($_GET["redirect"]))
{
	header("location:".$_GET["redirect"].".php");
	exit();
}

if ($dberror=="")
{
	header("location:scr_config_generate_configfiles.php?redirect=config");
}
else
{
	header("location:config_plugins.php?error=1");
}
*/

$file->status(1);

$file->footer();
?>