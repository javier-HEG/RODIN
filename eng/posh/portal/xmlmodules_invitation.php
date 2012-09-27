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
# generate the XML user modules
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=0;
$pagename="portal/xmlmodules_invitation.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("page");

$DB->sql = "SELECT dir_item.id,dir_item.url AS url,posx,posy,posj,x,y,variables,height,minwidth,sizable,dir_item.name,website,dir_item.status,uniq,format,nbvariables,usereader,autorefresh FROM dir_item, module, users ";
$DB->sql.= "WHERE module.user_id=users.id ";
$DB->sql.= "AND module.profile_id=".$_GET["p"]." AND module.item_id=dir_item.id ";
$DB->sql.= "AND users.id=".$_GET["i"]." AND LEFT(md5pass,6)='".$_GET["e"]."' ";
$DB->sql.= "ORDER BY posx,posy,uniq ";
$DB->getResults($DB->sql);

$inc=0;
$incj=0;
while($row=$DB->fetch(0))
{
	$inc++;
	if ($row["status"]=="O"){$url=$row["url"];}
	else {$url="../modules/moddef.php?id=" . $row["id"] . "&size=" . $row["height"] . "&st=" . $row["status"] . "&";}
	echo "<module>";
	echo "<col>".$row["posx"]."</col>";
	echo "<pos>".$row["posy"]."</pos>";
	echo "<posj>".$row["posj"]."</posj>";
	echo "<x>".$row["x"]."</x>";
	echo "<y>".$row["y"]."</y>";
	echo "<height>".$row["height"]."</height>";
	echo "<id>".$row["id"]."</id>";
	echo "<site><![CDATA[".$row["website"]."]]></site>";
	echo "<name><![CDATA[".$row["name"]."]]></name>";
	echo "<vars><![CDATA[".$row["variables"]."]]></vars>";
	echo "<minmodsize>".$row["minwidth"]."</minmodsize>";
	echo "<updmodsize>".$row["sizable"]."</updmodsize>";
	echo "<url>".$url."</url>";
	echo "<uniq>".$row["uniq"]."</uniq>";
	echo "<format>".$row["format"]."</format>";
	echo "<nbvars>".$row["nbvariables"]."</nbvars>";
	echo "<usereader>".$row["usereader"]."</usereader>";
	echo "<autorefresh>".$row["autorefresh"]."</autorefresh>";
	echo "</module>";
}
$DB->freeResults();

$file->footer("page");

$DB->close();
?>