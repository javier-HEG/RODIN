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
# POSH get general settings values
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_config_general_get_values.php";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("config");

$nb=0;
$DB->getResults($config_getParameters);
while ($row=$DB->fetch(0))
{
    $parameter=$row['parameter'];
    $value=$row['value'];
    $category=$row['category'];
    $datatype=$row['datatype'];
    
    if ($parameter=="txtnote") {
        $value=str_replace("&lt;","<",$value);
        $value=str_replace("&gt;",">",$value);
        $value=str_replace("<br>","\r\n",$value);
        $value=str_replace("&amp;","&",$value);
        $value=str_replace("&nbsp; ","  ",$value);
    }
    
    echo "<parameter>";
    echo "<label>".$parameter."</label>";
    echo "<datatype>".$datatype."</datatype>";
    echo "<valeur><![CDATA[".$value."]]></valeur>";
    echo "<category>".$category."</category>";
    echo "</parameter>";
}
$DB->freeResults();

$file->footer("config");
?>