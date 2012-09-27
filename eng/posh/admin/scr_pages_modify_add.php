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
# POSH Pages management - Apply tabs changes
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_pages_modify_add.php";
//includes
require_once("includes.php");
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("pagemodifadd");

launch_hook('admin_scr_pages_modify_add');

$pageid=$_POST["id"];
$oldpageid=$pageid;
$pageIcon="";
if(isset($_POST['hiddenIconValue']))
$pageIcon = $_POST['hiddenIconValue'];
if(isset($_POST['formaction']))
$formaction=$_POST['formaction'];
$tabUsers = Array(); 
$tabProfile = Array();
$tabProfileId = Array();

//Save page information
if ($_POST["act"]=="upd")
{
	//step 1 :  get the 'seq' in pages
	$DB->getResults($pages_getSequence,$DB->escape($pageid));
	$row = $DB->fetch(0);
	$seq=$row["seq"];
	$DB->freeResults();
	//delete the current page in table 'pages'
	$DB->execute($pages_removePage,$DB->escape($oldpageid));
	//create new page in table 'pages' with same seq than the old one
	$DB->execute($pages_addNew,$DB->escape($_POST["group"]),$DB->quote($_POST["name"]),$DB->quote($_POST["desc"]),$DB->escape($_POST["mode"]),$DB->escape($_POST["type"]),$DB->quote($_POST["param"]),$DB->escape($seq),$DB->quote($pageIcon),$DB->escape($_POST["removable"]));
	$pageid=$DB->getId();

	//if checkbox checked, replace previous tab version .
	if(isset($formaction) && $_POST["mode"]==1)
	{	
		//delete the modules linked to the page (table 'modules')
		$DB->getResults($users_getProfileId,$DB->escape($oldpageid));
		while ($row = $DB->fetch(0))
		{
			$profile_id=$row["id"];
			array_push($tabProfileId,$profile_id);
		}
		$DB->freeResults();
		for ($i=0;$i<sizeof($tabProfileId);$i++)
		{
			$DB->execute($users_deleteProfileModules,$DB->escape($tabProfileId[$i]));
     	}

		//retreive the id of the users of the current group  
		if ($_POST["group"]!=0) {
			$DB->getResults($users_getIdGroup,$DB->escape($_POST["group"]));
			while ($row=$DB->fetch(0))
			{
				$user_id=$row['user_id'];
				array_push($tabUsers,$user_id);
			}
			$DB->freeResults();	
		}
		//retreive all users id
		else {
			$DB->getResults($users_getId);
			while ($row=$DB->fetch(0))
			{
				$user_id=$row['id'];
				array_push($tabUsers,$user_id);
			}
			$DB->freeResults();	
		}

		for ($i=0;$i<sizeof($tabUsers);$i++)
		{
			$seq_profile=0;
			$DB->getResults($pages_getUserPageSequence,$DB->escape($oldpageid),$DB->escape($tabUsers[$i]));
			if ($DB->nbResults()>0) {
				$curs = $DB->fetch(0);
				if ($curs["seq"]!=NULL || $curs["seq"]!=0)	{ $seq_profile=$curs["seq"]; }
				$DB->execute($profile_addPage,$DB->escape($tabUsers[$i]),$DB->quote($_POST['name']),$DB->escape($_POST['nbcol']),$DB->escape($_POST['style']),$DB->quote($_POST['controls']),$DB->escape($_POST['showtype']),$DB->escape($seq_profile),$DB->quote($pageIcon),$DB->quote($_POST['modulealign']),$DB->escape($_POST["type"]),$DB->quote($_POST["param"]),$DB->escape($pageid),2,$DB->escape($_POST["removable"]));	
				$profileid=$DB->getId();
				array_push($tabProfile,$profileid);
			}
			$DB->freeResults();	
            
            //delete the old profile pages
            $DB->execute($users_deleteToUpdateTabs,$DB->escape($oldpageid),$DB->escape($tabUsers[$i]));
		} 
	}
	else {
		$DB->execute($pages_setProperties,$DB->quote($_POST["name"]),$DB->quote($_POST["desc"]),$DB->escape($_POST["mode"]),$DB->escape($_POST["type"]),$DB->quote($_POST["param"]),$DB->quote($pageIcon),$DB->escape($pageid));
		$DB->execute($profile_updatePageid,$DB->escape($pageid),$DB->escape($oldpageid));
	}
		           
	//save the modules information for portal creation
	if ($_POST["type"]==1) {
		// page configuration
		$DB->execute($pages_updateSubProperties,$DB->escape($_POST['nbcol']),$DB->escape($_POST['showtype']),$DB->escape($_POST['npnb']),$DB->escape($_POST['style']),$DB->quote($_POST['modulealign']),$DB->quote($_POST['controls']),$DB->escape($pageid));
		$DB->execute($pages_removeModules,$DB->escape($oldpageid));
     	
		// create the new modules selection
		$inc=0;			
		while (isset($_POST["i".$inc]))
		{
			$DB->execute($pages_addModules,$DB->escape($_POST["i".$inc]),$DB->escape($pageid),$DB->escape($_POST["c".$inc]),$DB->escape($_POST["p".$inc]),$DB->escape($_POST["pj".$inc]),$DB->escape($_POST["x".$inc]),$DB->escape($_POST["y".$inc]),$DB->quote($_POST["v".$inc]),($inc+1),$DB->escape($_POST["b".$inc]),$DB->escape($_POST["m".$inc]));
			if (isset($formaction) && $formaction==3)
			{
                for ($j=0;$j<sizeof($tabUsers);$j++)
                {
                    $DB->execute($scrconfigplace_addNewMod,$DB->escape($_POST["i".$inc]),$DB->escape($tabUsers[$j]),$DB->escape($tabProfile[$j]),$DB->escape($_POST["c".$inc]),$DB->escape($_POST["p".$inc]),$DB->escape($_POST["pj".$inc]),$DB->escape($_POST["x".$inc]),$DB->escape($_POST["y".$inc]),$DB->quote($_POST["v".$inc]),($inc+1),$DB->escape($_POST["b".$inc]),$DB->escape($_POST["m".$inc]));
                }
			}	
			$inc++;
		}
	}	
}
//creation page
else
{
	//retreive the incremented seq number
	$DB->getResults($pages_getMaxSeq,$DB->escape($_POST["group"]));
	$row = $DB->fetch(0);
	$seq=$row["nseq"]==""?1:$row["nseq"];
	$DB->freeResults();

	$DB->execute($pages_addNew,$DB->escape($_POST["group"]),$DB->quote($_POST["name"]),$DB->quote($_POST["desc"]),$DB->escape($_POST["mode"]),$DB->escape($_POST["type"]),$DB->quote($_POST["param"]),$DB->escape($seq),$DB->quote($pageIcon),$DB->escape($_POST["removable"]));
	$pageid=$DB->getId();
	
	//if 'apply to all portals' is checked
	if ( (isset($formaction)) && ($formaction==2) && ($_POST['mode']==1) )
	{
		$tabUsers = Array(); 
		$tabProfile = Array();
		//retreive the id of the users of the current group  
		if ($_POST["group"]!=0)
		{
			$DB->getResults($users_getIdGroup,$DB->escape($_POST["group"]));
			while ($row=$DB->fetch(0))
			{
				$user_id=$row['user_id'];
				array_push($tabUsers,$user_id);
			}
			$DB->freeResults();	
		}
		//retreive all users id
		else
		{
			$DB->getResults($users_getId);
			while ($row=$DB->fetch(0))
			{
				$user_id=$row['id'];
				array_push($tabUsers,$user_id);
			}
			$DB->freeResults();	
		}
			
		for ($i=0;$i<sizeof($tabUsers);$i++)
		{
			$seq_profile=0;
			$DB->getResults($profile_getMaxSeq,$DB->escape($tabUsers[$i]));
			if ($DB->nbResults()>0) 
			{
				$curs = $DB->fetch(0);
				if ($curs["nseq"]!=NULL)
					$seq_profile=$curs["nseq"];
			}
			//add the page in profile
			$DB->execute($profile_addPage,$DB->escape($tabUsers[$i]),$DB->quote($_POST['name']),$DB->escape($_POST['nbcol']),$DB->escape($_POST['style']),$DB->quote($_POST['controls']),$DB->escape($_POST['showtype']),$DB->escape($seq_profile),$DB->quote($pageIcon),$DB->quote($_POST['modulealign']),$DB->escape($_POST["type"]),$DB->quote($_POST["param"]),$DB->escape($pageid),1,$DB->escape($_POST['removable']));	
			$profileid=$DB->getId();
			array_push($tabProfile,$profileid);
			$DB->freeResults();
		} 
	}
	//save the modules information for portal creation
	if ($_POST["type"]==1)
	{
		// page configuration
		$DB->execute($pages_updateSubProperties,$DB->escape($_POST['nbcol']),$DB->escape($_POST['showtype']),$DB->escape($_POST['npnb']),$DB->escape($_POST['style']),$DB->quote($_POST['modulealign']),$DB->quote($_POST['controls']),$DB->escape($pageid));
		// suppress the previous modules selection
		$DB->execute($pages_removeModules,$DB->escape($pageid));
        
		// create the new modules selection
		$inc=0;
		while (isset($_POST["i".$inc]))
		{
			$DB->execute($pages_addModules,$DB->escape($_POST["i".$inc]),$DB->escape($pageid),$DB->escape($_POST["c".$inc]),$DB->escape($_POST["p".$inc]),$DB->escape($_POST["pj".$inc]),$DB->escape($_POST["x".$inc]),$DB->escape($_POST["y".$inc]),$DB->quote($_POST["v".$inc]),($inc+1),$DB->escape($_POST["b".$inc]),$DB->escape($_POST["m".$inc]));
			if (isset($formaction) && $formaction==2)
			{
				for ($j=0;$j<sizeof($tabUsers);$j++)
				{
					$DB->execute($scrconfigplace_addNewMod,$DB->escape($_POST["i".$inc]),$DB->escape($tabUsers[$j]),$DB->escape($tabProfile[$j]),$DB->escape($_POST["c".$inc]),$DB->escape($_POST["p".$inc]),$DB->escape($_POST["pj".$inc]),$DB->escape($_POST["x".$inc]),$DB->escape($_POST["y".$inc]),$DB->quote($_POST["v".$inc]),($inc+1),$DB->escape($_POST["b".$inc]),$DB->escape($_POST["m".$inc]));
				}
			}	
			$inc++;
		}
	}
}
$file->status(1);

$file->footer();
//header("location:pages_tabs.php?group=".$_POST["group"]); 
?>