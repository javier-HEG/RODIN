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
# POSH Pages  management - XML List of tabs
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=0;
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile(false);

$file->header("tabs");

$DB->getResults($pages_getAnonymousTabs);
while($row = $DB->fetch(0))
{

	echo "<tab>
		<number>".$row["id"]."</number>
		<name><![CDATA[".$row["name"]."]]></name>
		<icon><![CDATA[".$row["icon"]."]]></icon>
		<type>".$row["type"]."</type>
		<param><![CDATA[".$row["param"]."]]></param>
		<action>";

	if ($row["type"]==1)
	{
		echo "$"."p.app.pages.change(".$row["id"].")";
	}
	else if ($row["type"]==2)
	{
		echo "$"."p.app.pages.frame('".$row["param"]."',".$row["id"].")";
	}
    else if ($row["type"]==4)
	{
		echo "$"."p.app.pages.redirect('".$row["param"]."',".$row["id"].")";
	}
	else
	{
		echo $row["param"];
	}
	echo "</action>
		<locked>0</locked>
		<seq>".$row["seq"]."</seq>
		<edit>".(($row["type"] == 1 || $row["type"] == 2) ? "1" : "0")."</edit>
		<move>1</move>
	</tab>";

}

$DB->freeResults();

$file->footer();
?>