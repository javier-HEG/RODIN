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
# POSH Users management - XML List of all the users portals
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_allportals.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("portals");

$p=$_GET["p"];

echo "<page>".$p."</page>";

$DB->getResults($portals_getAllXml,$DB->escape(($p-1)*21));
while ($row = $DB->fetch(0))
{
	echo "<portal>";
	echo "<id>".$row["id"]."</id>";
	echo "<name><![CDATA[".$row["name"]."]]></name>";
	echo "<status>".$row["status"]."</status>";
	echo "</portal>";
}
$DB->freeResults();

$file->footer("portals");

$DB->close();
?>