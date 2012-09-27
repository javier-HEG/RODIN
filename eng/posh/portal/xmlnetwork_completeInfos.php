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
# XML complette user infos
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$info="";
$keywords="";
$pagename="portal/xmlnetwork_completeInfos.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$userid=(isset($_GET['id']) ? $_GET['id'] : exit('ID missing'));

$file=new xmlFile();

$file->header();

//get the user informations
$DB->getResults($frmnetworkprofile_getUser,$DB->escape($userid));
$row=$DB->fetch(0);
$picture=($row["picture"]==""?"../images/nopicture.gif":$row["picture"]);
echo "<id>".$userid."</id>";
echo "<picture><![CDATA[".$picture."]]></picture>";
echo "<description><![CDATA[".$row['description']."]]></description>";
echo "<username><![CDATA[".$row['username']."]]></username>";
echo "<longname><![CDATA[".$row['long_name']."]]></longname>";
echo "<keywords><![CDATA[".$row['keywords']."]]></keywords>";
$DB->freeResults();

//get the user criterias
$DB->getResults($criteria_getPublicCriterias,$DB->escape($userid));
if ($DB->nbResults()>0)
{
	while($row=$DB->fetch(0))
	{
		echo "<criteria>";
		echo "<parameters><![CDATA[".$row['parameters']."]]></parameters>";
		echo "<label><![CDATA[".$row['label']."]]></label>";
		echo "<type>".$row['type']."</type>";
		echo "<options><![CDATA[".$row['options']."]]></options>";
		echo "</criteria>";
	}	
}		

//check if the person is in my network
$DB->getResults($xmlnetworkuserdetail_getMyDescription,$DB->escape($_SESSION['user_id']),$DB->escape($userid));
$isExisting = $DB->nbResults();
echo "<innetwork>".$isExisting."</innetwork>";
if ($isExisting>0)
{
	//get the description you set for this user
	$row=$DB->fetch(0);
    echo "<mydescription><![CDATA[".$row["description"]."]]></mydescription>";
    $DB->freeResults();
	//get the keywords you set for this user
	$DB->getResults($xmlnetworkuserdetail_getMyKeywords,$DB->escape($_SESSION['user_id']),$DB->escape($userid));
	while($row=$DB->fetch(0))
	{
		echo "<mykeywords><![CDATA[".$row["label"]."]]></mykeywords>";
	}
}
$DB->freeResults();

$file->footer();

$DB->close();
?>