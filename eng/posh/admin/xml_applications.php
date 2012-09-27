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
# POSH applications management - XML List of all the applications
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_applications.php";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("applications");

$p=$_GET["p"];

$DB->getResults($applications_getApplications,$DB->escape(($p-1)*21));
while ($row = $DB->fetch(0))
{
	echo "<application>
	<id>".$row["id"]."</id>
	<title><![CDATA[".$row["title"]."]]></title>
	<icon>".$row["icon"]."</icon>
	<action><![CDATA[".$row["action"]."]]></action>
	</application>";
}
$DB->freeResults();

$file->footer();
?>