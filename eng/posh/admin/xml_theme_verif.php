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
# POSH Users management - XML List of users searched
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

//if (!isset($_POST["theme"])) exit();

$folder="";
$not_access=1;
$granted="A";
$pagename="admin/xml_theme_verif.php";
//includes
require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();
$file->header();
	
$name=$_GET["theme"];

echo "<test>".$name."</test>";

if (!is_file("../styles/themes/".$name.".thm"))
	echo "<exist>1</exist>";
else
	echo "<exist>0</exist>";


$file->footer();
?>