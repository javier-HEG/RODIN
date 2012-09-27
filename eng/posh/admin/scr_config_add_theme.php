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
# POSH Configuration - add a new theme
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_config_add_theme.php";
//includes
require_once('includes.php');
//require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');

launch_hook("admin_scr_config_add_theme");

if (is_uploaded_file($_FILES['fichier']['tmp_name']))
{
	$fichier_temp=$_FILES['fichier']['tmp_name'];
	$nom_fichier=$_FILES['fichier']['name'];
	if (substr($nom_fichier,-4)==".thm")    { 
        copy($_FILES['fichier']['tmp_name'], "../styles/themes/".$nom_fichier); 
    }
	/*
    else  {
        //echo lg("noThmFile")."<br /> <A href='config_theme.php'>".lg("backPrevPage")."</A>";
		exit();
	}
    */
}
/*
else    {
	//echo lg("noThmFile")."<br /> <A href='config_theme.php'>".lg("backPrevPage")."</A>";
	exit();
}
*/

echo '<script>parent.$p.admin.config.theme.getThemes();</script>';

//header("location:config_theme.php");
?>