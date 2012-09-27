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
# Users tabs
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=0;
$pagename="portal/xmlmostread.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("articles");
	
//get the articles information
$DB->getResults($xmlmostread_getArticles,$DB->escape($_GET["p"]*20));
while($row = $DB->fetch(0))
{
	echo '<article>
		<id>'.$row["id"].'</id>
		<title><![CDATA['.$row["article"].']]></title>
		<link><![CDATA['.$row["link"].']]></link>
		<source><![CDATA['.$row["source"].']]></source>
		<sourceid><![CDATA['.$row["sourceid"].']]></sourceid>
		<nbread>'.$row["nb"].'</nbread>
	</article>';
}
$DB->freeResults();

$file->footer();

$DB->close();
?>