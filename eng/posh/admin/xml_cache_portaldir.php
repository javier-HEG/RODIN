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
# POSH Portals management - Cache XML List portals directories
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$pagename="admin/xml_cache_portaldir.php";
$not_access=0;
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

$id=isset($_GET["id"])?$_GET["id"]:0;

$dirtable=(__portaldirtype=="group"?"users_group":"dir_category");

//get portal subdirectories
$DB->getResults($xmlcacheportaldir_getChildren,$dirtable,$DB->escape($id));
while ($row = $DB->fetch(0))
{
	echo "<dir>";
	echo "<dirid>".$row['id']."</dirid>";
	echo "<dirname><![CDATA[".$row['name']."]]></dirname>";
	echo "</dir>";
}
$DB->freeResults();

//get directory portals
$DB->getResults($xmlcacheportaldir_getPortals,$DB->escape($id));
while ($row = $DB->fetch(0))
{
	echo "<portal>";
	echo "<id>".$row['id']."</id>";
	echo "<name>".$row['name']."</name>";
	echo "</portal>";
}
$DB->freeResults();

$file->footer("channel");

$DB->close();
?>