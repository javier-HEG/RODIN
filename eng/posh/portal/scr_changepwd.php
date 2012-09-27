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
# Change user password 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$pass1  = (isset($_POST["pass1"])) ? $_POST["pass1"]
                                   : exit();
$oldpass = (isset($_POST["oldpass"])) ? $_POST["oldpass"]
                                      : exit();

$folder     = "";
$not_access = 1;
$isScript   = true;
$isPortal   = false;
$pagename   = "portal/scr_changepwd.php";
$granted    = "I";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../includes/xml.inc.php');

launch_hook('scr_changepwd');

$file = new xmlFile();

$file->header();
	// Change password in user information table
	$chk = $DB->execute($scrchangepwd_changePass,
                        $DB->quote($pass1),
                        $DB->escape($_SESSION['user_id']),
                        $DB->quote($oldpass));

	$test = $DB->nbAffected();

	if ($test != 0)
	{
		// Change profiles encoded informations that use the password
		$DB->execute($scrchangepwd_changePortalPass,
                        $DB->quote($pass1),
                        $DB->escape($_SESSION['user_id']),
                        $DB->quote($oldpass));
	}
	$DB->close();


$file->status($chk);
if ($test == 0)
{
    $file->message("incorrectPassword");
}
else
{
    $file->message("passwordModified");
}

$file->footer();
?>