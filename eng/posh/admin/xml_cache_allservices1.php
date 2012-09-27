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
# Generation of the entire module list on xml format (creation date sorting)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=0;
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

$p=mysql_real_escape_string($_GET["p"]);

echo "<page>".$p."</page>";
echo "<nbpg>".mysql_real_escape_string($_GET["nbpg"])."</nbpg>";

$DB->getResults($cache_getAllModuleXml,$DB->escape(($p-1)*7));
while ($row = $DB->fetch(0))
{
	echo "<item>";
	echo "<id>".$row["id"]."</id>";
	echo "<name><![CDATA[".$row["name"]."]]></name>";
	echo "<rank>".$row["nota"]."</rank>";
	echo "<desc><![CDATA[".$row["descr"];
	if (strlen($row["descr"])>78){echo " ...";}
	echo "]]></desc>";
	echo "</item>";
}
$DB->freeResults();

$file->footer("channel");
?>