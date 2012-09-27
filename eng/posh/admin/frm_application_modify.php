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
# POSH applications management - modify an application
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/frm_application_modify.php";
$tabname="applicationtab";

require_once("includes.php");
include('tab_access_control.php');
require_once("header_inframe.php");

if (isset($_GET["treated"]))
{
	echo lg("modifProcessed")." !";
	echo "<script type='text/javascript'>$"."p.admin.application.refresh();</script>";
}
?>

<script type="text/javascript">
var groups=[];
var selGroupId=-1;
var selAvailGroupId=-1;

function groupObj(id,name){
	this.id=id;
	this.name=name;
}
</script>

<form action="scr_application_modify.php" method="post"><br />
<table>

<?php
$appId=$_GET["appid"];

$DB->getResults($applications_getApplicationInformation,$DB->escape($appId));
$row = $DB->fetch(0);
echo '<tr><td>'.lg("title").'</td><td><input type="text" name="appname" maxlength="60" value="'.$row["title"].'" /></td></tr>';
echo '<tr><td>'.lg("description").'</td><td><textarea name="appdesc" cols="80" rows="10">'.$row["description"].'</textarea></td></tr>';
$DB->freeResults();
?>

</table>
<br />
<b><?php echo lg("addGroupsAccessOnApplications");?> :</b><br /><br />
 <div id="groupdiv">
	<table>
		<tr>
		<td><?php echo lg("userGroups");?> :<br /><br /><div id="selgroupdiv" style="width: 200px;height: 100px;overflow: auto;border: 1px solid #c6c3c6;padding: 2px;"></div></td>
		<td><input type="button" value="<" onclick="return $p.admin.users.addSelectedGroup();" style="font-size: 10pt"><br /><br /><input type="button" value=">" onclick="return $p.admin.users.supSelectedGroup();" style="font-size: 10pt"></td>
		<td><?php echo lg("availableGroups");?> :<br /><br /><div id="allgroupdiv" style="width: 200px;height: 100px;overflow: auto;border: 1px solid #c6c3c6;padding: 2px;"><div id="exp0"></div></div></td>
		</tr>
	</table>
	<div id="groupinputs"></div>
 </div>
 <br />
 <input type="hidden" name='appid' value="<?php echo $appId;?>" /><input type="submit" value="<?php echo lg("modify");?>" style="width: 250px"/>
</form>

<script type="text/javascript">
$p.admin.users.loadAvailGroups(0);

<?php
$group=array();
$DB->getResults($application_getSelGroups,$DB->escape($appId));
while ($row = $DB->fetch(0))
{
	echo "$"."p.admin.widgets.selGroup.push(new groupObj(".$row["id"].",\"".$row["name"]."\"));";
}
$DB->freeResults();
?>

$p.admin.users.showSelectedGroups();
</script>



</body>
</html>