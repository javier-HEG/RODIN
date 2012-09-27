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
# Get users of a group 
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : Ã©Ã Ã¨Ã¹
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlnetwork_usersgroup.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("users");

// Get group description
$DB->getResults($xmlnetworkusers_getDesc, $DB->escape($_SESSION['user_id']));
$descriptions = array();
while($row=$DB->fetch(0))
{
	$key = $row["id"];
	$descriptions[$key] = $row["description"]; 
}
$DB->freeResults();

// get users for the group
$DB->getResults($xmlnetworkusers_getUsersByGroup, $DB->escape($_GET["gid"]) );

while($row=$DB->fetch(0))
{
	echo "<user>";
	echo "<id>".$row["id"]."</id>";
	echo "<longname><![CDATA[".$row["long_name"]."]]></longname>";
	echo "<username><![CDATA[".$row["username"]."]]></username>";
	if (isset($descriptions[$row["id"]])) {
		echo "<description><![CDATA[".$descriptions[$row["id"]]."]]></description>";
	}
	echo "<stat><![CDATA[".$row["stat"]."]]></stat>";
	echo "<statdate><![CDATA[".$row["statdate"]."]]></statdate>";
	echo "<activity>".$row["activity"]."</activity>";
	echo "<lastconndate>".$row["lastconnect_date"]."</lastconndate>";
	echo "<dbdate>".$row["dbdate"]."</dbdate>";
	echo "<picture><![CDATA[".$row["picture"]."]]></picture>";
	echo "</user>";
	
}
$DB->freeResults();

$file->footer();

$DB->close();
?>