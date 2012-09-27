<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of POSH (Portaneo Open Source Homepage) http://sourceforge.net/projects/posh/.

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
# restricted modules for a user
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
#
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlexplorer.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

$dirId = $_GET['dirid'];

//get current directory information
$DB->getResults($explorer_getCurrentDirectory, $DB->escape($dirId));
$row = $DB->fetch(0);
echo "<dirname><![CDATA[".$row["name"]." (".$row["q"].")]]></dirname>";
echo "<parent>".$row["parent_id"]."</parent>";
$DB->freeResults();

//get subdirectories
$DB->getResults($xmldirectory_getChildrenDirectoryXml, $DB->escape($dirId));

if ($DB->nbResults() > 0)
{
	while ($row = $DB->fetch(0))
	{
		echo "<dir>";
		echo "<dirid>".$row['id']."</dirid>";
		echo "<dirname><![CDATA[".$row['name']."]]></dirname>";
        echo "<quantity>".$row['quantity']."</quantity>";
        echo "<secured>".$row['secured']."</secured>";
        echo "<secured_quantity>".$row['id']."</secured_quantity>";
		echo "</dir>";
	}
}
$DB->freeResults();

// get widgets
$DB->getResults($xmlexplorer_getItems,
                    $DB->escape($dirId),
                    $DB->escape($_SESSION['user_id']));

if ($DB->nbResults() > 0)
{
	while ($row = $DB->fetch(0))
	{
		echo "<item>";
		echo "<id>".$row['id']."</id>";
        echo "<icon><![CDATA[".$row['icon']."]]></icon>";
        echo "<secured>1</secured>";
		echo "<name><![CDATA[".$row['name']."]]></name>";
		echo "</item>";
	}
}
$DB->freeResults();

$file->footer();

$DB->close();
?>