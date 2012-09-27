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
# Users connection checks & start session
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$user = (isset($_POST["u"])) ? $_POST["u"]
                             :exit();
$password = (isset($_POST["pass"])) ? $_POST["pass"]
                                    : exit();

$folder     = "";
$not_access = 0;
$isScript   = true;
$isPortal   = false;
$pagename   = "portal/scr_connect.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../includes/authentification.inc.php');
require_once('../includes/xml.inc.php');

if (user_login($user,$password,false,$user,$errormsg))
{
	launch_hook('logged_in',$user);

	if (isset($_POST["auto"]) && $user->type=='I')
	{
		user_setcookie($user);
	}

	//Session initialization		
	init_session($user->id,$user->username,$user->type,$user->lang,$user->longname,$user->activity);
	$_SESSION['mdp'] = 1;
	$_SESSION['FRI_START'] = 1; //FRI: Signalise first start (against reload)

	launch_hook('scr_connect_session_initialized');

	if (isset($_POST["rtype"]))
	{
		if ($user->type == "I")
		{
			$ret = "scr_defportal.php";
		}
		elseif ($user->type == "A")
		{
			$ret = "../admin/index.php";
		}
		else
		{
			$ret = "index.php";
		}
	}
	else
	{
		if ($user->type == "I")
		{
			//$ret=$key;
			$ret = "user";
		}
		elseif ($user->type == "A")
		{
			$ret = "admin";
		}
		elseif ($user->type == "R")
		{
			$ret = "redactor";
		}
	}

	//do not send xml response before (cookie creation needs to be done before header sent)
	$file = new xmlFile();

	$file->header();
	
	$file->status(1);
	$file->returnData($ret);
	
	$file->footer();
}
else
{
	$file = new xmlFile();

	$file->header();

	$file->status(1);
	$file->error($errormsg);
	
	$file->footer();
}
?>