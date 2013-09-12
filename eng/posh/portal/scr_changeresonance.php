<?php
# ************** LICENCE ****************
/*
 * FABIO RICCI - FRI - CLONE OF scr_changepwd
 * 	Copyright (c) PORTANEO.

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

$positext= (isset($_POST["positext"])) ? $_POST["positext"]: '';
$negatext= (isset($_POST["negatext"])) ? $_POST["negatext"]: '';


$folder     = "";
$not_access = 1;
$isScript   = true;
$isPortal   = false;
$pagename   = "portal/scr_changeresonance.php";
$granted    = "I";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../includes/xml.inc.php');

launch_hook('scr_changeresonance');

$file = new xmlFile();

$_SESSION['positext']=$positext;
$_SESSION['negatext']=$negatext;


$file->header();
	// Change password in user information table
	$chk = $DB->execute($scrchangeresonance,
                        $DB->quote($positext),
                        $DB->quote($negatext),
                        $DB->escape($_SESSION['user_id']));

	$test = $DB->nbAffected();


$file->status($chk);
if ($test == 0)
{
    $file->message("Resonance texts not modified");
}
else
{
    $file->message("Resonance texts modified");
}

$file->footer();
?>