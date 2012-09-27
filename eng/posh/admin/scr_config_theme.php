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
# POSH Configuration - set theme
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted="A";
$pagename="admin/scr_config_theme.php";
	
//includes
require_once("includes.php");
require_once('../../app/exposh/l10n/'.__LANG.'/admin.lang.php');
require_once('../includes/file.inc.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("theme");

launch_hook('admin_scr_config_theme');

if (isset($_POST["template"]))  {   
    $DB->execute($config_setTemplate,$DB->quote($_POST["template"]));  
}
else    {	
    if (isset($_POST["delete"]))    {
        if(isset($_POST["todelete"]))   {
            if(!isset($_POST["existingThemes"]))    {  unlink("../styles/themes/".$_POST["todelete"].".thm");  }
        }
    }
    else    {
        //remove old theme selection
        $DB->execute($configtheme_removeAll);
        //add theme selection
        $inc=0;
        $themeSelectedName=array();
        while (isset($_POST["theme".$inc]))
        {
            $DB->execute($configtheme_addNewTheme,$DB->quote($_POST["theme".$inc]),$inc);
            array_push($themeSelectedName,$_POST["theme".$inc]);

            if (!copy("../styles/themes/".$_POST["theme".$inc].".thm", "../styles/main".($inc+1).".css"))   {
                //echo lg("noThmProcess")."<br />";
                //<A href='config_theme.php'>".lg("backPrevPage")."</A>";
                exit();
            }
            $inc++;
        }

        //$DB->execute($configtheme_updateThemeNumber,$inc);
        $DB->execute($configtheme_updateConfigVariable,$DB->quote(implode("','",$themeSelectedName)));
        if ($DB->nbAffected()==0)   { $DB->execute($configtheme_insertConfigVariable,$DB->quote(implode("','",$themeSelectedName))); }
        $DB->execute($configtheme_updateDefTheme,$DB->quote($_POST["theme0"]));
        if ($DB->nbAffected()==0)  {  $DB->execute($configtheme_insertDefTheme,$DB->quote($_POST["theme0"]));  }
    }
}	

$file->status(1);

$file->footer();

//header("location:scr_config_generate_configfiles.php?redirect=config_theme");
?>