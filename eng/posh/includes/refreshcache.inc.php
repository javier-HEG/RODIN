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
# Cache managemement functions
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

include_once('../includes/pagegeneration.inc.php');

function refresh_item($v_itemid,$v_folder)
{
	global $DB,$refreshcache_setModuleAsUpdated;
	//generate the xml feed
	$_GET['modid']=$v_itemid;
	$script_file=$v_folder . "xml_cache_item.php";
	$cache_file="../cache/item_".$v_itemid.".xml";	 
	cacheDataInFile($script_file, $cache_file);
    //flag the updated data with Y
	$DB->execute($refreshcache_setModuleAsUpdated,$DB->escape($v_itemid));
}

// Refresh the directory parts impacted
function refresh_directory($v_catid,$v_folder,$v_lang)
{
	global $DB2,$refreshcache_getLang,$refreshcache_getModuleNbInDir,$refreshcache_getSubDirNb,$refreshcache_setQuantity,$refreshcache_getParentDirectory,$refreshcache_setDirectoryAsUpdated;
	$loopCatid = $v_catid;
	$inc = 0;
	if ($v_catid != 0)
	{
		
        $DB2->getResults($refreshcache_getLang,$DB2->escape($loopCatid));
        $row2 = $DB2->fetch(0);
        if ($v_lang == "")
            $v_lang = $row2["lang"];
        $secured = $row2["secured"];
        $DB2->freeResults();
        
		while ($loopCatid != 0 && $inc != 10)
		{
			$DB2->getResults($refreshcache_getModuleNbInDir,$DB2->escape($loopCatid));
			$row2 = $DB2->fetch(1);
			$itemquantity = ($secured == "0" ? $row2[0] : 0);
            $itemSecuredquantity = ($secured == "0" ? 0 : $row2[0]);
			$DB2->freeResults();
	
			$DB2->getResults($refreshcache_getSubDirNb,$DB2->escape($loopCatid));
			$row2 = $DB2->fetch(1);
			$catquantity=$row2[0];
            $catSecuredquantity=$row2[1];
			$DB2->freeResults();

			$totquantity = $itemquantity + $catquantity;
            $totSecuredquantity = $itemSecuredquantity + $catSecuredquantity;

			$DB2->execute($refreshcache_setQuantity,
                            $DB2->escape($totquantity),
                            $DB2->escape($totSecuredquantity),
                            $DB2->escape($loopCatid));

			//generate the xml feed
            if ($secured == 0)
            {
    			$_GET['catid'] = $loopCatid;
    			$script_file = $v_folder."xml_cache_directory.php";
    			$cache_file = "../cache/cat_".$loopCatid."_".$v_lang.".xml";
    			cacheDataInFile($script_file, $cache_file);
            }

			//select the parent for next loop
			$DB2->getResults($refreshcache_getParentDirectory,$DB2->escape($loopCatid));
			$row2 = $DB2->fetch(1);
			$loopCatid=$row2[0];
			$DB2->freeResults();

			$inc++;
		}
	}
	//generate root directory
	$_GET['catid']=0;
	$script_file=$v_folder."xml_cache_directory.php";
	$cache_file="../cache/cat_0.xml";
	cacheDataInFile($script_file, $cache_file);
	//flag the updated data with 'Y'
	$DB2->execute($refreshcache_setDirectoryAsUpdated,$DB2->escape($v_catid));
}

function refresh_portal($v_id,$v_folder)
{
	global $DB;
	//generate the xml feed
	$_GET['id']=$v_id;
	$script_file=$v_folder . "xml_cache_portal.php";
	$cache_file="../cache/portal_".$v_id.".xml";				
	cacheDataInFile($script_file, $cache_file);
}
function refresh_portal_directory($v_id,$v_folder,$DB,$v_lang,$dirtable)
{
	global $DB,$refreshcache_getPortalLang,$refreshcache_getParentId;
	if ($v_lang=="")
	{
		$DB->getResults($refreshcache_getPortalLang,$DB->escape($dirtable),$DB->escape($v_id));
		$row = $DB->fetch(0);
		$v_lang=$row["lang"];
		$DB->freeResults();
	}
	//generate the xml feed
	$loopCatid=$v_id;
	$inc=0;
	while ($loopCatid!=0&&$inc!=10)
	{
		$_GET['id']=$loopCatid;
		$script_file=$v_folder . "xml_cache_portaldir.php";
		$cache_file="../cache/portaldir_".$loopCatid."_".$v_lang.".xml";				
		cacheDataInFile($script_file, $cache_file);
		
		//select the parent for next loop
		$DB->getResults($refreshcache_getParentId,$DB->escape($dirtable),$DB->escape($loopCatid));
		$row = $DB->fetch(1);
		$loopCatid=$row[0];
		$DB->freeResults();
		
		$inc++;
	}
	//for users groups, generate the root directory
	if ($dirtable=="users_group")
	{
		$_GET['id']=0;
		$script_file=$v_folder . "xml_cache_portaldir.php";
		$cache_file="../cache/portaldir_0_".$v_lang.".xml";				
		cacheDataInFile($script_file, $cache_file);
	}
}
?>