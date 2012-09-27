<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of COLLABORATION SUITE of POSH http://sourceforge.net/projects/posh/.

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
# Remove a user from my network
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_network_updatemyprofile.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header();

$status=$_POST["stat"];

//update status in profile
if ($DB->execute($scrnetworkupdatemyprofile_update,$DB->noHTML($status),$DB->escape($_SESSION['user_id'])))
{
	$file->status(1);
	//add in news feed
	$DB->execute($xmlnetworknews_insertNews,$DB->escape($_SESSION['user_id']),"'6'",$DB->noHTML($status),"''","'3'");
	//add in notebook
	$DB->execute($scrnotebookarticleadd_addArticle,$DB->noHTML($status),'""','""',0,6,0,3);

	if ($DB->nbAffected()!=0)
	{
		$noteid=$DB->getId();

		$DB->execute($scrnotebookarticleadd_addLink,$DB->escape($_SESSION['user_id']),$DB->escape($noteid),$DB->escape($_SESSION['user_id']),$DB->escape($_SESSION['user_id']));
	}
}
else
{
	$file->status(0);
}

$file->footer();

$DB->close();
?>