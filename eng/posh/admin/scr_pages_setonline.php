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
# POSH Pages management - Set Online tabs change 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_pages_setonline.php";
//includes
require('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');
require_once('../includes/pagegeneration.inc.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

launch_hook('admin_scr_pages_setonline');

// generate the tabs xml file
$script_file=$template_folder."xml_tabs_generation.php";
$cache_file="../portal/selections/tabs.xml";
$chk1=cacheDataInFile($script_file,$cache_file);
$script_file=$template_folder."xml_tabs_connected_generation.php";
$cache_file="../portal/selections/tabs_connected.inc.xml";
$chk2=cacheDataInFile($script_file,$cache_file);

// generate the starting pages
$DB->getResults($pages_getPagesId);
$row = $DB->fetch(0);
$_GET['id']=$row['id'];
$DB->freeResults();
$script_file=$template_folder."xml_pages_generation.php";
$cache_file="../portal/selections/page0.xml";
$chk3=cacheDataInFile($script_file, $cache_file);


// generate the pages file for personalize portal
$DB->sql =$oages_getPersonalizedPageId;
$rows=$DB->select(FETCH_ARRAY);
$chk4=true;
foreach ($rows as $row)
{
	$_GET['id']=$row['id'];
	$script_file=$template_folder."xml_pages_generation.php";
	if ($row["position"]==1)
	{
		$cache_file="../portal/selections/page".$row["id"].".xml";
	}
	else
	{
		$cache_file="../portal/selections/page".($row["id"]+1000000000).".xml";
	}
	if ($chk4)
		$chk4=cacheDataInFile($script_file, $cache_file);
}

if ($chk1 && $chk2 && $chk3 && $chk4)
{
	$file->status("1");
	$file->message(lg("pagesNowOnline"));
}
else
{
	$file->message("ERROR ".(($chk1*1)+($chk2*2)+($chk3*4)+($chk4*8)));
}

$file->footer("channel");
?>