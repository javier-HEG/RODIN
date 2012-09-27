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
# list similar tags
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$tag=(isset($_GET["tag"]))?$_GET["tag"]:exit();

$folder="";
$not_access=0;
$pagename="portal/xmlautocompletion.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("tags");

$DB->getResults($xmlautocompletion_get,$DB->quote($tag."%"));
if ($DB->nbResults()==0)
{
	echo "<notag />";
}
else
{
	while ($row=$DB->fetch(0))
	{
		echo "<tag>";
		echo "<id>".$row["id"]."</id>";
		echo "<label><![CDATA[".$row["label"]."]]></label>";
		echo "</tag>";
	}
}
$DB->freeResults();

$file->footer("tags");

$DB->close();
?>