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
# get information about a user of my network
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlnetwork_userdetail.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("user");

$id=$_GET["id"];

//get general information about the user
$DB->getResults($xmlnetworkuserdetail_getUser,$DB->escape($id));
$row=$DB->fetch(0);
echo "<username>".$row["username"]."</username>";
echo "<longname><![CDATA[".$row["long_name"]."]]></longname>";
echo "<picture>".$row["picture"]."</picture>";
$DB->freeResults();

//get the description you set for this user
$DB->getResults($xmlnetworkuserdetail_getMyDescription,$DB->escape($_SESSION['user_id']),$DB->escape($id));
if ($DB->nbResults()>0)
{
	echo "<new>0</new>";
	$row=$DB->fetch(0);
	echo "<description><![CDATA[".$row["description"]."]]></description>";

	$DB->freeResults();
	
	//get the keywords you set for this user
	$DB->getResults($xmlnetworkuserdetail_getMyKeywords,$DB->escape($_SESSION['user_id']),$DB->escape($id));
	while($row=$DB->fetch(0))
	{
		echo "<keyword><![CDATA[".$row["label"]."]]></keyword>";
	}
}

//get the number of groups the user is a member of
$DB->getResults($xmlgroup_getNbGroups,$DB->escape($_SESSION['user_id']));
if ($DB->nbResults()>0)
    echo "<group>1</group>";

$DB->freeResults();

$file->footer("user");

$DB->close();
?>