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
# re-generate the cache files (modules, directories, lists ...)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_cache_update.php";
//includes
require_once("includes.php");
$template_folder=__LOCALFOLDER."admin/";
require_once("../includes/pagegeneration.inc.php");


launch_hook('admin_scr_cache_update');

$script_file=$template_folder . "js_cache_waiting.php";
$cache_file="../portal/selections/waiting.js";
cacheDataInFile($script_file, $cache_file);

//Generation of the "our selection" XML Feed
$script_file=$template_folder . "xml_cache_ourselection.php";
$cache_file="../portal/selections/ourselection.xml";
cacheDataInFile($script_file, $cache_file);

$script_file=$template_folder . "xml_cache_newservices.php";
$cache_file="../portal/selections/newservices.xml";
cacheDataInFile($script_file, $cache_file);

//rss page generation
$script_file=$template_folder . "tem_cache_rss.php";
$cache_file="../portal/selections/portaneo.xml";
cacheDataInFile($script_file, $cache_file);

$script_file=$template_folder . "tem_cache_waited.php";
$cache_file="../portal/selections/waited.html";
cacheDataInFile($script_file, $cache_file);


//Get the number of pages
$DB->getResults($cacheupdate_getModuleNumber);
$row = $DB->fetch(0);
$nbpage = ceil($row["nb"]/5);
$DB->freeResults();

$inc=0;
for ($i=1;$i<=$nbpage;$i++)
{
	$inc++;
	$script_file=$template_folder . "xml_cache_allservices1.php?p=".$inc."&nbpg=".$nbpage;
	$cache_file="../portal/selections/allservices1_" . $inc . ".xml";
	cacheDataInFile($script_file, $cache_file);
	$script_file=$template_folder . "xml_cache_allservices2.php?p=".$inc."&nbpg=".$nbpage;
	$cache_file="../portal/selections/allservices2_" . $inc . ".xml";
	cacheDataInFile($script_file, $cache_file);
	$script_file=$template_folder . "xml_cache_allservices3.php?p=".$inc."&nbpg=".$nbpage;
	$cache_file="../portal/selections/allservices3_" . $inc . ".xml";
	cacheDataInFile($script_file, $cache_file);
	$script_file=$template_folder . "xml_cache_allservices4.php?p=".$inc."&nbpg=".$nbpage;
	$cache_file="../portal/selections/allservices4_" . $inc . ".xml";
	cacheDataInFile($script_file, $cache_file);
}


header("location:index.php");
?>