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
# Favorites module PHP scripts
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

header("content-type: application/xml"); 
$folder="";
$not_access=1;
$pagename="portal/wid_links_shared.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'><channel><status>1</status>';

$act = isset($_POST["act"])?$_POST["act"]:"";
$id = isset($_POST["modid"])?$_POST["modid"]:-1;
$linkid = isset($_POST["linkid"])?$_POST["linkid"]:-1;
$name = isset($_POST["name"])?$_POST["name"]:"";
$link = isset($_POST["link"])?$_POST["link"]:"";

if ($act=="sup")
{
	//suppress a link
	$DB->execute($widlinkshared_removeLink,$DB->escape($linkid));
}
if ($act=="add")
{
	//add a new link
	if ($id==0) //if the widget is not yet registered for a user
	{
		$DB->execute($widlinkshared_newModId);
		$id=$DB->getId();
		$DB->execute($widlinkshared_firstLink,$DB->escape($id),$DB->noHTML($name),$DB->noHTML($link));
	}
	else
	{
		$DB->execute($widlinkshared_addLink,$DB->escape($id),$DB->noHTML($name),$DB->noHTML($link));
	}
	echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
}

$DB->close();
echo "</channel>";
?>