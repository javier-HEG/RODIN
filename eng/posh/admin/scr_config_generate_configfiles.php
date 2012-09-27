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
# POSH Configuration - generate configuration files (config.js and config.inc.php)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_config_generate_configfiles.php";
//includes
require_once("includes.php");
require('../includes/file.inc.php');
require_once('../includes/xml.inc.php');

launch_hook('admin_scr_generate_configfiles');
$contentjs="";
$contentphp="<?php".chr(13).chr(10);
//change the random number
$DB->execute($configgenerate_newRand,time());
// generate theconfiguration files config.js and config.inc.php from adm_config table
$DB->getResults($configgenerate_getParameters);
while ($row = $DB->fetch(0))
{	
	if ($row["desttype"]=="J" || $row["desttype"]=="A")
	{
		$pos = strpos($row["parameter"],"KEY");
		if($pos === false)
		{
			switch($row["datatype"])
			{
				case "int":{
					$contentjs.='var __'.$row["parameter"].'='.$row["value"].';'.chr(13).chr(10);
					break;}
				case "str":{
					$contentjs.='var __'.$row["parameter"].'="'.escapeJS($row["value"]).'";'.chr(13).chr(10);
					break;}
				case "arr":{
					$contentjs.='var __'.$row["parameter"].'=new Array('.$row["value"].');'.chr(13).chr(10);
					break;}
			}
		}
		else{
			error_log("never write the administration key in the config files");
		}
	}
	if ($row["desttype"]=="P" || $row["desttype"]=="A")
	{
		switch($row["datatype"])
		{
			case "int":{
				$contentphp.='define("__'.$row["parameter"].'",'.$row["value"].');'.chr(13).chr(10);
				break;}
			case "str":{
				$contentphp.='define("__'.$row["parameter"].'","'.addslashes($row["value"]).'");'.chr(13).chr(10);
				break;}
			case "arr":{
				$contentphp.='$'.'__'.$row["parameter"].'=array('.$row["value"].');'.chr(13).chr(10);
				break;}
		}
	}
}
$DB->freeResults();

$rows = $DB->select(FETCH_ARRAY,$configgenerate_getHeadLinks);
$contentjs.='var __headmenu=new Array(';
$first=true;
foreach ($rows as $row)
{
	if (!$first) $contentjs.=",";
	else $first=false;
	$contentjs.='{"id":"'.$row["uniq_id"].'","seq":'.$row["seq"].',"type":"'.$row["type"].'","label":lg("'.htmlspecialchars($row["label"]).'"),"comment":lg("'
		.htmlspecialchars($row["comment"]).'"),"clss":"'
		.htmlspecialchars($row["clss"]).'","images":"'
		.htmlspecialchars($row["images"]).'","fct":"'
		.htmlspecialchars($row["fct"]).'"';
	$contentjs.=',"anonymous":'
		.htmlspecialchars($row["anonymous"]).',"connected":'
		.htmlspecialchars($row["connected"]).',"admin":'
		.htmlspecialchars($row["admin"]).',"position":"'
		.htmlspecialchars($row["position"]).'"';
	if ($row["type"]=="menu")
	{
		$contentjs.=',"options":new Array(';

		$DB->getResults($configgenerate_getHeadLink,$DB->escape($row["id"]));

		$firstOption=true;
		while ($row2=$DB->fetch(0))
		{
			if (!$firstOption) $contentjs.=",";
			else $firstOption=false;
			$contentjs.='{"label":lg("'
				.htmlspecialchars($row2["label"]).'"),"comment":lg("'
				.htmlspecialchars($row2["comment"]).'"),"clss":"'
				.htmlspecialchars($row2["clss"]).'","images":"'
				.htmlspecialchars($row2["images"]).'","fct":"'
				.htmlspecialchars($row2["fct"]).'"';
			$contentjs.=',"anonymous":'
				.htmlspecialchars($row2["anonymous"]).',"connected":'
				.htmlspecialchars($row2["connected"]).',"admin":'
				.htmlspecialchars($row2["admin"]).'}';
		}
		$DB->freeResults();

		$contentjs.=')';
	}
	//else if ($row["type"]=="form")
	//{
	//	$contentjs.=',"options":"ok"';
	//}
	else
	{
		$contentjs.=',"options":""';
	}
	$contentjs.='}';
}
$contentjs.=');'.chr(13).chr(10);
$DB->freeResults();

$outfile=new file("../includes/config.js");
$outfile->write($contentjs);
$outfile=new file("../includes/config.inc.php");
$outfile->write($contentphp."?>");

$redirect=(isset($_POST["redirect"]))?$_POST["redirect"]:"config";

$file=new xmlFile();
$file->header();
$file->status(1);
$file->footer();

//header("location:".$redirect.".php");
?>