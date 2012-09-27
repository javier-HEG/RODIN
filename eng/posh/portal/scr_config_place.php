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
# Save modules position 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_config_place.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

launch_hook('scr_configplace');

$file=new xmlFile();

$file->header();

//Check that the user portal number is given
if (isset($_POST["prof"]))
{
	$prof=$_POST["prof"];
	$inc=1;

	//record new module
	if (isset($_POST["idn"]))
		$DB->execute($scrconfigplace_addNewMod,
                                    $DB->escape($_POST["idn"]),
                                    $DB->escape($_SESSION['user_id']),
                                    $DB->escape($prof),
                                    $DB->escape($_POST["pxn"]),
                                    $DB->escape($_POST["pyn"]),
                                    $DB->escape($_POST["jn"]),
                                    $DB->escape($_POST["xn"]),
                                    $DB->escape($_POST["yn"]),
                                    $DB->quote($_POST["vn"]),
                                    $DB->escape($_POST["un"]),
                                    $DB->escape($_POST["fn"])
                    );
		
	//record suppression of a module
	if (isset($_POST["ids"]))
	{ /*FRI - enhanced
		include_once("..../app/u/FRIdbUtilities.php");
		$user_id=$_SESSION['user_id'];
		$wid=$_POST["ids"];
		$us=$_POST["us"];
		$app_id="$user_id:$prof:$us";
		//unregister_default_prefs($app_id); //Delete also PREFS from DB
		*/
		$DB->execute($scrconfigplace_removeMod,
                                $DB->escape($_SESSION['user_id']),
                                $DB->escape($prof),
                                $DB->escape($_POST["ids"]),
                                $DB->escape($_POST["us"])
                    );
	}
		
	//record module's move. Usage of the E status to avoid moving twice the same module (if one module take the place of an other)
	while (isset($_POST["id".$inc]))
	{
		$DB->execute($scrconfigplace_updateMod,
                        $DB->escape($_POST["px".$inc]),
                        $DB->escape($_POST["py".$inc]),
                        $DB->escape($_POST["j".$inc]),
                        $DB->escape($_POST["x".$inc]),
                        $DB->escape($_POST["y".$inc]),
                        $DB->escape($_SESSION['user_id']),
                        $DB->escape($prof),
                        $DB->escape($_POST["id".$inc]),
                        $DB->escape($_POST["u".$inc])
                    );
		$inc++;
	}

	$DB->close();
	
	$file->status(1);
}

$file->footer();
?>
