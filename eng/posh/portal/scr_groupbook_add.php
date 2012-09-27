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
# Add a new notebook group
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_groupbook_add.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header();

$id = isset($_POST["id"]) ? $_POST["id"] : -1;
$act = isset($_POST["act"]) ? $_POST["act"] : "";
$name = isset($_POST["name"]) ? $_POST["name"] : exit('group name not defined');

if ($act=="add")
{
    $desc = isset($_POST["desc"]) ? $_POST["desc"] : "";
    $pv = isset($_POST["pv"]) ? $_POST["pv"] : "";

	$DB->getResults($xmlgroupbook_getByName, strtoupper($DB->quote($name)));

	$row = $DB->fetch(0);
    $exist = $row["exist"];
	$DB->freeResults();

	if ($exist == 0) {
		//add new user in network
		$DB->execute($scrgroupbook_add,
                        $DB->quote($name),
                        $DB->quote($desc),
                        $DB->escape($_SESSION['user_id']),
                        $DB->escape($pv));

		$id = $DB->getId();

		$DB->execute($scrgroupbook_map_add,
                        $DB->escape($_SESSION['user_id']),
                        $DB->escape($id),
                        $DB->quote("O"));
	}
}
else if ($act=="del" || $act == 'quit') {
	$DB->execute($scrgroupbook_map_remove,
                    $DB->escape($_SESSION['user_id']),
                    $DB->escape($id));
}
else if ($act=="join") {
    //update group status for a user
	$DB->execute($scrgroupbook_map_update,
                    $DB->quote("O"),
                    $DB->escape($_SESSION['user_id']),
                    $DB->escape($id));
}
else if ($act=="selectnjoin") {
    $DB->getResults($scrgroup_countuser, $DB->escape($_SESSION['user_id']), $DB->escape($id));
    $userExistingInGroup = $DB->nbResults();
	$DB->freeResults();

    if ($userExistingInGroup == 0)
    {
        //add user in group
    	$DB->execute($scrgroupadd_join,
                        $DB->escape($_SESSION['user_id']),
                        $DB->quote("O"),
                        $DB->escape($id));
    }
}
//else if ($act=="quit") {
//	$DB->execute($scrgroupbook_map_update, $DB->quote("X"), $DB->escape($_SESSION['user_id']), $DB->escape($id));
//}

$DB->close();

$file->status(1);

$file->returnData($id);

$file->footer();
?>