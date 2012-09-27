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
# Search in notebooks (
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$keywords = isset($_GET["searchtxt"]) ? $_GET["searchtxt"] : exit();
$page = isset($_GET["p"]) ? $_GET["p"] : exit();

$folder = "";
$not_access = 1;
$granted = "I";
$pagename = "portal/xmlnotebook_search.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file = new xmlFile();

$file->header("results");
if ($page < 1000)
{
	//get the modules corresponding to the request based on the indexed keywords
    $allkeywords = explode(" ", $keywords);
    $list_keywords ="";
    foreach ($allkeywords as $k) {
        $k = trim($k);
        $list_keywords .= $DB->quote($k). ',';
    }
    $list_keywords .= $DB->quote($keywords);
    
	if ($_GET["type"] == 1)
	{
        $DB->getResults($xmlnotebooksearch_mynotebooksearch,
                            $list_keywords,
                            $DB->escape($_SESSION['user_id']),
                            $DB->escape($page*10));
	}
    // only in my notebook
	else if ($_GET["type"] == 2)
	{
		$DB->getResults($xmlnotebooksearch_otherNotebookSearch,
                            $list_keywords,
                            $DB->escape($_SESSION['user_id']),
                            $DB->escape($page*10));
	}
    else
    {
        $DB->getResults($xmlnotebooksearch_allNotebookSearch,
                            $list_keywords,
                            $DB->escape($_SESSION['user_id']),
                            $DB->escape($_SESSION['user_id']),
                            $DB->escape($page*10));
        
    }

	$nb_search = $DB->nbResults();
	echo "<nbres>".$nb_search."</nbres>";
	if ($nb_search > 0)
	{
		while ($row = $DB->fetch(0))
		{
			echo "<item>";
			echo "<id>".$row["id"]."</id>";
			echo "<title><![CDATA[".$row["title"]."]]></title>";
			echo "<date>".$row["pubdate"]."</date>";
			echo "<picture><![CDATA[".$row["picture"]."]]></picture>";
			echo "<owner><![CDATA[".$row["long_name"]."]]></owner>";
			echo "<ownerid>".$row["user_id"]."</ownerid>";
			echo "</item>";
		}
	}
	$DB->freeResults();
}
$file->footer("results");

$DB->close();
?>
