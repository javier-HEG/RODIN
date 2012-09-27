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
# POSH Users management - XML List of all the users
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

header("content-type: application/xml"); 
$folder="../portal/";
$not_access=0;
$pagename="admin/rss_modulestovalidate.php";
//includes
require('includes.php');
if (!isset($_GET["k"]) || $_GET["k"]!=__KEY) exit();

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';
echo '<rss version="2.0" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"><channel>';

echo '<title>Latest modules to validate</title>';

$DB->getResults("SELECT temp_dir_item.id,name,long_name,creation_date FROM temp_dir_item, users WHERE status='N' AND editor_id=users.id ORDER BY id DESC");
while ($row = $DB->fetch(0)){
	echo '<item>';
	echo '<title><![CDATA['.$row["name"].' ('.$row["long_name"].')]]></title>';
	echo '<link>'.__LOCALFOLDER.'/admin/index.php?noplink=1&amp;id='.$row["id"].'</link>';
	echo '<description />';
	echo '<pubDate>'.$row["creation_date"].'</pubDate>';
	echo '<guid isPermaLink="false">http://nolink/admin'.$row["id"].'</guid>';
	echo '</item>';
}
$DB->freeResults();
?>
</channel></rss>