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
# Users pages - latest RSS news
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlpages_latestnews.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("news");
$DB->getResults($xmlpages_getLatestNewsNb,$DB->escape($_SESSION["user_id"]));

$article_nb=$DB->nbResults();
if ($_GET["f"]==0)
{
	$DB->getResults($xmlpages_getLatestNews,$DB->escape($_SESSION["user_id"]),($_GET["p"]*10),10);
}
else
{
	$DB->getResults($xmlpages_getLatestNewsFiltered,$DB->escape($_SESSION["user_id"]),$DB->escape($_GET["f"]),($_GET["p"]*10),10);
}

echo'<total>'.'<max>'.$article_nb.'</max>'.'</total>';

while ($row=$DB->fetch(0))
{
	echo'<article>'.
		'<id>'.$row["aid"].'</id>'.
		'<title><![CDATA['.$row["atitle"].']]></title>'.
		'<feedid>'.$row["fid"].'</feedid>'.
		'<feed><![CDATA['.$row["ftitle"].']]></feed>'.
		'<link><![CDATA['.$row["link"].']]></link>'.
        '<iconid><![CDATA['.$row["iconid"].']]></iconid>'.
	'</article>';
}

$DB->freeResults();

$file->footer();

$DB->close();
?>