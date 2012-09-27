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
# POSH Modules management - Cache XML List of the directories
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="../portal/";
$not_access=0;
//includes
require('includes.php');
require_once('../includes/xml.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');

$file=new xmlFile();

$file->header("channel");

$id=isset($_GET["catid"])?$_GET["catid"]:0;

if ($id>0)
{
	$curcat = $id;
	$curcattext = "";
	$curcatname = "";
	while ($curcat>0)
	{
		$DB2->getResults($cache_getDirectoryInformation,$DB2->escape($curcat));
		$row = $DB2->fetch(0);
		if ($curcat==$id)
		{
			$curcatname = $row["name"];
			$curquantity=$row["quantity"];
			$curcattext = "> " . $curcatname;
			echo "<parent>".$row["parent_id"]."</parent>";
		}
		else
		{
			$curcattext = "> <a href=# onclick=\"$"."p.app.menu.widget.getExplorer(".$row["id"].",indef,'".$row['lang']."')\" style='color:#000000;text-decoration:underline;'>" . $row["name"] . "</a> " . $curcattext;
		}
		$curcat = $row["parent_id"];
		$DB2->freeResults();
	}
	echo "<dirname><![CDATA[".$curcatname." (".$curquantity.")]]></dirname>";
	echo "<path><![CDATA[ <a href='#' onclick='$"."p.app.menu.widget.getExplorer(0)' style='color:#000000;text-decoration:underline;'>".lg('home')."</a> " . $curcattext . " ]]></path>";
}
else
{
	echo "<path>".lg('home')."</path>
          <parent>0</parent>";
}

if ($id==0)
{
	$DB2->getResults($cache_getRootDirectoryChildren,$DB2->escape($id));
}
else
{
	$DB2->getResults($cache_getDirectoryChildren,$DB2->escape($id));
}

$number = $DB2->nbResults();
if ($number>0)
{
	while ($row = $DB2->fetch(0))
	{
		echo "
<dir>
 <dirid>".$row['id']."</dirid>
 <dirname><![CDATA[".$row['name']."]]></dirname>
 <quantity>".$row['quantity']."</quantity>
 <secured>".$row['secured']."</secured>
 <secured_quantity>".$row['secured_quantity']."</secured_quantity>
 <lang>".$row['lang']."</lang>
</dir>
        ";
	}
}
$DB2->freeResults();

$DB2->getResults($module_getModuleInformationByXml,$DB2->escape($id));
if ($DB2->nbResults() > 0)
{
	while ($row = $DB2->fetch(0))
	{
		echo "
<item>
 <id>".$row['id']."</id>
 <name><![CDATA[".$row['name']."]]></name>
 <rank>".$row['nota']."</rank>
 <icon>".$row['icon']."</icon>
</item>
";
	}
}
$DB2->freeResults();

$file->footer("channel");
?>