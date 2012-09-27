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
# POSH Module management - Directory root modifications form
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_rootdirectory_modify.php";
$tabname="modulestab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("dirModify");

$DB->getResults($module_getMainDirectory);
while($row = $DB->fetch(0))
{
    echo "<directory>";
    echo "<catid>".$row["id"]."</catid>";
    echo "<catoldid>".$row["id"]."</catoldid>";
    echo "<catname><![CDATA[".$row["name"]."]]></catname>";
    echo "<catseq>".$row["seq"]."</catseq>";
    echo "<catlang>".$row["lang"]."</catlang>";
    echo "</directory>";
}
$DB->freeResults();

$file->footer();
?>