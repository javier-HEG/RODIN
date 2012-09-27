<?php
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
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
function getTabsFunctions($v_tab)
{ 
	global $DB,$admin_getTabFunctions;
	$DB->getResults($admin_getTabFunctions,$DB->quote($v_tab));
	if ($DB->nbResults()>1)
	{
		while ($row=$DB->fetch(0))
		{
			echo '<div class="box tabsfunctions">';
			echo '<h3><img src="../images/puce.gif" alt="" /><a href="'.__LOCALFOLDER.$row["link"].'">'.lg($row["label"]).'</a></h3>'."\n";
			echo '<p>'.lg($row["description"]).'</p>'."\n";
			echo "</div>\n";
		}
	}
	else
	{
		$row=$DB->fetch(0);
		echo "<script type='text/javascript'>
			window.location.replace('".__LOCALFOLDER.$row["link"]."');</script>";
		return;
	}
	$DB->freeResults();
}

function escapeJS($value)
{
	// Escaping double quotes
	$search[] = '/((\\{2})*)"/m';
	$replace[] = '$1\\"';
	
	// Escaping /
	$search[] = '/((\\{2})*)\//m';
	$replace[] = '$1\\\/';
	
	// Replacing \n and \r\n with the appropriate thing
	$search[] = '/\r?\n/m';
	$replace[] = '\\n';
	$value = preg_replace($search,$replace,$value);
	
	return $value;
}
?>