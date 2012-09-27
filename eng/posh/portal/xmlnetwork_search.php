<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of COLLABORATION SUITE of POSH http://sourceforge.net/projects/posh/.

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
# Get users search result
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$type=isset($_GET["type"])?$_GET["type"]:"m";
$search=(isset($_GET["search"]))?$_GET["search"]:exit();

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlnetwork_search.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("results");

$p = isset($_GET["p"])?$_GET["p"]:0;

//search people with email
if ($type=="m")
{
	$DB->getResults($xmlnetworksearch_getUserByName,$DB->quote($search));
}
//search people with name or tag
if ($type=="t")
{

	$keyword=str_replace(",","','",$search);
	$DB->getResults($xmlnetworksearch_getUserByKeywords,$keyword,$DB->escape($_SESSION['user_id']),$DB->escape($p*10));

	//if no result with tag, try the name
	if ($DB->nbResults()==0)
	{
		$DB->freeResults();
		$DB->getResults($xmlnetworksearch_getUserByNamePart,$DB->quote("%".$search."%"),$DB->escape($p*10));
	}
}

while($row=$DB->fetch(0))
{
	echo "<user>";
	echo "<id>".$row["id"]."</id>";
	echo "<name><![CDATA[".$row["long_name"]."]]></name>";
	echo "<picture><![CDATA[".$row["picture"]."]]></picture>";
	echo "</user>";
	
}
$DB->freeResults();

$file->footer("results");

$DB->close();
?>