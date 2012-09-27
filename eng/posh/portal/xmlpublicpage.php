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
# Public pages
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlpublicpage.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$id=isset($_GET["id"])?$_GET["id"]:exit();

$file=new xmlFile();

$file->header("portals");

$DB->getResults($xmlpublicpage_getPage,$DB->escape($id));

$row=$DB->fetch(0);
echo "<portal>
		<id>".$id."</id>
		<name><![CDATA[".$row["name"]."]]></name>
		<desc><![CDATA[".$row["description"]."]]></desc>
		<mode>".$row["position"]."</mode>
		<type>".$row["type"]."</type>
		<param><![CDATA[".$row["param"]."]]></param>
		<seq>".$row["seq"]."</seq>
		<nbcol>".$row["nbcol"]."</nbcol>
		<showtype>".$row["showtype"]."</showtype>
		<npnb>".$row["npnb"]."</npnb>
		<style>".$row["style"]."</style>
		<modulealign>".$row["modulealign"]."</modulealign>
		<controls>".$row["controls"]."</controls>
		<icon>".$row["icon"]."</icon>
		<removable>".$row["removable"]."</removable>";
$DB->freeResults();

$DB->getResults($xmlpublicpage_getWidgets,$DB->escape($id));
while ($row2=$DB->fetch(0))
{
	echo "<module>
		<id>".$row2["item_id"]."</id>
		<col>".$row2["posx"]."</col>
		<pos>".$row2["posy"]."</pos>
		<posj>".$row2["posj"]."</posj>
		<x>".$row2["x"]."</x>
		<y>".$row2["y"]."</y>
		<vars><![CDATA[".$row2["variables"]."]]></vars>
		<uniq>".$row2["uniq"]."</uniq>
		<blocked>".$row2["blocked"]."</blocked>
		<minimized>".$row2["minimized"]."</minimized>
	</module>";
}
$DB->freeResults();

echo "</portal>";

$file->footer();

$DB->close();
?>