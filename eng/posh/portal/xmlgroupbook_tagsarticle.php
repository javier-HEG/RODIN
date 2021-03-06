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
# Get tags of an article
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlgroupbook_tagsarticle.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();
$gid = $_GET["gid"];
$file->header("tags");

$DB->getResults($xmlnotebookprofile_isInGroup, $DB->escape($gid), $DB->escape($_SESSION["user_id"]));
if ($DB->nbResults()==0)
{
	$groupbookAccessLevel="'O'";
}
else
{
	$groupbookAccessLevel="'M', 'O'";
}
$DB->freeResults();

$DB->getResults($sidebar_tagListGroupbook, $DB->escape($gid), $groupbookAccessLevel);
while($row=$DB->fetch(0))
{
	echo "<tag>";
	echo "<id>".$row["id"]."</id>";
	echo "<label><![CDATA[".$row["label"]."]]></label>";
	echo "<nb>".$row["nb"]."</nb>";
	echo "</tag>";
	
}
$DB->freeResults();

$file->footer("tags");

$DB->close();
?>