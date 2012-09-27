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
# Cache management functions
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

include_once('../includes/file.inc.php');

// function used to generate cache files
//$template_folder=__LOCALFOLDER."admin/";
$template_folder="../admin/";
function cacheDataInFile($script_file, $cache_file)
{
	if (file_exists($script_file))
	{	
		ob_start();
		include($script_file);
		$HTML = ob_get_contents();
		ob_end_clean();
		$outfile=new file($cache_file);
		$outfile->write($HTML);
		return true;
	}
	return false;
}
?>