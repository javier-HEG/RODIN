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
# Disconnect from connected environments (personal page & admin)
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder     = "";
$not_access = 0;
$isScript   = true;
$isPortal   = false;
$pagename   = "portal/scr_authentif.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../includes/authentification.inc.php');

launch_hook('scr_authentif');

$errormsg=" ";
$user="";
$password="";
$type="";
$id="";

//logout request
if(isset($_GET["act"]) AND $_GET["act"]=="logout")
{
    if (isset($_SESSION['type']) && $_SESSION['type']=="A")  {
        setcookie('laststate', '', 1);
    }    
	user_logout();
	exit;
}
//login request
if (!empty($_COOKIE["autoi"]))
{
	$id = $_COOKIE["autoi"];
	$password = $_COOKIE["autop"];
	$md5 = true;
}

if(isset($_POST["username"]))
{
	$id = $_POST["username"];
	$password = isset($_POST["password"]) ? $_POST["password"] : "";
	$md5 = false;
}

if (user_login($id,$password,$md5,$user,$error))
{
	launch_hook('logged_in',$user);

	init_session($user->id,$user->username,$user->type,$user->lang,$user->longname,$user->activity);

	if (isset($_POST["page"]))
	{
		header("location:".__LOCALFOLDER.$_POST["page"]);
	}
	else
	{
		if ($user->type=="I") header("location:scr_defportal.php");
		if ($user->type=="A") header("location:../admin/index.php");
	}
	exit();
}
else
{
	user_logout();
	exit();
}
?>