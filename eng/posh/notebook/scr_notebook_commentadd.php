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
# Aadd a comment (XML return) 
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder     = "";
$not_access = 1;
$isScript   = true;
$isPortal   = false;
$pagename   = "notebook/scr_notebook_commentadd.php";
$granted    = "I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$artid = $_POST["artid"];

$file=new xmlFile();

$file->header();

$message = isset($_POST["message"]) ? $_POST["message"] : "";

//add the comment
$DB->execute($scrnotebookcommentadd_addComment,$DB->escape($artid),$DB->escape($_SESSION['user_id']),$DB->noHTML($message));
$commentId=$DB->getId();

if ($DB->nbAffected()!=0)
{
	//increment comments number
	$DB->execute($scrnotebookcommentadd_updComment,$DB->escape($artid));

	// add notification in for my profile in network
	$DB->execute($xmlnetworknews_insertNewsWithoutTitle,$DB->escape($_SESSION['user_id']),"'5'",$DB->quote("id=".$_SESSION['user_id']."&artid=".$artid),$DB->escape($artid));

    // add an alert for the owner of the notebook
    $DB->execute($scrAlertAdd,
                    $DB->escape($_POST['uid']),
                    "2",
                    $DB->escape($artid),
                    $DB->quote($_SESSION['longname'])
    );

	$file->returnData($commentId);
	$file->status(1);
}

$file->footer();

$DB->close();
?>