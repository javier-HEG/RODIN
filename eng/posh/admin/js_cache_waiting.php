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
# list of new modules generation (XML)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$not_access=0;
//includes
require("includes.php");
global $DB;

$DB->getResults($cachewaiting_getModules);

$ret = "function waiting(){return \"<br />New Widgets :<br />"; //<table class='menubox' width='250'><tr><td>New modules :</td></tr><tr><td align='center'><br />";
while ($row = $DB->fetch(0))
{
	$ret.= "<div class='menubox' style='width: 200px;margin: 2px;'>".$row["name"]."</div>";
}
$ret.= "</td></tr></table>\";}";
echo $ret;

$DB->freeResults();
?>