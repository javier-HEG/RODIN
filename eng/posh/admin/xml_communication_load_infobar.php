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
# POSH load the information bar  
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_communication_load_infobar.php";
$tabname="commtab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("comm");

$DB->getResults($communication_getConfig);
while ($row=$DB->fetch(0))
{
	if ($row["parameter"]=="bartype") { 
        $bartype=$row["value"]; 
        echo "<bartype>".$bartype."</bartype>";
    }
	if ($row["parameter"]=="rssinfo") {
        $rssinfo=$row["value"];
        echo "<rssinfo><![CDATA[".$rssinfo."]]></rssinfo>";
    }
	if ($row["parameter"]=="bartexthtml") {
        $texthtml=$row["value"];
        $texthtml=str_replace("<br>","\r\n",$texthtml);
        $texthtml=eregi_replace("<[^\<]+>","",$texthtml);
        $texthtml=str_replace("&amp;","&",$texthtml);
        $texthtml=str_replace("&nbsp; ","  ",$texthtml);
        echo "<texthtml><![CDATA[".$texthtml."]]></texthtml>";
    }
	if ($row["parameter"]=="barclosing") {
        $barclosing=$row["value"];
        echo "<barclosing><![CDATA[".$barclosing."]]></barclosing>";
    }
}
$DB->freeResults();

$file->footer();
?>