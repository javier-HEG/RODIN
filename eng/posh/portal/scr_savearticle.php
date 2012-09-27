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
# Archive an article (XML return)
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

if (!isset($_POST["t"])) exit();
if (!isset($_POST["l"])) exit();

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_savearticle.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header();
$t = isset($_POST["t"])?$_POST["t"]:"";
$l = isset($_POST["l"])?$_POST["l"]:"";
$s = isset($_POST["s"])?$_POST["s"]:"";
$i = isset($_POST["i"])?$_POST["i"]:"";
$d = isset($_POST["d"])?$_POST["d"]:"";
$id = isset($_POST["id"])?$_POST["id"]:"";

// save the article in the user list
$chk=$DB->execute($scrsavearticle_saveArticle,$DB->escape($_SESSION['user_id']),$DB->noHTML($t),$DB->noHTML($l),$DB->quote($s),$DB->quote($i),$DB->quote($d),$DB->escape($id));
$file->status($chk);

$file->footer();

$DB->close();
?>