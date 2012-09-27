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
# get information about my profile
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlnetwork_myprofile.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("profile");

//get general information about the user
$DB->getResults($xmlnetworkmyprofile_getProfile,$DB->escape($_SESSION['user_id']));
$row=$DB->fetch(0);
echo "<username>".$row["username"]."</username>";
echo "<longname><![CDATA[".$row["long_name"]."]]></longname>";
echo "<desc><![CDATA[".str_replace("\r\n","<br />",$row["description"])."]]></desc>";
echo "<picture><![CDATA[".$row["picture"]."]]></picture>";
echo "<stat><![CDATA[".$row["stat"]."]]></stat>";
echo "<activity>".$row["activity"]."</activity>";
echo "<keyword><![CDATA[".$row["keywords"]."]]></keyword>";
$DB->freeResults();

$file->footer("profile");

$DB->close();
?>