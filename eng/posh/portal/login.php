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

//manage autoconnection
if (!empty($_COOKIE['autoi']))
{
    header("location:scr_authentif.php");exit();
}

$folder     = "";
$not_access = 0;
$isScript   = false;
$isPortal   = false;
$pagename   = "portal/login.php";
$message    = '';
//includes
require_once('includes.php');
//require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');

launch_hook('login_php');

$message = (isset($_GET["message"])) ? $_GET["message"]
                                     : '';

// Account validation
if (isset($_GET['chk']) && isset($_GET["id"])) {
	$DB->getResults($login_checkAccountConfirmation,
                        $DB->quote($_GET['chk']),
                        $DB->escape($_GET['id']));
	if ($DB->nbResults()>0)
    {
		$DB->execute($login_activateAccount,
                        $DB->escape($_GET['id']));
		$message='accountValidated';
	}
}

// Unsubscribe
if (isset($_GET["id"]) && isset($_GET["md5"]))
{
	$message = lg("unsubscribeMessage");
	$DB->execute($users_unsubscribe,
                    $DB->escape($_GET['id']),
                    $DB->quote($_GET['md5']));	
}

//load theme
require_once('../../app/exposh/templates/'.__template.'/login.php');

?>