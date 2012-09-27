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
# GroupBook properties
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder = "../notebook/";
$not_access = 0;
$pagename = "notebook/xml_groupbook_properties.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$id = $_GET["id"];

$DB->getResults($notebook_getGroupbookProperties,$DB->escape($_SESSION['user_id']),$DB->escape($id));

$file = new xmlFile();

$file->header("groupbook");

$row = $DB->fetch(0);

echo '
<name><![CDATA['.$row["name"].']]></name>
<picture><![CDATA['.$row["picture"].']]></picture>
<private>'.$row["private"].'</private>
<ismember>'.$row["user_id"].'</ismember>
<createdby>'.$row["created_by"].'</createdby>
<description><![CDATA['.$row["description"].']]></description>
';

$DB->freeResults();

$file->footer();

$DB->close();
?>