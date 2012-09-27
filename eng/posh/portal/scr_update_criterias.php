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
#  update my profile criterias informations
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$granted="I";
$isScript=true;
$isPortal=false;
$pagename="portal/scr_update_criterias.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("updateCriterias");

$user_id=$_SESSION['user_id'];

$nbSpecificFields=$_POST['nbSpecificFields'];

//specific criterias for the user
if ($nbSpecificFields!=0)   {
    for ($i=1;$i<=$nbSpecificFields;$i++)
    {
        $parameters="";
        $id=$_POST["uniq_id".$i];
        if (isset($_POST["userinfo".$i]))   {
            if (is_array($_POST["userinfo".$i]))    {
                for ($j=0;$j<count($_POST["userinfo".$i]);$j++)
                {	
                    if ($j==0)	{ $parameters = $_POST["userinfo".$i][$j]; }
                    else   { $parameters .= ";".$_POST["userinfo".$i][$j]; }
                }	
            }	
            else    {
                if ($_POST["userinfo".$i]!="")	{ $parameters=$_POST["userinfo".$i]; }
            }
        }					
        //sql query to add	
        $DB->execute($users_updateUserInfos,$DB->quote($parameters),$DB->escape($user_id),$DB->escape($id));
    }
}

$file->status(1);

$file->footer();

$DB->close();
?>