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
# Get user working group 
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder = "";
$not_access = 1;
$granted = "I";
$pagename = "portal/xmlnetwork_userworkinggroups.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file = new xmlFile();
$userId = $DB->escape($_SESSION['user_id']);

$file->header("workinggroups");

if (isset($_GET["uId"]))
{
    if (isset($_GET['allpublic']))
    {
        // give all public groups for a user
        $DB->getResults($xmlnetwork_getUserWorkingGroups,
                        $DB->escape($_GET["uId"])
        );
    }
    else
    {
    	//find user working groups or not private of other users working groups
    	$DB->getResults($xmlnetworksearch_getUserAuthWorkingGroups,
                        $DB->escape($userId),
                        $DB->escape($userId),
                        $DB->escape($userId),
                        $DB->escape($_GET["uId"])
        );
    }    
}
else
{
	//find user working group
	if (isset($_GET["okOnly"]))
    {
		$DB->getResults($xmlnetworksearch_getUserWorkingGroups,
                    $DB->escape($userId),
                    "'O'"
        );
	}
	else
    {
		$DB->getResults($xmlnetworksearch_getUserWorkingGroups,
                    $DB->escape($userId),
                    "'O', 'I'"
        );
	}
}

while($row=$DB->fetch(0))
{
	echo "<workinggroup>";
	echo "<id>".$row["id"]."</id>";
	echo "<name><![CDATA[".$row["name"]."]]></name>";
    echo "<description><![CDATA[".$row["description"]."]]></description>";
    echo "<picture><![CDATA[".$row["picture"]."]]></picture>";
	echo "<created_by>".$row["created_by"]."</created_by>";
	echo "<status>".$row["status"]."</status>";
	echo "</workinggroup>";
}

$DB->freeResults();

$file->footer();

$DB->close();
?>