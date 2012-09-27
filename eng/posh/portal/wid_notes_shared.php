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
# Notes modules PHP scripts
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

header("content-type: application/xml");
$folder="";
$not_access=1;
$pagename="portal/wid_notes_shared.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'><channel><status>1</status>';
$notes = isset($_POST["notes"])?$_POST["notes"]:"";
$noteId = isset($_POST["noteid"])?$_POST["noteid"]:-1;
if ($noteId==0)
{
	$DB->execute($widnoteshared_newNote,$DB->quote($notes));
	echo '<ret>'.$DB->getId().'</ret>';
}
else
{
	$DB->execute($widnoteshared_updNote,$DB->quote($notes),$DB->escape($noteId));
}

$DB->close();
echo '</channel>';
?>