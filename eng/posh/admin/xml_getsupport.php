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
# POSH Admin - load the support information (adm_log)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/xml_getsupport.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("support");

$page=(isset($_GET["page"])?$_GET["page"]:0);
$DB->getResults($xml_getsupport,$DB->escape($page*100));
$total=$DB->nbResults();
echo "<total>".$total."</total>";
while ($row=$DB->fetch(0))
{
    $id=$row["id"];
    $log=$row["log"];
    $logdate=$row["logdate"];
    echo "<logs>";
    echo "<id>".$id."</id>";
    echo "<log><![CDATA[".$log."]]></log>";
    echo "<logdate>".$logdate."</logdate>";
    echo "</logs>";
}

$DB->freeResults();

$file->footer();
?>