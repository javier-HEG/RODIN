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
# generate the XML user friends list
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlfriends.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("friends");

$DB->getResults($xmlfriends_getFriends,$DB->escape($_SESSION['user_id']));

while($row=$DB->fetch(0))
{
	echo "<friend>";
	echo "<id>".$row["friend_id"]."</id>";
	echo "<email><![CDATA[".$row["email"]."]]></email>";
	echo "<name><![CDATA[".$row["long_name"]."]]></name>";
	echo "</friend>";
}
$DB->freeResults();

$file->footer("friends");

$DB->close();
?>