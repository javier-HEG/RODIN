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
# Invite user to join groups
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$not_access=1;
$pagename="portal/src_group_update.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header();

$chk = $DB->execute($scr_group_update,  $DB->quote($_POST['title']),
                                        $DB->quote($_POST['picture']),
                                        $DB->escape($_POST['access']),
                                        $DB->quote($_POST['description']),
                                        $DB->escape($_SESSION['user_id']),
                                        $DB->escape($_POST['id']));	

$file->status($chk);

$file->footer();

$DB->close();
?>