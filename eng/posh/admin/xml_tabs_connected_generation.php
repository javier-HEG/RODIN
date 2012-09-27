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
# POSH Pages management - Cache XML List of tabs
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=0;
//includes
require('includes.php');

$DB->getResults($pages_getConnectedTabs);
while($row = $DB->fetch(0))
{
	$id=$row["id"]+1000000000;
	echo "<tab>
		<number>".$id."</number>
		<name><![CDATA[".$row["name"]."]]></name>
		<type>".$row["type"]."</type>
		<action>"; 
	if ($row["type"]==1)
	{
		echo "$"."p.app.pages.change(".$id.")";
	}
	else if ($row["type"]==2)
	{
		echo "$"."p.app.pages.frame('".$row["param"]."',".$id.")";
	}
    else if ($row["type"]==4)
	{
		echo "$"."p.app.pages.redirect('".$row["param"]."',".$id.")";
	}
	else
	{
		echo $row["param"];
	};
	echo "</action>
		<locked>0</locked>
		<seq>0</seq>
		<icon><![CDATA[".$row["icon"]."]]></icon>
		<edit>0</edit>
		<move>0</move>
	</tab>";
}
$DB->freeResults();
?>