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
# POSH config features - 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_config_notifications_titles.php";
$errLog="";
$tabname="configstab";

require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("notification");

$type=$_GET['id'];
$lang=$_GET['lang'];

$DB->getResults($config_getNotification,$DB->quote($lang),$type);

$row = $DB->fetch(0);
$LabelMessageNoTranslation = "";
if (empty($row)) {
        $DB->freeResults();
        $DB->getResults($config_getNotification,$DB->quote("en"),$type);
    
        $row = $DB->fetch(0);
        $LabelMessageNoTranslation = "MessagetoTranslate";
        $row["lang"] = $lang;
        $row["id"]=0;
}

$notif_id=$row["id"];
$notif_lang=$row["lang"];
$notif_subject=stripslashes($row["subject"]);
$notif_message=stripslashes($row["message"]);
$notif_sender=$row["sender"];
$notif_copy=$row["copy"];


echo "<id>$notif_id</id>";
echo "<lang>$notif_lang</lang>";
echo "<subject><![CDATA[$notif_subject]]></subject>";
echo "<message><![CDATA[$notif_message]]></message>";
echo "<sender>$notif_sender</sender>";
echo "<copy><![CDATA[$notif_copy]]></copy>";
echo "<type>$type</type>";
echo "<msgalert>$LabelMessageNoTranslation</msgalert>";

$DB->freeResults();

$file->footer();
?>