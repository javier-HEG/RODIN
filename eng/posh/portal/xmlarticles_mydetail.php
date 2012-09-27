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
# get information about a user article
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlarticles_detail.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("article");

$id=$_GET["id"];

//get general information about the article
$DB->getResults($xmlarticlesmydetail_getArticle,$DB->escape($id),$DB->escape($_SESSION['user_id']));
$row=$DB->fetch(0);
echo "<title><![CDATA[".$row["title"]."]]></title>";
echo "<link><![CDATA[".$row["link"]."]]></link>";
echo "<private>".$row["private"]."</private>";
echo "<description><![CDATA[".$row["description"]."]]></description>";
echo "<icon><![CDATA[".$row["icon"]."]]></icon>";
echo "<feedarticle_id>".$row["feedarticle_id"]."</feedarticle_id>";
$DB->freeResults();

//get the keywords you set for this article
$DB->getResults($xmlarticlesdetail_getKeywords,$DB->escape($_SESSION['user_id']),$DB->escape($id));
while($row=$DB->fetch(0))
{
	echo "<keyword><![CDATA[".$row["label"]."]]></keyword>";
}
$DB->freeResults();

$file->footer("article");

$DB->close();
?>