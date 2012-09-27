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
# Create a new user portal
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$pagename="portal/scr_createportal.php";
$not_access=0;
$isScript=true;
$isPortal=false;
//includes
require_once('includes.php');
require_once('../includes/pagegeneration.inc.php');
require_once('../includes/xml.inc.php');

launch_hook('scr_createportal');

$file=new xmlFile();

$file->header();

$idUser=0;
if (isset($_SESSION['user_id'])) {
$idUser=$_SESSION['user_id'];
}
else if (isset($_SESSION['temp_id'])) {
$idUser=$_SESSION['temp_id'];
}

echo '<ret><![CDATA[';

	//Check that the max portal number is not reached ================ TO SUPPRESS ?????? ============
	$DB->getResults($scrcreateportal_getTabs,$DB->escape($idUser));
	$row=$DB->fetch(1);
	$seq=$row[0]+1;
	$DB->freeResults();

	//register the portal general information
	$url="notused";
	$pageid=isset($_POST["pageid"])?$_POST["pageid"]:0;
	
  //page width
	$w=isset($_POST["w"])?$_POST["w"]:3;
	//page stylesheet nb
	$s=isset($_POST["s"])?$_POST["s"]:1;
	$t=isset($_POST["t"])?$_POST["t"]:0;
	$nb=isset($_POST["nb"])?$_POST["nb"]:15;
	// page icon
	$icon=isset($_POST["i"])?$_POST["i"]:"";
	// page name
	$n=(isset($_POST["n"]) AND $_POST["n"]!="" AND $_POST["n"]!="nouveau")?$_POST["n"]:"page ".$seq;
	// page type (1: personalizable 2: html page 3:javascript 4:redirection)
	$ty=isset($_POST["ty"])?$_POST["ty"]:1;
	$p=isset($_POST["p"])?$_POST["p"]:"";
	// modules aligned
	$a=isset($_POST["a"])?$_POST["a"]:"Y";
	$removable=isset($_POST["removable"])?$_POST["removable"]:1;
	
// Commented BY EMA > Do not work if I add an empty page in a portal
//	$DB->getResults($getUserpage,$DB->escape($idUser),$DB->escape($pageid));
//	if($DB->nbResults()==0)
//	{

	$seq_=		$DB->escape($seq);
	$icon_=		$icon;

	$DB->execute($fri_scrcreateportal_addTab,
											$DB->escape($idUser),
											$DB->quote($n),
											$DB->escape($w),
											$DB->escape($s),
											$DB->quote($url),
											$DB->quote($t),
											$DB->escape($nb),
											$DB->escape($seq),
											$DB->quote($icon),
											$DB->quote($a),
											$DB->quote($ty),
											$DB->quote($p),
											$DB->escape($pageid),
											$DB->escape($removable)
								);
        $prof=$DB->getId();
/*
$QUERY=vsprintf($fri_scrcreateportal_addTab,
							array(
								$DB->escape($idUser),
											$DB->quote($n),
											$DB->escape($w),
											$DB->escape($s),
											$DB->quote($url),
											$DB->quote($t),
											$DB->escape($nb),
											$DB->escape($seq),
											$DB->quote($icon),
											$DB->quote($a),
											$DB->quote($ty),
											$DB->quote($p),
											$DB->escape($pageid),
											$DB->escape($removable)
							)
						);
*/

		//register the modules selected and define their place in the page
		$inc=0;
		while (isset($_POST["id".$inc]) AND !Empty($_POST["id".$inc]))
		{
			//get feed id from the variables
			preg_match('/pfid=(\d+)/',$_POST["var".$inc],$matches);
			$fid=(count($matches)>0)?$matches[1]:0;

			$DB->execute($createportal_addModule,$DB->escape($_POST["id".$inc]),$DB->quote($idUser),$DB->escape($prof),$DB->escape($_POST["col".$inc]),$DB->escape($_POST["pos".$inc]),$DB->escape($_POST["posj".$inc]),$DB->escape($_POST["x".$inc]),$DB->escape($_POST["y".$inc]),$DB->quote($_POST["var".$inc]),($inc+1),$DB->escape($_POST["blocked".$inc]),$DB->escape($_POST["minimized".$inc]),$fid);
			$inc++;
		}
		echo $prof;
//	}
	
echo ']]></ret>';

$file->status(1);

$file->footer();

$DB->close();
?>