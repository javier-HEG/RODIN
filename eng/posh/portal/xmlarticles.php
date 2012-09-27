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
# generate the XML users saved articles
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlarticles.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("articles");

$start = (isset($_GET["start"]))?$_GET["start"]:0;
$nb = (isset($_GET["nb"]))?$_GET["nb"]:10;

//get the user articles
$DB->getResults($xmlarticles_getArticles,$DB->escape($_SESSION['user_id']),$DB->escape($start),$DB->escape($nb));
$inc=0;
while($row = $DB->fetch(0))
{
	echo "<article>";
	echo "<id>".$row["id"]."</id>";
	echo "<title><![CDATA[".$row["title"]."]]></title>";
	echo "<link><![CDATA[".$row["link"]."]]></link>";
	echo "<source><![CDATA[".$row["source"]."]]></source>";
	echo "<icon><![CDATA[".$row["icon"]."]]></icon>";
	echo "<feedarticle_id>".$row["feedarticle_id"]."</feedarticle_id>";
	echo "<pubdate>".$row["pubdate"]."</pubdate>";
	echo "</article>";
}
$DB->freeResults();

$file->footer();

$DB->close();
?>