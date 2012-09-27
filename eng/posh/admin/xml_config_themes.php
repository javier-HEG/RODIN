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
# POSH get themes 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_config_themes.php";
$tabname="configstab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("themes");

$DB->getResults($config_getTemplate);
$row=$DB->fetch(0);
$template=$row["value"];
$DB->freeResults();

$handle=opendir('../templates/');
while ($item = readdir($handle))
{
	if ($item!=".." && $item!="." && is_file('../templates/'.$item."/index.php")) {
        ($item==$template)?$check=1:$check=0;
        echo "<templates>";
        echo "<item>".$item."</item>";
        echo "<check>".$check."</check>";
        echo "</templates>";
   }
}
closedir($handle);


$handle=opendir('../styles/themes/');
while ($myfile = readdir($handle))
{
	if (substr($myfile,-4)=='.thm')   {
		$name=substr($myfile,0,strlen($myfile)-4);
		echo "<filetheme>";
        echo "<filename>".$name."</filename>";
        echo "</filetheme>";
	}
}
closedir($handle);


$DB->getResults($configtheme_getThemes);
while ($row=$DB->fetch(0))
{
    $name=$row['name'];
    echo "<theme>";
    echo "<name>".$name."</name>";
    echo "</theme>";
}
$DB->freeResults();

$file->footer();

?>
