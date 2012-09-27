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
# re-generate all the cache (modules, directory, lists ...)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_cache_generate_all_install.php";
//includes

require_once("includes.php");
require_once('../includes/refreshcache.inc.php');
require_once('../includes/pagegeneration.inc.php');

launch_hook('admin_scr_cache_generate');

//update quantities
$DB->execute($cachegeneration_setZeroQuantity);
$DB->execute($cachegeneration_removeTempDirectories);
$DB->execute($cachegeneration_5thDirectoryLevel);
$DB->execute($cachegeneration_4thDirectoryLevel);
$DB->execute($cachegeneration_3rdDirectoryLevel);
$DB->execute($cachegeneration_2ndDirectoryLevel);
$DB->execute($cachegeneration_1stDirectoryLevel);
$DB->execute($cachegeneration_mystery1stLevel);
$DB->execute($cachegeneration_mystery2ndLevel);
$DB->execute($cachegeneration_mystery3rdLevel);
$DB->execute($cachegeneration_mystery4thLevel);
$DB->execute($cachegeneration_mystery5thLevel);
$DB->execute($cachegeneration_updateTempCat);
$DB->execute($cachegeneration_updateTempCatSecured);

//generate main category cache
$_GET['catid']=0;
$script_file=$template_folder."xml_cache_directory.php";
$cache_file="../cache/cat_0.xml";
cacheDataInFile($script_file, $cache_file);

//generate category caches
$DB->sql = $cachegeneration_getDirectoryId;
$rows = $DB->select(FETCH_ARRAY);
foreach ($rows as $row)
{
	$_GET['catid']=$row['id'];
	$script_file=$template_folder."xml_cache_directory.php";
	$cache_file="../cache/cat_".$row["id"]."_".$row["lang"].".xml";
	cacheDataInFile($script_file, $cache_file);
}

$item=array();
$rows = $DB->getResults($cachegeneration_getModuleId);
while ($row = $DB->fetch(0)){array_push($item,$row["id"]);}
//add RSS reader module
array_push($item,"86");
$DB->freeResults();

while(list($key,$val)=each($item))
{
	$_GET['modid']=$val;
	$script_file=$template_folder."xml_cache_item.php";
	$cache_file="../cache/item_".$val.".xml";				
	cacheDataInFile($script_file, $cache_file);
}

//generate first tabs
$script_file=$template_folder."xml_tabs_generation.php";
$cache_file="../portal/selections/tabs.xml";
cacheDataInFile($script_file,$cache_file);
$script_file=$template_folder."xml_tabs_connected_generation.php";
$cache_file="../portal/selections/tabs_connected.inc.xml";
cacheDataInFile($script_file,$cache_file);

// generate the starting pages
$DB->sql = $cachegeneration_getPagesId;
$rows = $DB->select(FETCH_ARRAY);
$row = $rows[0];

$_GET['id']=$row['id'];
$script_file=$template_folder."xml_pages_generation.php";
$cache_file="../portal/selections/page0.xml";
cacheDataInFile($script_file, $cache_file);

// generate the pages file for personalize portal
$DB->sql =$cachegeneration_getPersonalizedPages ;
$rows = $DB->select(FETCH_ARRAY);
foreach ($rows as $row)
{
	$_GET['id']=$row['id'];
	$script_file=$template_folder."xml_pages_generation.php";
	$cache_file="../portal/selections/page".$row["id"].".xml";
	cacheDataInFile($script_file, $cache_file);
}

$script_file=$template_folder."js_cache_waiting.php";
$cache_file="../portal/selections/waiting.js";
cacheDataInFile($script_file, $cache_file);

$script_file=$template_folder . "js_cache_waiting.php";
$cache_file="../portal/selections/waiting.js";
cacheDataInFile($script_file, $cache_file);


if (isset($_GET["install"]))
{
	header("location:scr_config_generate_configfiles_install.php?redirect=../install/generateplugins");
}
else
{
	header("location:scr_config_generate_configfiles_install.php?redirect=index");
}

?>