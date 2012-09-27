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
# POSH Configuration - save information banner configuration
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_communication_infobar.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("infobar");

launch_hook('admin_scr_communication_infobar');

$bartype=($_POST["bartype"]==""?"0":$_POST["bartype"]);
$DB->execute($communication_setBarType,$DB->quote($bartype));
if ($DB->nbAffected()==0)   {
	$DB->execute($communcation_insertBarType,$DB->quote($bartype));
}

$DB->execute($communication_setRssInfo,  $DB->quote($_POST["rssinfo"])  );
if ($DB->nbAffected()==0)   {
	$DB->execute($communication_insertRssInfo,  $DB->quote($_POST["rssinfo"])  );
}

$barclosing=isset($_POST["barclosing"])?"true":"false";
$DB->execute($communication_setInfoBar,  $DB->quote($barclosing)  );
if ($DB->nbAffected()==0)   {
	$DB->execute($communication_insertInfoBar,  $DB->quote($barclosing)  );
}


$texthtml=$_POST["bartexthtml"];
$texthtml=str_replace("\r\n","<br>",$texthtml);
$texthtml=str_replace("&","&amp;",$texthtml);
$texthtml=str_replace("  ","&nbsp; ",$texthtml);
$texthtml=eregi_replace("<[^\<]+>","",$texthtml);
$baralert = isset( $_POST["baralert"] ) ? true : false;
if ( $baralert ) {
    $texthtml="<span style=\"background:yellow;padding:0 4px 0 4px;\">".$texthtml."</span>";
}

$DB->execute($communication_setBarTextHtml,  $DB->quote($texthtml)  );
if ($DB->nbAffected()==0)
{
	$DB->execute($communication_insertBarTextHtml, $DB->quote($texthtml)  );
}

$file->status(1);

$file->footer();

//header("location:scr_config_generate_configfiles.php?redirect=communication");
?>