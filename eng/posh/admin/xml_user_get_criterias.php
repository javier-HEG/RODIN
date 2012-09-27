<?php
# ************** LICENCE *******************************************************
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
# *********************************************************************************
# POSH User management - User modification and adding form
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# *********************************************************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_user_get_criterias.php";
$info="";
$tabname="userstab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();
$file->header("useradd");

$DB->getResults($criteria_getInformations);
$nb=$DB->nbResults();
echo "<nb>".$nb."</nb>";
while ($row=$DB->fetch(0))
{
    $id=$row["id"];
    $type=$row["type"];
    $label=$row["label"];
    $options=$row["options"];
    $mandatory=$row["mandatory"];
    echo "<criteria>";
    echo "<id>".$id."</id>";
    echo "<type>".$type."</type>";
    echo "<label><![CDATA[".$label."]]></label>";
    echo "<options><![CDATA[".$options."]]></options>";
    echo "<mandatory>".$mandatory."</mandatory>";
    echo "</criteria>";
}
$DB->freeResults();

$file->footer();
?>