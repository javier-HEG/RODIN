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
# Notes modules PHP scripts
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

header("content-type: application/xml");
$folder="";
$not_access=1;
$pagename="modules/wid_notes.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

if(!isset($_GET["getText"])) {
	if ($_POST["noteid"]==0) {
		$DB->execute($widnote_newNote,$DB->escape($_SESSION['user_id']),$DB->quote($_POST["notes"]));
		echo '<ret>'.$DB->getId().'</ret>';
	}
	else {
		$DB->execute($widnote_updateNote,$DB->quote($_POST["notes"]),$DB->escape($_POST["noteid"]),$DB->escape($_SESSION['user_id']));
		if ($DB->nbAffected()==0) {
			$DB->execute($widnote_addNote,$DB->escape($_SESSION['user_id']),$DB->quote($_POST["notes"]));
			echo '<ret>'.$DB->getId().'</ret>';
		}
	}
}
else {
    $noteid=$_GET["noteid"];
    $sharedmd5key=isset($_GET['sharedmd5key'])?$_GET['sharedmd5key']:'';
    $widgetid = isset($_GET['widgetid'])?$_GET['widgetid']:0;

	if ($_GET["noteid"]!=0 && isset($_SESSION['user_id'])) {
        if( (empty($sharedmd5key))
        || (!isset($sharedmd5key)) 
        || ($sharedmd5key=='undefined') ) {
            $DB->getResults($xml_getUserNotes,$DB->escape($noteid),$DB->escape($_SESSION["user_id"]));
        }
        else {
            $DB->getResults($xml_getUserNotesShared,
                            $DB->quote("%noteid=".$DB->escape($noteid)."%"),
                            $DB->quote($sharedmd5key),
                            $DB->escape($_SESSION['user_id']),
                            $DB->escape($widgetid),
                            $DB->escape($noteid)
                            );
        }
        $inc=0;
        $row = $DB->fetch(0);
		echo '<note><![CDATA['.$row["notes"].']]></note>';
        $DB->freeResults();
    }  
}

$DB->close();

$file->status(1);

$file->footer();
?>