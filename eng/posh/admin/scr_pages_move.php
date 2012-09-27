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
# POSH Pages management - Apply tabs move
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_pages_move.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

launch_hook('admin_scr_pages_move');

$id=$DB->escape($_POST["id"]);
$seq=$DB->escape($_POST["seq"]);

// Get the old sequence id of the moved module
$DB->getResults($pages_getSequence,$id);
$row = $DB->fetch(0);
$oldseq=$DB->escape($row["seq"]);
$DB->freeResults();

launch_hook('page_move',$id,$oldseq,$seq);

// moved all the pages impacted by the moving
$DB->execute($pages_moveNextSeqLeft,$oldseq);
echo $DB->sql.'<br />';
$DB->execute($pages_moveNextSeqRight,$seq);
echo $DB->sql.'<br />';
$DB->execute($pages_updateSeq,$seq,$id);
echo $DB->sql.'<br />';
$file->status("1");

$file->footer("channel");
?>