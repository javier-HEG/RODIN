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
$pagename="portal/wid_addressbook.php";
$granted="I";
//includes
require_once('includes.php');

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'><channel><status>1</status>';

$act=$_POST["act"];
$id=$_POST["modid"];

if ($act=="sup")
{
	$DB->execute("DELETE FROM widget_addressbook AS a,widget_addressbook_id AS b WHERE add_id=%u AND a.id=b.id AND user_id=%u ",$DB->escape($_POST["addid"]),$DB->escape($_SESSION['user_id']));
	if ($DB->nbAffected==0)
	{
		$DB->execute("INSERT INTO widget_addressbook_id (user_id,status) VALUES(%u,'A') ",$DB->escape($_SESSION['user_id']));
		$oldid=$id;
		$id=$DB->getId();
		$DB->execute("INSERT INTO widget_addressbook (id,firstname,lastname,email,company,func,phone1,phone2,other,tags) SELECT %u,firstname,lastname,email,company,func,phone1,phone2,other,tags FROM widget_addressbook WHERE id=%u AND add_id!=%u ",$id,$DB->escape($oldid),$DB->escape($_POST["addid"]));
		echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
	}
}
if ($act=="add")
{
	$addid=$_POST["addid"];
	if ($id==0)
	{
		$DB->execute("INSERT INTO widget_addressbook_id (user_id,status) VALUES(%u,'A') ",$DB->escape($_SESSION['user_id']));
		$id=$DB->getId();
		$DB->execute("INSERT INTO widget_addressbook (id,firstname,lastname,email,company,func,phone1,phone2,other,tags) VALUES (%u,%s,%s,%s,%s,%s,%s,%s,%s,%s) ",$DB->escape($id),$DB->noHTML($_POST["fn"]),$DB->noHTML($_POST["ln"]),$DB->noHTML($_POST["m"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["f"]),$DB->noHTML($_POST["p1"]),$DB->noHTML($_POST["p2"]),$DB->noHTML($_POST["o"]),$DB->noHTML($_POST["tags"]));
	}
	else
	{
		//edition or adding
		if ($addid=="")
		{
			$DB->execute("INSERT INTO widget_addressbook (id,firstname,lastname,email,company,func,phone1,phone2,other,tags) SELECT %u,%s,%s,%s,%s,%s,%s,%s,%s,%s FROM widget_addressbook_id WHERE id=%u AND user_id=%u ",$DB->escape($id),$DB->noHTML($_POST["fn"]),$DB->noHTML($_POST["ln"]),$DB->noHTML($_POST["m"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["f"]),$DB->noHTML($_POST["p1"]),$DB->noHTML($_POST["p2"]),$DB->noHTML($_POST["o"]),$DB->noHTML($_POST["tags"]),$DB->escape($id),$DB->escape($_SESSION['user_id']));
		}
		else
		{
			$DB->execute("UPDATE widget_addressbook,widget_addressbook_id SET firstname=%s,lastname=%s,email=%s,company=%s,func=%s,phone1=%s,phone2=%s,other=%s,tags=%s WHERE add_id=%u AND widget_addressbook.id=widget_addressbook_id.id AND user_id=%u ",$DB->noHTML($_POST["fn"]),$DB->noHTML($_POST["ln"]),$DB->noHTML($_POST["m"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["f"]),$DB->noHTML($_POST["p1"]),$DB->noHTML($_POST["p2"]),$DB->noHTML($_POST["o"]),$DB->noHTML($_POST["tags"]),$DB->escape($addid),$DB->escape($_SESSION['user_id']));
		}
		if ($DB->nbAffected()==0)
		{
			$DB->execute("INSERT INTO widget_addressbook_id (user_id,status) VALUES(%u,'A') ",$DB->escape($_SESSION['user_id']));
			$oldid=$id;
			$id=$DB->getId();
			$DB->execute("INSERT INTO widget_addressbook (id,firstname,lastname,email,company,func,phone1,phone2,other,tags) SELECT %u,firstname,lastname,email,company,func,phone1,phone2,other,tags FROM widget_addressbook WHERE id=%u ",$DB->escape($id),$DB->escape($oldid));
			if ($addid=="")
			{
				$DB->execute("INSERT INTO widget_addressbook (id,firstname,lastname,email,company,func,phone1,phone2,other,tags) VALUES (%u,%s,%s,%s,%s,%s,%s,%s,%s,%s) ",$DB->escape($id),$DB->noHTML($_POST["fn"]),$DB->noHTML($_POST["ln"]),$DB->noHTML($_POST["m"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["f"]),$DB->noHTML($_POST["p1"]),$DB->noHTML($_POST["p2"]),$DB->noHTML($_POST["o"]),$DB->noHTML($_POST["tags"]));
			}
			else
			{
				$DB->execute("UPDATE widget_addressbook SET firstname=%s,lastname=%s,email=%s,company=%s,func=%s,phone1=%s,phone2=%s,other=%s,tags=%s WHERE add_id=%u ",$DB->noHTML($_POST["fn"]),$DB->noHTML($_POST["ln"]),$DB->noHTML($_POST["m"]),$DB->noHTML($_POST["c"]),$DB->noHTML($_POST["f"]),$DB->noHTML($_POST["p1"]),$DB->noHTML($_POST["p2"]),$DB->noHTML($_POST["o"]),$DB->noHTML($_POST["tags"]),$DB->escape($addid));
			}
		}
	}
	echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
}

$DB->close();

echo "</channel>";
?>