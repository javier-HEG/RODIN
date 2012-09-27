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
# Get recommended users
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlnetwork_recommendations.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("users");

//get the list of the user in my network
$DB->getResults($xmlnetworkusers_getExcludedMembers,$DB->escape($_SESSION['user_id']));
while($row=$DB->fetch(0))
{
	echo "<excluded><id>".$row["friend_id"]."</id></excluded>";
}
$DB->freeResults();

$p = isset($_GET["p"])?$_GET["p"]:0;

//get the list of the links
$DB->getResults($xmlnetworkusers_getSameFriends,$DB->escape($_SESSION['user_id']),$DB->escape($p*18));
$nbRes=$DB->nbResults();
if ($nbRes!=0)
{
	while($row=$DB->fetch(0))
	{
		echo "<user>"
			."<id>".$row["id"]."</id>"
			."<longname><![CDATA[".$row["long_name"]."]]></longname>"
			."<username><![CDATA[".$row["username"]."]]></username>"
			."<description><![CDATA[".$row["description"]."]]></description>"
			."<stat><![CDATA[".$row["stat"]."]]></stat>"
			."<statdate><![CDATA[".$row["statdate"]."]]></statdate>"
			."<activity>".$row["activity"]."</activity>"
			."<lastconndate>".$row["lastconnect_date"]."</lastconndate>"
			."<dbdate>".$row["dbdate"]."</dbdate>"
			."<picture><![CDATA[".$row["picture"]."]]></picture>"
			."<nbrel>".$row["nbrel"]."</nbrel>"
			."<type>relation</type>"
		."</user>";
	}
}
$DB->freeResults();

if ($nbRes<18)
{
	$DB->getResults($xmlnetworkusers_getSameTag,$DB->escape($_SESSION['user_id']),0);
	while($row=$DB->fetch(0))
	{
		echo "<user>"
			."<id>".$row["id"]."</id>"
			."<longname><![CDATA[".$row["long_name"]."]]></longname>"
			."<username><![CDATA[".$row["username"]."]]></username>"
			."<description><![CDATA[".$row["description"]."]]></description>"
			."<stat><![CDATA[".$row["stat"]."]]></stat>"
			."<activity>".$row["activity"]."</activity>"
			."<lastconndate>".$row["lastconnect_date"]."</lastconndate>"
			."<dbdate>".$row["dbdate"]."</dbdate>"
			."<picture>".$row["picture"]."</picture>"
			."<nbrel>".$row["nbcommontags"]."</nbrel>"
			."<type>tag</type>"
		."</user>";
	}
	$DB->freeResults();
}

$file->footer();

$DB->close();
?>