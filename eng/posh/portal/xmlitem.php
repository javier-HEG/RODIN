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
# display restricted module for a user
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
#
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlitem.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("page");

if (isset($_GET["id"])) {
    if ($_GET["id"] == 0) {
        echo "<err>id null</err>";
        $file->footer("page");
        $DB->close();
        exit;
    }
    $id = $_GET["id"]; 
} else {
    echo "<err>id missing</err>";
    $id=-1;
    $file->footer("page");
    $DB->close();
    exit;
}

$DB->getResults($getXmlItem,$DB->escape($id),$DB->escape($_SESSION['user_id']));
error_log($DB->sql);
$row = $DB->fetch(0);
?>
<item>
 <id><?php echo $id;?></id>
 <name><![CDATA[<?php echo $row['name'];?>]]></name>
 <url><![CDATA[<?php echo $row['url'];?>]]></url>
 <size><?php echo $row["height"];?></size>
 <minwidth><?php echo $row["minwidth"];?></minwidth>
 <sizable><?php echo $row["sizable"];?></sizable>
 <format><?php echo $row["format"];?></format>
 <var><![CDATA[<?php echo $row["defvar"];?>]]></var>
 <description><![CDATA[<?php echo $row['description'];?>]]></description>
 <website><![CDATA[<?php echo $row['website'];?>]]></website>
 <nbvars><?php echo $row['nbvariables'];?></nbvars>
 <usereader><?php echo $row['usereader'];?></usereader>
 <autorefresh><?php echo $row['autorefresh'];?></autorefresh>
 <editor_id><?php echo $row['editor_id'];?></editor_id>
 <editor><?php echo $row['long_name'];?></editor>
 <creation_date><?php echo $row['creation_date'];?></creation_date>
 <views><?php echo $row["views"];?></views>
 <icon><?php echo $row["icon"];?></icon>
 <l10n><?php echo $row["l10n"];?></l10n>    
</item>
<?php
$DB->freeResults();

$file->footer("page");

$DB->close();
?>