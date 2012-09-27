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
# POSH theme upload
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/frm_config_theme_upload.php";
$errLog="";
$tabname="configstab";

require_once("includes.php");
require_once('header_inframe.php');


echo '		<form method="post" action="scr_config_add_theme.php" enctype="multipart/form-data"> ';
echo '		<p><strong>'.lg("addTheme").'</strong> :';
echo '		<input type="hidden" name="MAX_FILE_SIZE" value="500000" />';
echo '		<input type="file" name="fichier" size="30" /> <input type="submit" value="'.lg("Upload").'" /><br /><br />';
echo '		</p>';
echo '		<p>'.lg("themeReplaceWarning").' !</p>';
echo '		</form>';

?>