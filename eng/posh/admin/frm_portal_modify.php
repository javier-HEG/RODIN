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
# POSH Portal management - Portal modification form
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/frm_portal_modify.php";

require_once('includes.php');
require_once("header_inframe.php");
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');

?>
<script type="text/javascript">
	var dirid=0;
	var dirname;
	function showDir()
	{
		var l_s="";
		if (dirid==0)
		{
			var l_firstdirid=(__portaldirtype=="module"?__dimension[0]["id"]:0)
			parent.admPortals.initDir(l_firstdirid,__dimension[0]["seq"]);
			l_s+="<font color='#ff0000'>"+lg("lblDirChangeTxt")+" [<a href='#' onclick='return setDir();'>"+lg("lblClickHere")+"</a>]</font>";
			document.forms["f"].submitbtn.disabled=true;
		}
		else
		{
			l_s+=dirname+" <input type='hidden' name='catid' value='"+dirid+"' />[<a href='#' onclick='return editDir();'>"+lg("lblModify")+"</a>]";
			document.forms["f"].submitbtn.disabled=false;
		}
		navPrint("dirdiv",l_s);
	}
	function setDir()
	{
		dirid=parent.admPortals.catid;
		dirname=parent.admPortals.catname;
		showDir();
		return false;
	}
	function editDir()
	{
		dirid=0;
		showDir();
		return false;
	}
</script>


<?php
if (isset($_GET["reload"]))
{
	echo lg("modifProcessed")." !";
	echo "<script type='text/javascript'>parent.admPortals.refreshGroup();</script>";
}

$id=$_GET["id"];
//get portal infomation
$DB->getResults($frmportalmodify_getPortal2,$DB->escape($id));
$row = $DB->fetch(0);
$name=$row["name"];
$desc=$row["description"];
$status=$row["status"];
$catid=0;
$catname="";
$DB->freeResults();

?>


<table cellpadding="0" cellspacing="10" border="0">
	<tr>
	<td valign="top" width="400">
	<form name="f" action="scr_portal_modify.php" method="post">
	<input type="hidden" name="id" value="<?php echo $id;?>" />
	<input type="hidden" name="oldcatid" value="<?php echo $catid;?>" />
	<table>
	<tr><td><?php echo lg("title");?> :</td><td><input type="text" name="name" value="<?php echo $name;?>" maxlength="40" /></td></tr>
	<tr><td><?php echo lg("description");?> :</td><td><textarea name="desc" cols="30" rows="3"><?php echo $desc;?></textarea></td></tr>
	<tr><td><?php echo lg("status");?> :</td><td><select name=status>
	<option value='O'<?php if ($status=="O") echo " SELECTED";?>><?php echo lg("active");?>
	<option value='N'<?php if ($status=="N") echo " SELECTED";?>><?php echo lg("inactive");?>
	<option value='D'<?php if ($status=="D") echo " SELECTED";?>><?php echo lg("suppressed");?>
	</select>
	</td></tr>
	<!--<tr><td><?php echo lg("dir");?> :</td><td><div id="dirdiv"></div></td></tr>-->
	<tr><td></td><td><br /><input name="submitbtn" type="submit" value="<?php echo lg("modify");?>" /> <input type="button" value="<?php echo lg("watch");?>" onclick="parent.admPortals.overview(<?php echo $id;?>)" />
	</td></tr></table>
	</form>
	</td>
	<td valign="top">
	<table cellpadding="2" cellspacing="0" border="0">
		<tr><td><?php echo lg("modules");?> :</td></tr>


<?php
$DB->getResults($frmportalmodify_getModules,$DB->escape($id));
while ($row=$DB->fetch(0))
{
	echo '<tr><td><img src="../images/ico_right_arrow.gif" /> '.$row["name"].'</td></tr>';
}
$DB->freeResults();

$DB->close();
?>


	</table>
	</td>
	</tr>
</table>
</form>
</body>
</html>