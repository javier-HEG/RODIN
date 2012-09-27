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
# POSH User management - Group suppression form
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_group_suppress.php";
$tabname="userstab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();
$file->header("groupsupp");

$group=$_GET["group"];

$DB->getResults($users_groupNumber,$DB->escape($group));
$row = $DB->fetch(1);
$nbgroup=$row[0];
echo "<nbgroup>".$nbgroup."</nbgroup>";
$DB->freeResults();

$DB->getResults($users_getUsersNumber,$DB->escape($group));
$row = $DB->fetch(1);
$nbuser=$row[0];
echo "<nbuser>".$nbuser."</nbuser>";
$DB->freeResults();

$file->footer();
?>