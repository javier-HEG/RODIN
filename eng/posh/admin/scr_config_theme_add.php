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
# POSH Configuration - set theme
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_config_theme_add.php";
	
//includes
require_once("includes.php");
require_once('../includes/file.inc.php');

if (isset($_POST["add_new"]))
{
	if (is_uploaded_file($_FILES['logopath']['tmp_name']) 
        && (!isset($_POST["nologo"])) )    {
			$fichier_temp=$_FILES['logopath']['tmp_name'];
			$nom_fichier=$_FILES['logopath']['name'];
			$toReplace = array("".chr(34)."","".chr(39)."","".chr(32)."","".chr(60)."","".chr(62)."","".chr(47)."","".chr(92)."");
			$by = array("","","","","","","");
			$file_name = str_replace($toReplace,$by, $nom_fichier);
			if (substr($file_name,-4)==".gif"||substr($file_name,-4)==".jpg"||substr($file_name,-4)==".jpeg"||substr($file_name,-4)==".png"||$file_name=="")
				copy($_FILES['logopath']['tmp_name'], "../styles/themes/".$file_name);
		else    {
			exit();
		}
	}

	if(isset($_POST['themename']))  {
		$toReplace = array("".chr(34)."","".chr(39)."","".chr(32)."","".chr(60)."","".chr(62)."","".chr(47)."","".chr(92)."");
		$by = array("","","","","","","");
		$themename=str_replace($toReplace,$by, $_POST['themename']);
	}
	
	if (!is_file("../styles/themes/".$themename.".thm") && $themename!="")  {
		$outfile=new file("../styles/themes/".$themename.".thm");
		$infile=new file("../styles/themes/base.txt");
		$read=$infile->read();
		$toReplace = array("%logo", "%fontcolor","%letterscolor","%letterssize","%headerfontcolor","%widgetbarcolor","%widgettitlecolor");
        if (isset($_POST["nologo"])) { $chemin=""; }
        else { $chemin="../styles/themes/".$nom_fichier; }
        
        $by = array($chemin,$_POST['fontcolor'],$_POST['letterscolor'],$_POST['letterssize'],$_POST['headerfontcolor'],$_POST['widgetbarcolor'],$_POST['widgettitlecolor']);
        $newcontent = str_replace($toReplace,$by, $read);
		$outfile->write($newcontent);
	}
}

echo '<script>parent.$p.admin.config.theme.getThemes();</script>';
?>