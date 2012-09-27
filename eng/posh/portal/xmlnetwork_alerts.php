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
# Get users network alerts 
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : Ã©Ã Ã¨Ã¹
# ***************************************

$folder     = "";
$not_access = 1;
$granted    = "I";
$pagename   = "portal/xmlnetwork_alerts.php";

$page       = $_GET['page'];

//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("alerts");

$DB->getResults($xmlnetworksearch_getUserWorkingGroups,
            $DB->escape($_SESSION['user_id']),
            "'I'"
);
while($row = $DB->fetch(0))
{
	echo "<group>";
	echo "<id>".$row["id"]."</id>";
	echo "<name><![CDATA[".$row["name"]."]]></name>";
	echo "<status>".$row["status"]."</status>";
	echo "</group>";
}
$DB->freeResults();

$DB->getResults($xmlnetwork_getAlerts,
            $DB->escape($_SESSION['user_id']),
            $page*10
);
while($row = $DB->fetch(0))
{
	echo "<alert>";
	echo "<type>".$row["type"]."</type>";
    echo "<refid>".$row["referer_id"]."</refid>";
	echo "<refname><![CDATA[".$row["referer_name"]."]]></refname>";
	echo "</alert>";
}
$DB->freeResults();

$file->footer();

$DB->close();
?>