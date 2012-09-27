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
# Save rss article status (read/unread)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_feed_changestatus.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');
launch_hook('scr_feed_changestatus');

$file=new xmlFile();
$file->header();
$inc=0;

if (isset($_POST['delete']) && $_POST['delete']==1 )
{
	$idArt = $_POST['artId'];
	$idMod = $_POST['v_mod'];
	
	$DB->getResults($xmlfeeds_getItemStatus,$DB->escape($_SESSION["user_id"]),$DB->escape($idArt));
	$row = $DB->fetch(0);
	
	//If the status is 1 (read article), the status is updated
	if ($row['status']==1)
		$DB->execute($xmlfeeds_updateItemStatus,$DB->escape($_SESSION["user_id"]),$DB->escape($idArt));
	//otherwise the query is an insert
	else
		$DB->execute($xmlfeeds_setAsDelete,$DB->escape($idArt),$DB->escape($_SESSION["user_id"]));
	
	$DB->freeResults();
	$file->returnData($idMod);
}
else
{
	while (isset($_POST["s".$inc]))
	{
		$newStatus=$_POST["s".$inc];

		if ($newStatus==1)
			$DB->execute($scrfeed_setAsRead,$DB->escape($_POST["artid".$inc]),$DB->escape($_SESSION['user_id']));
		else
			$DB->execute($scrfeed_setAsUnread,$DB->escape($_POST["artid".$inc]),$DB->escape($_SESSION['user_id']));	
		
		$inc++;
	}
}

$file->status(1);
$file->footer();
$DB->close();
?>