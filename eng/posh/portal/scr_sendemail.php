<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of COLLABORATION SUITE of POSH http://sourceforge.net/projects/posh/.

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
# Send an email
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_sendemail.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header();

$inc=0;
$emailsent=false;
$ret="";
$from=(isset($_POST["from"]) && !empty($_POST["from"]))?$_POST["from"]:__SUPPORTEMAIL;
while (isset($_POST["em".$inc]))
{
	$check=mail($_POST["em".$inc],stripslashes(utf8_decode($_POST["title"])),stripslashes(utf8_decode($_POST["desc"])),"From: ".$from." \r\n");
	if (!$emailsent && $check) $ret.=lg("emailSent");
	$emailsent=true;
	$inc++;
}

$DB->close();

$file->status(1);
$file->message($ret);

$file->footer();
?>