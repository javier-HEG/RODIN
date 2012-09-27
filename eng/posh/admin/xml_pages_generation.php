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
# POSH Users management - Cache XML List of tabs
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=0;
$pagename="admin/xml_pages_generation.php";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile(false);

$file->header("page");

$DB->getResults($pagegeneration_getPage,$DB->escape($_GET["id"]));
$row = $DB->fetch(0);
echo "<name><![CDATA[".$row["name"]."]]></name>\n";
echo "<type>".$row["type"]."</type>\n";
echo "<modulealign>".$row["modulealign"]."</modulealign>\n";
echo "<ctrl>".$row["controls"]."</ctrl>\n";
echo "<param><![CDATA[".$row["param"]."]]></param>\n";
echo "<nbcol>".$row["nbcol"]."</nbcol>\n";
echo "<showtype>".$row["showtype"]."</showtype>\n";
echo "<npnb>".$row["npnb"]."</npnb>\n";
echo "<style>".$row["style"]."</style>\n";
$DB->freeResults();

$DB->getResults($pagegeneration_getPageModules,$DB->escape($_GET["id"]));
while($row = $DB->fetch(0))
{

	echo "<module>
	<col>".$row["posx"]."</col>
	<pos>".$row["posy"]."</pos>
	<posj>".$row["posj"]."</posj>
	<x>".$row["x"]."</x>
	<y>".$row["y"]."</y>
	<height>".$row["height"]."</height>
	<id>".$row["item_id"]."</id>
	<site><![CDATA[".$row["website"]."]]></site>
	<name><![CDATA[".$row["name"]."]]></name>
	<vars><![CDATA[".$row["variables"]."]]></vars>
	<minmodsize>".$row["minwidth"]."</minmodsize>
	<updmodsize>".$row["sizable"]."</updmodsize>
	<url><![CDATA[".$row["url"]."]]></url>
	<uniq>".$row["uniq"]."</uniq>
	<format>".$row["format"]."</format>
	<nbvars>".$row["nbvariables"]."</nbvars>
	<blocked>".$row["blocked"]."</blocked>
	<minimized>".$row["minimized"]."</minimized>
	<usereader>".$row["usereader"]."</usereader>
	<autorefresh>".$row["autorefresh"]."</autorefresh>
	</module>";

}
$DB->freeResults();

$file->footer();
?>