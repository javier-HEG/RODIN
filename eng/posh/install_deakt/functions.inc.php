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
/* UTF8 encoding : é ô à ù */

function setStep($step,$installType){
	$str = '<'.'?php define("__INSTALLATIONSTEP",'.$step.');';
	$str.= 'define("__INSTALLTYPE",'.$installType.');';
	$str.= '$'.'__AVLANGS=array("en","fr","de");?'.'>';
	$outfile=new file("../includes/config.inc.php");
	$outfile->write($str);
}
function getParam($DB,$param,$default){
	global $install_getParam;
	$DB->getResults($install_getParam,$DB->quote($param));

	if ($DB->nbResults()<1){
		$ret=$default;
	} else {
		$row=$DB->fetch(0);
		$ret=$row["value"];
	}
	$DB->freeResults();
	return $ret;
}
function moduleUsed($DB,$moduleId){
	global $DB,$install_checkIfModuleExists;
	$DB->getResults($install_checkIfModuleExists,$DB->escape($moduleId));
	$ret=($DB->nbResults()>0)?true:false;
	$DB->freeResults();	
	return $ret;
}
function generateConfigFile($v_final,$v_server,$v_login,$v_pass,$v_db){
	if (!isset($GLOBALS['DB'])){
		if ($v_server!="" AND $v_login!=""){$DB=new connection($v_server,$v_login,$v_pass,$v_db);}
		else {$DB=new connection(__SERVER,__LOGIN,__PASS,__DB);}
	} else {global $DB;}
	global $install_getAllParams,$install_getHeadLinks,$install_getHeadLink;

	$contentjs="";
	$contentphp="<?php".chr(13).chr(10);
	// generate the starting pages
	$DB->getResults($install_getAllParams);
	while ($row = $DB->fetch(0)){
		if ($row["desttype"]=="J" OR $row["desttype"]=="A"){
			switch($row["datatype"]){
				case "int":{
					$contentjs.='var __'.$row["parameter"].'='.$row["value"].';'.chr(13).chr(10);
					break;}
				case "str":{
					$contentjs.='var __'.$row["parameter"].'="'.addslashes($row["value"]).'";'.chr(13).chr(10);
					break;}
				case "arr":{
					$contentjs.='var __'.$row["parameter"].'=new Array('.$row["value"].');'.chr(13).chr(10);
					break;}
				default:{
					break;}
			}
		}
		if ($row["desttype"]=="P" OR $row["desttype"]=="A"){
			switch($row["datatype"]){
				case "int":
					$contentphp.='define("__'.$row["parameter"].'",'.$row["value"].');'.chr(13).chr(10);
					break;
				case "str":
					$contentphp.='define("__'.$row["parameter"].'","'.addslashes($row["value"]).'");'.chr(13).chr(10);
					break;
				case "arr":
					$contentphp.='$'.'__'.$row["parameter"].'=array('.$row["value"].');'.chr(13).chr(10);
					break;
				default:{
					break;}
			}
		}
	}
	$DB->freeResults();
	
	$rows = $DB->select(FETCH_ARRAY,$install_getHeadLinks);
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
		.htmlspecialchars($row["position"]).'"';;
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
	else if ($row["type"]=="form")
	{
		$contentjs.=',"options":"ok"';
	}
	else
	{
		$contentjs.=',"options":""';
	}
	$contentjs.='}';
}
$contentjs.=');'.chr(13).chr(10);
$DB->freeResults();

	//$DB->close();

//	if (!$v_final) $contentjs.="window.location='../install/';";

	$outfile=new file("../includes/config.js");
	$outfile->write($contentjs);
	$outfile=new file("../includes/config.inc.php");
	$outfile->write($contentphp."?>");
}
?>