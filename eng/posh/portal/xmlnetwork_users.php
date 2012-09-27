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
# Get users of my network for a keyword
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlnetwork_users.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("users");

//find user of my network, either from id, or from keyword
if ($_GET["kwid"]==0)
{
	$DB->getResults($xmlnetworkusers_getUserById,$DB->escape($_SESSION['user_id']),$DB->escape($_GET['s']));
}
else
{
	$DB->getResults($xmlnetworkusers_getUserByKeyword,$DB->escape($_SESSION['user_id']),$DB->escape($_GET["kwid"]),$DB->escape($_GET['s']));
}

while($row=$DB->fetch(0))
{
	echo "<user>";
	echo "<id>".$row["id"]."</id>";
	echo "<longname><![CDATA[".$row["long_name"]."]]></longname>";
	echo "<username><![CDATA[".$row["username"]."]]></username>";
	echo "<description><![CDATA[".$row["description"]."]]></description>";
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