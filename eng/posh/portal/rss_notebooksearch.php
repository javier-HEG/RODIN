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
# search in notebook (rss feed)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

header("content-type: application/xml"); 
$folder = "../portal/";
$not_access = 0;
$pagename = "admin/rss_notebooksearch.php";
//includes
require('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';
echo '<rss version="2.0" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"><channel>';

$searchtxt = $_GET["search"];

echo '<title>'.$searchtxt.'</title>';

$keyword = "'".str_replace(",","','",$searchtxt)."'";

$DB->getResults($xmlnotebooksearch_allNotebookSearch,
                    $keyword,
                    $DB->escape($_SESSION['user_id']),
                    $DB->escape($_SESSION['user_id']),
                    0);

while ($row = $DB->fetch(0)){
	echo '<item>';
	echo '<title><![CDATA['.$row["title"].']]></title>';
	echo '<link>'.__LOCALFOLDER.'notebook/detail.php?artid='.$row["id"].'</link>';
	echo '<description><![CDATA['.$row["description"].']]></description>';
	echo '<pubDate>'.$row["pubdate"].'</pubDate>';
	echo '</item>';
}
$DB->freeResults();
?>
</channel>
</rss>