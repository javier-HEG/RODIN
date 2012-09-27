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
# POSH stats - compute statistics 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=0;
$isScript=false;
$isPortal=false;
//$granted="A";
$pagename="admin/scr_stats_compute.php";
$tabname="statsstab";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("statscompute");

launch_hook('admin_stats_compute');

$DB->execute($support_deleteStats);

//number of users (id 3)
$DB->execute($stats_computeNbOfUsers);

//number of portals opening(id 2)
$DB->execute($stats_computeOpenings);

//uniq visitors for the day(id 6)
$DB->execute($stats_computeVisitors);

//set action 4 = backup action 2 for month processing
$DB->execute($stats_computeArchiveForMonth);
//uniq visitors for the month(id 8)
$DB->execute($stats_computeVisitorsMonth);

//empty log
//$DB->execute($stats_removeOld);

$file->status(1);

$file->footer();
//header("location:stats_show.php");
?>