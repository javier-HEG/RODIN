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
# Share items with users
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_shareitem.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("notification");

$secured = isset($_POST["secured"])?$_POST["secured"]:"0";
$prof = isset($_POST["prof"])?$_POST["prof"]:"";
$uniq = isset($_POST["uniq"])?$_POST["uniq"]:"";

// if shared object is a module
if ($_POST["obj"]=="m")
{
//	if (!isset($_POST["id"])) exit();
//	if (!isset($_POST["v"])) exit();
//	$id=$_POST["id"];
//	$var=urlencode($_POST["v"]);
//	$chk=$secured=="1"?md5(uniqid(0)):"";

	//add module in shared objects list
	//$DB->execute($scrsendtofriend_shareModule,$DB->quote($chk),$DB->escape($id),$DB->quote($var));
    //get shared
    $DB->getResults($scrsendtofriend_getSharedValue,
                    $DB->escape($_SESSION['user_id']),
					$DB->escape($prof),
					$DB->escape($uniq));
	$row = $DB->fetch(0);
	$shared=$row["shared"];
	$DB->freeResults();
    if( empty($shared) ) {
    	$DB->execute($scrsendtofriend_shareModule,
    					$DB->quote($secured),
    					$DB->escape($_SESSION['user_id']),
    					$DB->escape($prof),
    					$DB->escape($uniq));
    } else {
        $secured = $shared;
    }
//	echo "<widgetchk>".$secured."</widgetchk>";
}
elseif ($_POST["obj"]=="p")
{ 

/*// if shared object is a portal
	//if (!isset($_POST["prof"]) || !isset($_POST["nbcol"]) || !isset($_POST["style"]) || !isset($_POST["mode"])) exit();
	if (!isset($_POST["prof"]) || !isset($_POST["mode"])) exit();
	$prof=$_POST["prof"];
	//$nbcol=$_POST["nbcol"];
	//$style=$_POST["style"];
	$mode=$_POST["mode"];
	//$label=$_POST["label"];

	//add portal to shared objects list
	//$DB->execute($scrsendtofriend_sendPortal,$DB->quote($label),$_SESSION['user_id'],$DB->escape($nbcol),$DB->escape($style),$DB->escape($mode));
	$DB->execute($scrsendtofriend_sendPortal,$_SESSION['user_id'],$DB->escape($mode),$DB->escape($prof),$_SESSION['user_id']);
	$portalid=$DB->getId();
	echo "<portalid>".$portalid."</portalid>";
	
	//if public sharing
	if (isset($_POST["portname"]))
	{
		// create copy of the portal shared
		//$linkid=$portalid;
		//add portal information (script also used in scr_sendtofriends.php script)
		$DB->execute($scrsendtofriend_sharePublicPortal,$DB->noHTML($_POST["portname"]),$DB->noHTML($_POST["portdesc"]),"",$DB->escape($portalid));

//	//		$ret.=lg("proposedSharing")."<br />";
//		$check="";
//		
//		$DB->execute($scrsendtofriend_sharePublicPortal,$DB->quote($_POST["portname"]),$DB->quote($_POST["portdesc"]),$check,$portalid);
//		
//		//add keywords
//		if ($_POST["kw"]!=""){
//			$keyword=explode(",",$_POST["kw"]);
//			for ($i=0;$i<count($keyword);$i++){
//				$selkw=$keyword[$i];
//				$DB->getResults($scrsendtofriend_getKeyword,$selkw);
//				if ($DB->nbResults()==0){
//					$DB->execute($scrsendtofriend_addNewKeyword,$selkw);
//					$kwid=$DB->getId();
//				} else {
//					$row = $DB->fetch(0);
//					$kwid=$row["id"];
//				}
//				$DB->freeResults();

//				$DB->execute($scrsendtofriend_insertKeyword,$portalid,$kwid);
//			}
//		}
	}
	if (!isset($_POST["portname"]) || (isset($_POST["portname"]) && $secured==1))
	{
		$check=md5(uniqid(0));
		echo "<portalchk>".$check."</portalchk>";
		$DB->execute($scrsendtofriend_sharePrivPortal,$DB->quote($check),$DB->escape($portalid));
	}

	//add widgets in shared objects list
	$DB->execute($scrsendtofriend_sharePortalModules,$DB->escape($portalid),$DB->escape($_SESSION['user_id']),$DB->escape($prof));
*/
	//share the portal
	$DB->execute($scrsendtofriend_setPortalSharingInfo,
					$DB->quote($secured),
					$DB->escape($_SESSION['user_id']),
					$DB->escape($prof));
	//share the widgets of the shared portal
	$DB->execute($scrsendtofriend_setWidgetsAsShared,
					$DB->quote($secured),
					$DB->escape($_SESSION['user_id']),
					$DB->escape($prof));
	//save tags
	if (isset($_POST["portname"]))
	{
		if ($_POST["kw"]!=""){
			$keyword=explode(",",$_POST["kw"]);
			for ($i=0;$i<count($keyword);$i++){
				$selkw=$keyword[$i];
				$DB->getResults($scrsendtofriend_getKeyword,$DB->quote($selkw));
				if ($DB->nbResults()==0){
					$DB->execute($scrsendtofriend_addNewKeyword,$DB->quote($selkw));
					$kwid=$DB->getId();
				} else {
					$row = $DB->fetch(0);
					$kwid=$row["id"];
				}
				$DB->freeResults();

				$DB->execute($scrsendtofriend_insertKeyword,$DB->quote($portalid),$DB->quote($kwid));
			}
		}
	}
}

$DB->close();
print("<secured>$secured</secured>");
$file->status(1);

$file->footer();
?>