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
# Users criteria
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="A";
$pagename="admin/xml_user_loadexistingcriteria.php";

//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');
require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');

	$DB->getResults($criteria_getInformations);
	$nbUpdate=$DB->nbResults();
	if($nbUpdate>0)
	{
		echo "<b>".lg("existingCriterias")."</b><br />";
		echo "<input type='hidden' name='updateAction' value='".$nbUpdate."' />";
	}
	$i=0;
	while ($row=$DB->fetch(0))
	{
		$id=$row["id"];
		$label=$row["label"];
		$type=$row["type"];
		$options=$row["options"];
		$mandatory=$row["mandatory"];
		$editable=$row["editable"];
	
		echo "<input type='hidden' name='updateId".$i."' value='".$id."' />";
		echo "<br /><table cellpadding='2' cellspacing='0' border='0'><tbody>";
		echo "<tr><td>".lg('lblLabel')." : </td><td><input type='text' name='updateLab".$i."' value='".$label."' size='20' maxlength='30' />&nbsp;&nbsp;<a href='#' onclick=if(confirm(lg('msgSuppressCriteriaConfirm')))\$p.admin.users.deleteCriteria(".$id."); >".lg('lblDelete')."</a>";
		echo "<tr><td>".lg('lblType')." : </td><td><select name='updateTyp".$i."' disabled=true><option value='1'".($type==1?" selected='selected'":"").">Text</option><option value='2'".($type==2?" selected='selected'":"").">List</option><option value='3'".($type==3?" selected='selected'":"").">Checkbox</option><option value='4'".($type==4?" selected='selected'":"").">Radio</option><option value='5'".($type==5?" selected='selected'":"").">Textarea</option></select></td></tr>";
		echo "</tr><td>".lg('lblValues')." :</td><td><input type='text' size='40' disabled=true name='updateOpt".$i."' value='".$options."'/></td></tr>";
		echo "<tr><td>".lg('lblMandatory')." : </td><td><select name='updateMan".$i."'><option value='1'".($mandatory==1?" selected='selected'":"").">Oui</option><option value='0'".($mandatory==0?" selected='selected'":"").">Non</option></select></td></tr>";
		echo "<tr><td>".lg('lblEditable')." : </td><td><select name='updateEdi".$i."'><option value='1'".($editable==1?" selected='selected'":"").">Oui</option><option value='0'".($editable==0?" selected='selected'":"").">Non</option></select></td></tr>";
		echo"</tbody></table><hr width='35px'>";
		$i++;
	}
	$DB->freeResults();
	
$DB->close();
?>