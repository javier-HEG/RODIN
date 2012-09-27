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
# Favorites module PHP scripts
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

header("content-type: application/xml"); 
$folder="";
$not_access=1;
$pagename="modules/wid_addressbook.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

$act=$_POST["act"];
$id=$_POST["modid"];

if ($act=="sup")
{
	$DB->execute($widAddr_RemoveContact,$DB->escape($_POST["addid"]),$DB->escape($_SESSION['user_id']));
	if ($DB->nbAffected()==0)
	{
		$DB->execute($widAddr_createNewId,$DB->escape($_SESSION['user_id']));
		$oldid=$id;
		$id=$DB->getId();
		$DB->execute($widAddr_copyContact,$id,$DB->escape($oldid),$DB->escape($_POST["addid"]));
		echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
	}
}
if ($act=="add")
{
	$addid=$_POST["addid"];
	if ($id==0)
	{
		$DB->execute($widAddr_addNewContactId,$DB->escape($_SESSION['user_id']));
		$id=$DB->getId();
		$DB->execute($widAddr_addNewContact,$DB->escape($id),$DB->noHTML($_POST["fn"]),$DB->noHTML($_POST["ln"]),$DB->noHTML($_POST["m"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["f"]),$DB->noHTML($_POST["p1"]),$DB->noHTML($_POST["p2"]),$DB->noHTML($_POST["o"]),$DB->noHTML($_POST["tags"]));
	}
	else
	{
		//edition or adding
		if ($addid=="")
		{
			$DB->execute($widAddr_addNewContactOnExistingMod,$DB->escape($id),$DB->noHTML($_POST["fn"]),$DB->noHTML($_POST["ln"]),$DB->noHTML($_POST["m"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["f"]),$DB->noHTML($_POST["p1"]),$DB->noHTML($_POST["p2"]),$DB->noHTML($_POST["o"]),$DB->noHTML($_POST["tags"]),$DB->escape($id),$DB->escape($_SESSION['user_id']));
		}
		else
		{
			$DB->execute($widAddr_updateContact,$DB->noHTML($_POST["fn"]),$DB->noHTML($_POST["ln"]),$DB->noHTML($_POST["m"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["f"]),$DB->noHTML($_POST["p1"]),$DB->noHTML($_POST["p2"]),$DB->noHTML($_POST["o"]),$DB->noHTML($_POST["tags"]),$DB->escape($addid),$DB->escape($_SESSION['user_id']));
		}
		if ($DB->nbAffected()==0)
		{
			$DB->execute($widAddr_createNewMod,$DB->escape($_SESSION['user_id']));
			$oldid=$id;
			$id=$DB->getId();
			$DB->execute($widAddr_insertExistingContact,$DB->escape($id),$DB->escape($oldid));
			if ($addid=="")
			{
				$DB->execute($widAddr_newModInsertContact,$DB->escape($id),$DB->noHTML($_POST["fn"]),$DB->noHTML($_POST["ln"]),$DB->noHTML($_POST["m"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["f"]),$DB->noHTML($_POST["p1"]),$DB->noHTML($_POST["p2"]),$DB->noHTML($_POST["o"]),$DB->noHTML($_POST["tags"]));
			}
			else
			{
				$DB->execute($widAddr_newModUpdateContact,$DB->noHTML($_POST["fn"]),$DB->noHTML($_POST["ln"]),$DB->noHTML($_POST["m"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["f"]),$DB->noHTML($_POST["p1"]),$DB->noHTML($_POST["p2"]),$DB->noHTML($_POST["o"]),$DB->noHTML($_POST["tags"]),$DB->escape($addid));
			}
		}
	}
	echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
}
if ($act=="get")
{
	$id=$_POST["modid"];
	$DB->getResults($widAddr_getAddressBook,$DB->escape($id),$DB->escape($_SESSION['user_id']));
	while ($row = $DB->fetch(0))
	{
		echo '<addr>';
		echo '<add_id>'.$row["add_id"].'</add_id>';
		echo '<firstname><![CDATA['.$row["firstname"].']]></firstname>';
		echo '<lastname><![CDATA['.$row["lastname"].']]></lastname>';
		echo '<email><![CDATA['.$row["email"].']]></email>';
		echo '<company><![CDATA['.$row["company"].']]></company>';
        echo '<func><![CDATA['.$row["func"].']]></func>';
        echo '<phone1><![CDATA['.$row["phone1"].']]></phone1>';
        echo '<phone2><![CDATA['.$row["phone2"].']]></phone2>';
        echo '<other><![CDATA['.$row["other"].']]></other>';
        echo '<tags><![CDATA['.$row["tags"].']]></tags>';
		echo '</addr>';
	}
	$DB->freeResults();
}
$DB->close();

$file->status("1");

$file->footer();
?>