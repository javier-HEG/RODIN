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
# POSH load existing plugins
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_load_plugins.php";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');
require_once('../includes/file.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');

$file=new xmlFile();

$file->header("plugins");

$dir='../includes/plugins/';
$plugins = array();

$DB->getResults($plugin_getPluginList);
$inc=0;
$installed=array();
while ($row=$DB->fetch(0))
{
	$installed[$inc]=$row["name"];
	$inc++;
}
$DB->freeResults();


search_plugins($dir,$plugins,true);

$ajaxing=1;

foreach ($plugins as $id => $p)
{
    if (in_array($p["name"],$installed)) { $install=1; }
    else { $install=0; }
    /*
    foreach (  $p as $cle => $val ) {
        error_log("item from base : $id -> $cle : $val");
    }
    */
    echo "<plugin>";
    echo "<id>".$id."</id>";
    
    echo "<name><![CDATA[".$p["name"]."]]></name>";
    echo "<description><![CDATA[".$p["description"]."]]></description>";
    echo "<dependencies>".$p["dependencies"]."</dependencies>";
    echo "<file>".$p["file"]."</file>";
    echo "<dir>".$p["dir"]."</dir>";
    echo "<link>".$p["link"]."</link>";
    echo "<display>".$p['display']."</display>";
    echo "<installed>".$install."</installed>";
   // write_plugin_box($id,$p["name"],$p["description"],$p["dependencies"],$p["file"],$p["dir"],$p["link"],in_array($p["name"],$installed));
   echo "<plugform><![CDATA[";
   //add   $p["ajaxplug"] in this script to continue using plugin with old posh version, to delete after some time
    $p["ajaxplug"] = 1;
   launch_hook("display_plugin",$p);
   echo "]]></plugform>";
   
    echo "</plugin>";
}
$file->footer("plugins");

/*
 * Search plugins in a dir
 * Do it recursively
 * Input :
 *	$path (string) : path to explore, looking for plugins
 *	$old_plugins (boolean) : look for old plugins (only at the root)
 * Output :
 *	$plugins (array) : plugins in the folder
 */
function search_plugins($path,&$plugins,$old_plugins=false)
{
	$handle=opendir($path);
	while ($file = readdir($handle)) {
		// Old fashionned plugins
		if ($old_plugins && substr($file,-5)=='.info' && is_file($path.$file)){
			$infile=new file($path.$file);
			$parameterlist=$infile->read();
			$parameter=explode(";",$parameterlist);
			for ($i=0;$i<count($parameter)-1;$i++){
				$pair=explode("=",trim($parameter[$i]));
				$value[$pair[0]]=$pair[1];
			}
			$value["dir"]=$value["directory"];
			$plugins[]=$value;
		} else
		// new format
		if (substr($file,-4)=='.php' && is_file($path.$file)){
			$infile=new file($path.$file);
			$filecontent=$infile->read();
			if (fetch_plugin_header($filecontent,$header))
			{
				// Fetch the metadata
				// Get the name
				if (!fetch_plugin_metadata($header,'name',$p["name"]))
				{
					// No valid name => plugin not valid
					continue;
				}
				// Get the description
				if (!fetch_plugin_metadata($header,'description',$p["description"]))
				{
					// No valid description => plugin not valid
					continue;
				}
				// Get the dependencies
				if (!fetch_plugin_metadata($header,'dependencies',$p["dependencies"]))
				{
					// No valid dependencies => plugin not valid
					continue;
				}
                fetch_plugin_metadata($header,'display',$p["display"]);
				$p["file"]=substr($file,0,strlen($file)-4);
				$p["dir"]=str_replace('../includes/plugins/','',$path);
				$p["link"]=str_replace('../includes/plugins/','',$path.$file);
				
				$plugins[]=$p;
			}
			
		}else if (is_dir($path.$file) && $file != '.' && $file !='..')
		{
			// Recursively in other dirs
			search_plugins($path.$file.'/',$plugins);
		}
	}
	closedir($handle);
}
?>