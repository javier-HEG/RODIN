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
# POSH languages import - 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/frm_config_langimport.php";
$errLog="";
$tabname="configstab";

require_once("includes.php");
include('tab_access_control.php');
require_once('header_inframe.php');
?>
<br /><br />
<center><b><?php echo lg('sqlFilesInfo') ?></b></center>
<br /><br /><br />
<form name="f" action="scr_config_langimport.php" enctype="multipart/form-data" method="post">
    <center><?php echo lg('lblImportSqlFile'); ?><br /><br />
    <input type='file' name='fichier' />&nbsp;<input type="submit" name='submitFileImport' value="<?php echo lg("save");?>" /><br />
    <br /><br />
    <?php echo lg('lblGenerateLangFiles'); ?><br /><br />
    <input type="submit" name='submitGenerateFiles' value="<?php echo lg('GenerateFiles'); ?>" /></center>
</form>
