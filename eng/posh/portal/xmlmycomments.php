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
# latest comments
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlmycomments.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("comments");

$p = isset($_GET["p"])?$_GET["p"]:0;

$DB->getResults($xmlmycomments_getComments,$DB->escape($_SESSION['user_id']),$DB->escape($p*20));

while ($row = $DB->fetch(0))
{
	echo "<item>";
	echo "<articleid>".$row["article_id"]."</articleid>";
	echo "<message><![CDATA[".$row["message"]."]]></message>";
	echo "<userid>".$row["user_id"]."</userid>";
	echo "<user><![CDATA[".$row["long_name"]."]]></user>";
	echo "</item>";
}
$DB->freeResults();

$file->footer("comments");

$DB->close();
?>