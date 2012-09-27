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
# POSH Module management - Directory move form
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=false;
$isPortal=false;
$granted="A";
$pagename="admin/xml_directory_move.php";
$tabname="modulestab";

require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("dirmove");


if (isset($_GET["treated"]))
{
	echo lg("modifProcessed");
	echo '<script type="text/javascript">$p.admin.widgets.refreshDir();</script>';
}
else
{
?>

<script type="text/javascript">
	var catid=$p.admin.widgets.catid;
	var catname=$p.admin.widgets.catname;

	function directoryMoveInit(){
		var l_s="<b>"+lg("lblMoveDir")+" <font color='#ff0000'>"+catname+"</font> : </b>"+lg("lblSelectDir","<I>"+catname+"</I>")+" <input type='hidden' name='catid' value='"+catid+"' /><input type='hidden' name='parentid' value='0' /><input type='submit' value='"+lg("lblMove")+"' />";
		$p.print("movediv",l_s);
	}
	function getNewParent(){
		var l_error=false,l_step=0;
		if ($p.admin.widgets.currLevel==0 || $p.admin.widgets.currLevel>3){l_error=true;l_step="1 ("+$p.admin.widgets.currLevel+")";}
		for (var i=1;i<=$p.admin.widgets.currLevel;i++){
			if (catid==$p.admin.widgets.levCatid[i]){l_error=true;l_step=2;}
		}
		var l_id=$p.admin.widgets.catid;
		if (l_id==0){l_error=true;l_step=3;}
		if (l_error){
			alert(lg("msgIncorrectFolder")+" ! (err "+l_step+")");
			return false;
		} else {
			document.forms["f"].parentid.value=l_id;
			return true;
		}
	}
</script>
<form name="f" action="scr_directory_move.php" method="post" onsubmit="return getNewParent()">
<div id="movediv" style="padding:10px"></div>
</form>
<script type="text/javascript">
directoryMoveInit();
</script>


<?php
}
?>


</body>
</html>