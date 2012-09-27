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
# POSH Module management - User Module validation form
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="tutorial/xml_getmoduleinfo.php";

$itemid=$_GET["itemid"];

require_once('includes.php');
require_once('../includes/xml.inc.php');
require_once('../includes/modules_tools.php');

$file=new xmlFile();

$file->header("modInfo");

//get module information
$DB->getResults($tutorial_getModulesInfo,$DB->escape($itemid));
$row = $DB->fetch(0);

$xmlmodule=$row["xmlmodule"];
$source=$row["source"];
$validated=$row["status1"];

$id=$row["item_id"];
$url=$row["url"];
$defvar=$row["defvar"];
$name=$row["name"];
$description=$row["description"];
$typ=$row["typ"];
$format=$row["format"];
$height=$row["height"];
$minwidth=$row["minwidth"];
$sizable=$row["sizable"];
$website=$row["website"];
$nbvariables=$row["nbvariables"];
$lang=$row["lang"];
$usereader=$row["usereader"];
$autorefresh=$row["autorefresh"];
$status=$row["status2"];
$icon=$row["icon"];
$views=$row["views"];
$l10n=$row["l10n"];

echo "<id>".$id."</id>";
echo "<url><![CDATA[".$url."]]></url>";
echo "<defvar><![CDATA[".$defvar."]]></defvar>";
echo "<name><![CDATA[".$name."]]></name>";
echo "<description><![CDATA[".$description."]]></description>";
echo "<type><![CDATA[".$typ."]]></type>";
echo "<format><![CDATA[".$format."]]></format>";
echo "<height>".$height."</height>";
echo "<minwidth>".$minwidth."</minwidth>";
echo "<website><![CDATA[".$website."]]></website>";
echo "<nbvariables>$nbvariables</nbvariables>";
echo "<lang><![CDATA[".$lang."]]></lang>";
echo "<usereader>".$usereader."</usereader>";
echo "<autorefresh>".$autorefresh."</autorefresh>";
echo "<status>".$status."</status>";
echo "<validate>".$validated."</validate>";
echo "<icon>".$icon."</icon>";
echo "<views>$views</views>";
echo "<l10n>$l10n</l10n>\n";
//echo "<source><![CDATA[".$source."]]></source>";

$headers=getHeaderContents($xmlmodule);
$contents=getContents($xmlmodule);
$headers = preg_replace('/^(\r\n|\r|\n)*/','',$headers);
echo "<headers><![CDATA[$headers]]></headers>\n";
for ($i=0;$i<count($contents);$i++) 
{   
    $parameters = $contents[$i];
    $type = getContentType($parameters);
    $view = getContentViewParam($parameters);
    echo "<contents>";
    echo "<type>".$type."</type>";
    echo "<view>".$view."</view>";
    if ($type=='url') {
        $url = getContentUrlParam($parameters);
        echo "<url><![CDATA[".$url."]]></url>";
    } 
    elseif ($type=='html') {
        preg_match ('/<!\[CDATA\[(.*)((.|\n)*)\]\]>/U', $parameters, $matches); 
        $matches[2] = preg_replace('/^(\r\n|\r|\n)*/','',$matches[2]);
        echo "<incontent><![CDATA[".$matches[2]."]]></incontent>";   
    }
    echo "</contents>";   
    
}

$file->footer();
?>