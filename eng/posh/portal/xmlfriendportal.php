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
# generate friends portal xml file
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$id=(isset($_GET["id"]))?$_GET["id"]:exit();
$check=$_GET["chk"];

$folder="";
$not_access=1;
$granted="I";
$pagename="portal/xmlfriendportal.php";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("portals");

$DB->getResults($xmlfriendportal_getPortal,$DB->escape($id),$DB->quote($check));
if ($DB->nbResults()!=0)
{
	$row = $DB->fetch(0);
?>
<portal>
 <id><?php echo $id;?></id>
 <name><![CDATA[<?php echo $row['name'];?>]]></name>
 <status><?php echo $row["status"];?></status>
 <author><?php echo $row["user_id"];?></author>
 <nbcol><?php echo $row["width"];?></nbcol>
 <style><?php echo $row["style"];?></style>
 <type><?php echo $row["type"];?></type>
 <param><![CDATA[<?php echo $row['param'];?>]]></param>
<?php
	$DB->freeResults();

	$DB->getResults($xmlfriendportal_getModules,$DB->escape($id),$DB->quote($check));
	$i=1;
	while ($row = $DB->fetch(0))
	{
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
  <blocked><?php echo $row["blocked"];?></blocked>
  <usereader><?php echo $row["usereader"];?></usereader>
  <autorefresh><?php echo $row["autorefresh"];?></autorefresh>
 </module>
<?php
		$i++;
	}
	echo "</portal>";
}
$DB->freeResults();

$file->footer();

$DB->close();
?>