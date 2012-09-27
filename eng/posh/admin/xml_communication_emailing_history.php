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
# POSH Communication email history - XML List of mail
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_communication_emailing_history.php";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("history");

$DB->getResults($communication_sentEmails);
echo "<nb>".$DB->nbResults()."</nb>";
if ($DB->nbResults()!=0) {
	while ($row=$DB->fetch(0))
	{
        echo "<contact>";
        echo "<maildate>".date("d/m/Y",$row["date"])."</maildate>";
        echo "<id>".$row["id"]."</id>";
        echo "<subject>".$row["subject"]."</subject>";
        echo "<sender>".$row["sender"]."</sender>";
        echo "<receiver>".$row["receiver"]."</receiver>";
        echo "</contact>";	
    }
}
$DB->freeResults();

$file->footer();
?>