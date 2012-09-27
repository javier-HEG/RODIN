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
# POSH Modules management - Cache XML List of modules
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=0;
//includes

require('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("item");

$id=isset($_GET["modid"])?$_GET["modid"]:0;

if ($id=='86')
{
	$DB2->getResults($cache_getRssModule);
}
else
{
	$DB2->getResults($cache_getModuleXml,$DB2->escape($id));
}
$row = $DB2->fetch(0);

echo "<id>".$id."</id>
 <name><![CDATA[".$row['name']."]]></name>
 <url><![CDATA[".$row['url']."]]></url>
 <size>".$row["height"]."</size>
 <minwidth>".$row["minwidth"]."</minwidth>
 <sizable>".$row["sizable"]."</sizable>
 <format>".$row["format"]."</format>
 <var><![CDATA[".$row["defvar"]."]]></var>
 <nota>".$row['nota']."</nota>
 <description><![CDATA[".$row['description']."]]></description>
 <website><![CDATA[".$row['website']."]]></website>
 <nbvars>".$row['nbvariables']."</nbvars>
 <usereader>".$row['usereader']."</usereader>
 <autorefresh>".$row['autorefresh']."</autorefresh>
 <editor_id>".$row['editor_id']."</editor_id>
 <editor><![CDATA[".$row['long_name']."]]></editor>
 <creation_date>".$row['creation_date']."</creation_date>
";
 
if ($id != 86) { 
    $l10n = isset($row['l10n']) ? $row['l10n'] : null;
    echo "<views>".$row['views']."</views>
<l10n>$l10n</l10n>    
<icon><![CDATA[".$row['icon']."]]></icon>
";
}

$DB2->freeResults();

$file->footer("item");
?>