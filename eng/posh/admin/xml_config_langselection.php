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
# POSH languages management - 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_config_langselection.php";
$errLog="";
$tabname="configstab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("lang");

/*
$DB->getResults($config_getTemplate);
$row=$DB->fetch(0);
$template=$row["value"];
$DB->freeResults();
*/

$handle=opendir('../../app/exposh/l10n/');
$inc=0;

while ($item = readdir($handle))
{
	if ($item!=".." && $item!="." && is_dir('../../app/exposh/l10n/'.$item) && strlen($item)==2)
    {
        (in_array($item,$__AVLANGS))?$check=1:$check=0;
        ($__AVLANGS[0]==$item)?$select=1:$select=0;
        echo "<language>";
        echo "<item>".$item."</item>";
        echo "<inc>".$inc."</inc>";
        echo "<check>".$check."</check>";
        echo "<select>".$select."</select>";
        echo "</language>";
		$inc++;
	}
}

closedir($handle);

$file->footer();
?>