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
# POSH Modules management - XML List of modules searched
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

if (!isset($_GET["searchtxt"])) exit();
if (!isset($_GET["p"])) exit();

$folder="";
$not_access=0;
$pagename="admin/xmlsearch.php";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("results");

$motcle=$_GET["searchtxt"];
$page=$DB->escape($_GET["p"]);
$empty=true;

if (strlen($motcle)>2) $empty=false;

if ($empty) {
	echo "<error>Keyword incorrect or too short (3 char. min)</error>";
}
else {
	if ($page<1000) {
		//get the modules corresponding to the request based on the indexed keywords
		$kw=str_replace(" ","','",$motcle)."','".$motcle;
        if($_SESSION['user_id']>1) { 
            $DB->getResults($module_searchModuleAllowed,"'".$kw."'",$DB->escape($_SESSION['user_id']),($page*21));
        }
        else {
            $DB->getResults($module_searchModule,"'".$kw."'",($page*21));
        }
		$nb_search = $DB->nbResults();
		echo "<nbres>".$nb_search."</nbres>";
		$inc=1;
		if ($nb_search>0) {
			while ($row = $DB->fetch(0))
			{
				echo "<item>";
				echo "<id>".$row["id"]."</id>";
				echo "<name><![CDATA[".$row["name"]."]]></name>";
				echo "</item>";
			}
		}
		$DB->freeResults();
	}
}

$file->footer("results");
?>