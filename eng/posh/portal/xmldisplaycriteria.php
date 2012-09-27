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
# Users criteria
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmldisplaycriteria.php";

//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();
$file->header("userinfo");

echo '<email>'.$_SESSION['username'].'</email>';

//get criterias
$DB->getResults($criteria_getCompleteCriterias,$DB->escape($_SESSION['user_id']));

while ($row=$DB->fetch(0))
{
	echo "<criteria>";
    echo "<id>".$row["id"]."</id>";
	echo "<label><![CDATA[".$row["label"]."]]></label>";
	echo "<type><![CDATA[".$row["type"]."]]></type>";
	echo "<options><![CDATA[".$row["options"]."]]></options>";
	echo "<parameters><![CDATA[".$row["parameters"]."]]></parameters>";
	echo "<editable><![CDATA[".$row["editable"]."]]></editable>";
	echo "</criteria>";
}
$DB->freeResults();

$file->footer("userinfo");
$DB->close();
?>