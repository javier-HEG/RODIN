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
# Favorites module PHP scripts
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

header("content-type: application/xml"); 
$folder="";
$not_access=1;
$pagename="modules/wid_links.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header("channel");

if (isset($_GET['getText']) && isset($_GET['linkid']) && isset($_SESSION['user_id'])) {

    $linkid = $_GET["linkid"];
    $sharedmd5key=isset($_GET['sharedmd5key'])?$_GET['sharedmd5key']:'';
    $widgetid = isset($_GET['widgetid'])?$_GET['widgetid']:0;
    $favid = isset($_GET['favid'])?$_GET['favid']:0;

    if ($linkid!=0) {
        if( (empty($sharedmd5key))
        || (!isset($sharedmd5key)) 
        || ($sharedmd5key=='undefined') ) {
        $DB->getResults($wid_getUserLinks,
                            $DB->escape($favid),
                            $DB->escape($_SESSION['user_id'])
                       );
        }
        else {
            $DB->getResults($wid_getUserLinksShared,
                                $DB->quote("%linkid=".$DB->escape($linkid)."%"),
                                $DB->quote($sharedmd5key),
                                $DB->escape($_SESSION['user_id']),
                                $DB->escape($widgetid),
                                $DB->escape($favid)
                            );
        }
        $inc=0;
        while ($row = $DB->fetch(0)){
            $db_link_id = $row['link_id'];
            $db_name = $row['name'];
            $db_url = $row['url'];
            $db_tags = $row['tags'];
            echo "<link>";
            echo "<linkid>".$db_link_id."</linkid>";
            echo "<name><![CDATA[".$db_name."]]></name>";
            echo "<url><![CDATA[".$db_url."]]></url>";
            echo "<tags><![CDATA[".$db_tags."]]></tags>";
            echo "</link>";
            $inc++;
        }
        $DB->freeResults();
    }    
}
else {
    
    $act=$_POST["act"];
    $id=$_POST["modid"];

    if ($act=="sup")
    {
    	$DB->execute($widlink_removeLink,$DB->escape($_POST["linkid"]),$DB->escape($_SESSION['user_id']));
    	if ($DB->nbAffected()==0)
    	{
    		$DB->execute($widlink_createNewId,$DB->escape($_SESSION['user_id']));
    		$oldid=$id;
    		$id=$DB->getId();
    		$DB->execute($widlink_copyLinks,$id,$DB->escape($oldid),$DB->escape($_POST["linkid"]));
    		echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
    	}
    }
    if ($act=="add")
    {
    	$linkid=$_POST["linkid"];
    	if ($id==0)
    	{
    		$DB->execute($widlink_addNewLinkId,$DB->escape($_SESSION['user_id']));
    		$id=$DB->getId();
    		$DB->execute($widlink_addNewLink,$DB->escape($id),$DB->noHTML($_POST["name"]),$DB->noHTML($_POST["link"]),$DB->noHTML($_POST["tags"]));
    	}
    	else
    	{
    		//edition or adding
    		if ($linkid=="")
    		{
    			$DB->execute($widlink_addNewLinkOnExistingMod,$DB->escape($id),$DB->noHTML($_POST["name"]),$DB->noHTML($_POST["link"]),$DB->noHTML($_POST["tags"]),$DB->escape($id),$DB->escape($_SESSION['user_id']));
    		}
    		else
    		{
    			$DB->execute($widlink_updateLink,$DB->noHTML($_POST["name"]),$DB->noHTML($_POST["link"]),$DB->noHTML($_POST["tags"]),$DB->escape($linkid),$DB->escape($_SESSION['user_id']));
    		}
    		if ($DB->nbAffected()==0)
    		{
    			$DB->execute($widlink_createNewMod,$DB->escape($_SESSION['user_id']));
    			$oldid=$id;
    			$id=$DB->getId();
    			$DB->execute($widlink_insertExistingLink,$DB->escape($id),$DB->escape($oldid));
    			if ($linkid=="")
    			{
    				$DB->execute($widlink_newModInsertLink,$DB->escape($id),$DB->noHTML($_POST["name"]),$DB->noHTML($_POST["link"]),$DB->noHTML($_POST["tags"]));
    			}
    			else
    			{
    				$DB->execute($widlink_newModUpdateLink,$DB->noHTML($_POST["name"]),$DB->noHTML($_POST["link"]),$DB->noHTML($_POST["tags"]),$DB->escape($linkid));
    			}
    		}
    	}
    	echo '<ret>'.$id.'_'.($DB->getId()).'</ret>';
    }
}

$DB->close();

$file->status(1);

$file->footer();
?>