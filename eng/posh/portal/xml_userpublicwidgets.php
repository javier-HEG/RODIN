<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of POSH http://sourceforge.net/projects/posh/.

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
# Get a user public widgets
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xml_userpublicwidgets.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("widgets");

$userId=$_GET['id'];
$sharedAccessList='3';

//check if I am in the user network
$DB->getResults($xmlnotebookprofile_isInNetwork,$DB->escape($userId),$DB->escape($_SESSION['user_id']));
if ($DB->nbResults()!=0)
{
	$sharedAccessList.=',2';
}

$xmlnetwork_getWidgets="
	SELECT	c.id,
			a.variables,
			b.name AS portname,
			c.name AS widname
	FROM 	module AS a,
			profile AS b,
			dir_item AS c
	WHERE	b.id=a.profile_id
		AND	a.item_id=c.id
		AND b.user_id=%u
		AND a.shared IN (%s)
	ORDER BY b.id,a.uniq
";

//get the list of my followers
$DB->getResults($xmlnetwork_getWidgets,$DB->escape($userId),$sharedAccessList);
while($row=$DB->fetch(0))
{
	echo "<widget>";
	echo "<id>".$row["id"]."</id>";
	echo "<variables><![CDATA[".$row["variables"]."]]></variables>";
	echo "<portname><![CDATA[".$row["portname"]."]]></portname>";
	echo "<widname><![CDATA[".$row["widname"]."]]></widname>";
	echo "</widget>";
}
$DB->freeResults();

$file->footer();

$DB->close();
?>