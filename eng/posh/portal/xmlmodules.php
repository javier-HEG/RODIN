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
# Users modules list (for a defined portal number)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$id=(isset($_POST["id"]))?$_POST["id"]:exit();

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlmodules.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("page");

$DB->getResults($xmlmodules_getTabInfo,$DB->escape($_SESSION['user_id']),$DB->escape($id));
if ($DB->nbResults()<1)
{
	echo "<nopage>1</nopage></page>";
	exit();
}
$row = $DB->fetch(0);
$pass=$row["md5pass"];

//authentification
if (isset($_SESSION["mdp"]) AND $_SESSION["mdp"]==1){$pass="";}

if ($pass=="")
{
	echo "<ctrl>".$row["controls"]."</ctrl>";
	echo "<modulealign>".$row["modulealign"]."</modulealign>";
	echo "<style>".$row["style"]."</style>";
	echo "<nbcol>".$row["width"]."</nbcol>";
	echo "<usepass>".($row["md5pass"]==""?0:1)."</usepass>";
	echo "<showtype>".$row["showtype"]."</showtype>";
	echo "<advise>".$row["advise"]."</advise>";
	echo "<usereader>".$row["usereader"]."</usereader>";
	echo "<npnb>".$row["nbnews"]."</npnb>";
	echo "<default>".$row["def"]."</default>";
	
	$DB->freeResults();

	echo '<modules>';

	$DB->getResults($xmlmodules_getInfoModules,$DB->escape($_SESSION['user_id']),$DB->escape($id));

	$inc=0;
	$incj=0;
	while($row=$DB->fetch(0))
	{
		$inc++;
		if ($row["status"]=="O"){$url=$row["url"];$format=$row["format"];}
		else {$url="../modules/moddef.php?id=" . $row["id"] . "&size=" . $row["height"] . "&st=" . $row["status"] . "&";$format="I";}
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
		echo "<url><![CDATA[".$url."]]></url>";
		echo "<uniq>".$row["uniq"]."</uniq>";
		echo "<format>".$format."</format>";
		echo "<nbvars>".$row["nbvariables"]."</nbvars>";
		echo "<blocked>".$row["blocked"]."</blocked>";
		echo "<minimized>".$row["minimized"]."</minimized>";
		echo "<usereader>".$row["usereader"]."</usereader>";
		echo "<autorefresh>".$row["autorefresh"]."</autorefresh>";
		echo "<icon>".$row["icon"]."</icon>";
        echo "<views>".$row["views"]."</views>";
        echo "<l10n>".$row["l10n"]."</l10n>";  
        echo "<shared><![CDATA[".$row["shared"]."]]></shared>";  
        //echo "<currentview>".$row["currentview"]."</currentview>";
		echo "</module>";
	}
	$DB->freeResults();

	echo "</modules>";
}
else
{
	$DB->freeResults();
	echo "<pagelocked>1</pagelocked>";
}
$file->footer("page");

$DB->close();
?>