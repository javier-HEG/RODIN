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
# Users pages
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder     = "";
$not_access = 1;
$granted    = "I";
$pagename   = "portal/xmlpages.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file = new xmlFile();

$file->header("pages");

$groups = array();
array_push($groups,0);

//get the user profile
$DB->getResults($xmlpages_getTabs,$DB->escape($_SESSION["user_id"]));
if ($DB->nbResults() == 0) {
	$DB->freeResults();
	//get the root group of the user
	$rows = $DB->select(FETCH_ARRAY,$xmlpages_getGroup,$DB->escape($_SESSION['user_id']));
	
	if ($DB->nbResults() == 0 || ($DB->nbResults() == 1 && $rows[0]["group_id"] == 0))
    {
        //actions removed
	}
	else {
		foreach ($rows as $row)
		{
			$parentgroup = $row["group_id"];
			while ($parentgroup!=0)
			{
				$currgroup = $parentgroup;
				$DB->getResults($xmlpages_getParentGroup,$DB->escape($currgroup));
				$row=$DB->fetch(0);
				$parentgroup = $row["parent_id"];
				$DB->freeResults();
			}
			//is the group having specific page
			$DB->getResults($xmlpages_getPageForGroup,$DB->escape($currgroup));
			if ($DB->nbResults() != 0)
                array_push($groups,$currgroup);
			$DB->freeResults();
		}
	}
	
	// if no group with pages assigned, assign default one
	//if (count($groups)==0) array_push($groups,0);

	// assign selected page
	foreach ($groups as $group)
	{
		//$DB->getResults($xmlpages_getPageInformation,$DB->escape($group));
		$rows = $DB->select(FETCH_ARRAY,$xmlpages_getPageInformation,$DB->escape($group));
		foreach ($rows as $row)
		{
			$pageid=$row["id"];
			echo "<portal>";
			echo "<id>".$pageid."</id>";
			echo "<name><![CDATA[".$row["name"]."]]></name>";
			echo "<desc><![CDATA[".$row["description"]."]]></desc>";
			echo "<mode>".$row["position"]."</mode>";
			echo "<type>".$row["type"]."</type>";
			echo "<param><![CDATA[".$row["param"]."]]></param>";
			echo "<seq>".$row["seq"]."</seq>";
			echo "<nbcol>".$row["nbcol"]."</nbcol>";
			echo "<showtype>".$row["showtype"]."</showtype>";
			echo "<npnb>".$row["npnb"]."</npnb>";
			echo "<style>".$row["style"]."</style>";
			echo "<modulealign>".$row["modulealign"]."</modulealign>";
			echo "<removable>".$row["removable"]."</removable>";
			echo "<pageid>".$pageid."</pageid>";
			$DB->getResults($xmlpages_getModules,$DB->escape($pageid));
			while ($row2=$DB->fetch(0))
			{
				echo "<module>";
				echo "<id>".$row2["item_id"]."</id>";
				echo "<col>".$row2["posx"]."</col>";
				echo "<pos>".$row2["posy"]."</pos>";
				echo "<posj>".$row2["posj"]."</posj>";
				echo "<x>".$row2["x"]."</x>";
				echo "<y>".$row2["y"]."</y>";
				echo "<vars><![CDATA[".$row2["variables"]."]]></vars>";
				echo "<uniq>".$row2["uniq"]."</uniq>";
				echo "<blocked>".$row2["blocked"]."</blocked>";
				echo "<minimized>".$row2["minimized"]."</minimized>";
				echo "</module>";
			}
			$DB->freeResults();
			echo "</portal>";
		}
	}
}
else
{
	$row = $DB->fetch(0);
	echo "<page>";
	echo "<id>".$row["id"]."</id>";
	//echo "<url><![CDATA[mypage.php?start=".$row["id"]."]]></url>";
    echo "<url><![CDATA[mypage.php]]></url>";
	echo "</page>";
	$DB->freeResults();
}
$file->footer();

$DB->close();
?>