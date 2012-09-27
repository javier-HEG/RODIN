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
# Save user portal options 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$prof=(isset($_POST["prof"]))?$_POST["prof"]:exit();
$portname=(isset($_POST["portname"]))?(Empty($_POST["portname"])?"----":$_POST["portname"]):exit();
$portstyle=(isset($_POST["portstyle"]))?$_POST["portstyle"]:exit();
$col=(isset($_POST["col"]))?$_POST["col"]:exit();

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_config_options.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../includes/xml.inc.php');

launch_hook('scr_config_options');

$file=new xmlFile();

$file->header();

//Change the default portal (if requested)
if (isset($_POST["portdef"]) AND $_POST["portdef"]=='Y')
{
	$DB->execute($scrconfigoptions_setDefAll,$DB->escape($_SESSION['user_id']));
	$DB->execute($scrconfigoptions_setDef,$DB->escape($_SESSION['user_id']),$DB->escape($prof));
}

//Update the portal information (name, password, style)
if (isset($_POST["usepass"]))
{
	if ($_POST["usepass"]==1)
	{
		$DB->getResults($scrconfigoptions_getPass,$DB->escape($_SESSION['user_id']));
		$row=$DB->fetch(0);
		$portpass=$row["md5pass"];
		$DB->freeResults();
		$passStr = ", pass='', md5pass=".$DB->quote($portpass)." ";
	}
	else
	{
		$passStr = ", pass='', md5pass='' ";
	}
}
else
{
	$passStr="";
}

$DB->sql = "UPDATE profile ";
$DB->sql.= "SET name=".$DB->noHTML($portname)." ";
$DB->sql.= $passStr;
$DB->sql.= ", style=".$DB->quote($portstyle).",width=".$DB->escape($col)." ";
$DB->sql.= ", advise=".$DB->quote($_POST["advise"]).", modif_date=CURRENT_DATE ";
if (isset($_POST["usereader"])) $DB->sql.= ", usereader=".$DB->quote($_POST["usereader"])." ";
if (isset($_POST["ctrl"])) $DB->sql.= ", controls=".$DB->quote($_POST["ctrl"])." ";
if (isset($_POST["align"])) $DB->sql.= ", modulealign=".$DB->quote($_POST["align"])." ";
if (isset($_POST["icon"])) $DB->sql.= ", icon=".$DB->quote($_POST["icon"])." ";
if (isset($_POST["load"])) $DB->sql.= ", loadonstart=".$DB->quote($_POST["load"])." ";
$DB->sql.= "WHERE user_id=".$DB->escape($_SESSION['user_id'])." AND id=".$DB->escape($prof)." ";

$chk=$DB->execute($DB->sql);

$file->status($chk);
if ($chk) $file->message("modificationApplied");

$file->footer();

$DB->close();
?>