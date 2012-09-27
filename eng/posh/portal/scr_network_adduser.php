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
# Add a user in my network
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder     = "";
$not_access = 1;
$isScript   = true;
$isPortal   = false;
$pagename   = "portal/scr_network_adduser.php";
$granted    = "I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header();

$id = isset($_POST["id"]) ? $_POST["id"] : 0;
$act = isset($_POST["act"]) ? $_POST["act"] : "";
$desc = isset($_POST["desc"]) ? $_POST["desc"] : "";
$kw = isset($_POST["kw"]) ? $_POST["kw"] : "";
$kwformated = isset($_POST["kwformated"]) ? $_POST["kwformated"] : "";

if ( $act == "add" )
{
	//add new user in network
	$DB->execute($scrnetworkadduser_addUser,
                    $DB->escape($_SESSION['user_id']),
                    $DB->escape($id),
                    $DB->noHTML($desc)
    );
    //create an alert about this user adding
    $DB->execute($scrAlertAdd,
                    $DB->escape($id),
                    '1',
                    $DB->escape($_SESSION['user_id']),
                    $DB->quote($_SESSION['longname'])
    );
}
else
{
	//update user information in my network
	$DB->execute($scrnetworkadduser_updateUser,$DB->noHTML($desc),$DB->escape($_SESSION['user_id']),$DB->escape($id));

	$DB->execute($scrnetworkadduser_removeKeywords,$DB->escape($_SESSION['user_id']),$DB->escape($id));
}

//add keywords linked to user
if ($_POST["kw"]!="")
{
	$keyword=explode(",",$kw);
	$keywordSimplified=explode(",",$kwformated);
	for ($i=0;$i<count($keyword);$i++)
	{
		$selkw=$keywordSimplified[$i];
		$DB->getResults($scrnetworkadduser_getKeyword,$DB->noHTML($selkw));
		if ($DB->nbResults()==0)
		{
			$DB->execute($scrnetworkadduser_addNewKeyword,$DB->noHTML($keyword[$i]),$DB->noHTML($selkw));
			$kwid=$DB->getId();
		}
		else
		{
			$row = $DB->fetch(0);
			$kwid=$row["id"];
		}
		$DB->freeResults();

		$DB->execute($scrnetworkadduser_insertKeyword,$DB->escape($_SESSION['user_id']),$DB->escape($id),$kwid);
	}
}
$DB->close();

$file->status(1);
$file->returnData($id);

$file->footer();
?>