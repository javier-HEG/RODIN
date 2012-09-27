<?php
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
/* UTF8 encoding : é ô à ù */
$not_access=0;
require_once('confinstall.inc.php');
require_once('includes.php');
require_once('../includes/file.inc.php');
require_once('functions.inc.php');

if (!isset($_POST["username"])) exit();
if (!isset($_POST["password"])) exit();
if (!isset($_POST["confpass"])) exit();
$username=$_POST["username"];
$password=trim($_POST["password"], " \t\n\r");
$conf_password=trim($_POST["confpass"], " \t\n\r");

if ($conf_password!=$password){header("location:step5.php?err=1");exit();}
if (strlen($password)<6){header("location:step5.php?err=2");exit();}

$key=md5($username.$password);
$appkey=$password;
$DB->execute($install_addAdminUser,$DB->quote($username),$DB->quote($password),$DB->quote($appkey),$DB->quote($password),$DB->quote($key),$DB->quote(__LANG));
$id=$DB->getId();

$DB->execute($install_removeInstallationStep);

//install admin tabs
$DB->execute($install_rootUserTabs);

//generate config file
generateConfigFile(true,"","","","");

$DB->close();

header("location:scr_cache_all.php");
?>