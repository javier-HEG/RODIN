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
# POSH Configuration - set application languages
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_config_lang.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("setlang");

launch_hook('admin_scr_config_lang');

$defLang=$_POST["langdefault"];
$langlist=array();

$inc=0;
While (isset($_POST["lang".$inc]))
{   
	if (isset($_POST["langsel".$inc]))  {
		if ($_POST["lang".$inc]==$defLang)  {
			array_unshift($langlist,$_POST["lang".$inc]);
		}
		else    {
			array_push($langlist,$_POST["lang".$inc]);
		}
	}
	$inc++;
}

$chk=$DB->execute($config_setValue,$DB->quote('"'.implode('","',$langlist).'"'),$DB->quote("AVLANGS"));

$file->status($chk);

$file->footer();
?>