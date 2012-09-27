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
# Insert a new module in a portal
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$destprof=$_POST["dest"];
$id=$_POST["id"];
$profile_id=$_POST["profile_id"]; //FRI rfb id des neuen tab
$orig=$_POST["orig"]; //FRI - db id tab des originaeren Tab


$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_addmodule_intab.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

launch_hook('scr_addmodule_intab');

$file=new xmlFile();

$file->header();

$DB->getResults($scrintab_getNewPos,$DB->escape($destprof),$DB->escape($_SESSION['user_id']));
$row=$DB->fetch(0);
$newpos=$row["newpos"];
if (empty($newpos))$newpos=1;
$DB->freeResults();


		$userid_=	  	$DB->escape($_SESSION['user_id']);
		$desprof_=		$DB->escape($destprof);
		$newpos_=  		$DB->escape($newpos);
		$vars_=				$DB->quote($_POST["vars"]);
		$fid_	=				$DB->escape($_POST["fid"]);
		$id_ =				$DB->escape($id);


/*
$QUERY=vsprintf($fri_scrintab_addModule,
							array(
								$DB->escape($_SESSION['user_id']),
								$DB->escape($profile_id),
								$DB->quote($_POST["vars"]),
								$DB->escape($_POST["fid"]),
								$DB->escape($id),
								$DB->escape($_SESSION['user_id']),
								$DB->escape($orig)
							)
						);

$h = fopen("scr_addmodule_intab.txt","w");
$params=<<<EOT
		$userid_
		$profile_id
		$newpos_
		$vars_
		$fid_
		-----
		
		$fri_scrintab_addModule
		$QUERY
EOT;
fwrite($h,$params);
fclose($h);
*/

//add the module in the new tabs
$DB->execute($fri_scrintab_addModule,
								$DB->escape($_SESSION['user_id']),
								$DB->escape($profile_id),
								$DB->quote($_POST["vars"]),
								$DB->escape($_POST["fid"]),
								$DB->escape($id),
								$DB->escape($_SESSION['user_id']),
								$DB->escape($orig)
						);

if ($DB->nbAffected()==1)
{
	$file->status(1);
	$file->returnData('x_'.$newpos.'_'.$_POST["tabdest"]);
}
else
{
	$file->status(0);
}
$file->footer();

$DB->close();
?>