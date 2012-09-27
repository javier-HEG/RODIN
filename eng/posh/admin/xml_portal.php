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
# generation of the portal information (xml format)
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

header("content-type: application/xml");
$folder="../portal/";
$not_access=1;
$granted="A";
$pagename="admin/xml_portal.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');

echo '<?xml version="1.0" encoding="UTF-8"?>';

$id=isset($_GET["id"])?$_GET["id"]:exit();

//get selected portal inforamtion
$DB->getResults($xmlportal_getPortal,$DB->escape($id));
$row = $DB->fetch(0);
?>
<portal>
 <id><?php echo $id;?></id>
 <name><![CDATA[<?php echo $row['name'];?>]]></name>
 <description><![CDATA[<?php echo $row['description'];?>]]></description>
 <status><?php echo $row["status"];?></status>
 <author><?php echo $row["author"];?></author>
 <nbcol><?php echo $row["nbcol"];?></nbcol>
 <modulealign><?php echo $row["modulealign"];?></modulealign>
 <style><?php echo $row["style"];?></style>
 <showtype><?php echo $row["position"];?></showtype>
<?php
$DB->freeResults();

// get selected portal widgets
$DB->getResults($xmlportal_getModules,$DB->escape($id));
$i=1;
while ($row = $DB->fetch(0)){
?>
 <module>
  <id><?php echo $row["item_id"];?></id>
  <name><![CDATA[<?php echo $row["name"];?>]]></name>
  <col><?php echo $row["posx"];?></col>
  <pos><?php echo $row["posy"];?></pos>
  <posj><?php echo $row["posj"];?></posj>
  <x><?php echo $row["x"];?></x>
  <y><?php echo $row["y"];?></y>
  <vars><![CDATA[<?php echo $row["variables"];?>]]></vars>
  <height><?php echo $row["height"];?></height>
  <site><![CDATA[<?php echo $row["website"];?>]]></site>
  <minmodsize><?php echo $row["minwidth"];?></minmodsize>
  <updmodsize><?php echo $row["sizable"];?></updmodsize>
  <url><![CDATA[<?php echo $row["url"];?>]]></url>
  <uniq><?php echo $i;?></uniq>
  <format><?php echo $row["format"];?></format>
  <nbvars><?php echo $row["nbvariables"];?></nbvars>
  <usereader><?php echo $row["usereader"];?></usereader>
  <autorefresh><?php echo $row["autorefresh"];?></autorefresh>
 </module>
<?php
	$i++;
}
$DB->freeResults();
$DB->close();
echo "</portal>";
?>