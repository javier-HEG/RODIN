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
# XML chat archive discussions
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlchat_archive.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("chats");

$DB->getResults($xmlchat_getArchive,$DB->escape($_SESSION['user_id']),$DB->escape($_SESSION['user_id']),($_GET["p"]*20));
while($row=$DB->fetch(0))
{
	echo '<chat>'
	.'<id>'.$row["id"].'</id>'
	.'<title><![CDATA['.$row["title"].']]></title>'
	.'<pubdate>'.$row["pubdate"].'</pubdate>'
	.'<username><![CDATA['.($row["id1"]==$_SESSION['user_id']?$row["name2"]:$row["name1"]).']]></username>'
	.'</chat>';
}

$DB->freeResults();

$file->footer();

$DB->close();
?>