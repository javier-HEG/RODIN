<?php
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
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
// Admin functions

$pagename="../includes/admin_tools.php";
$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted='A';

require_once('../includes/modules_tools.php');

/**
    \name copyFromTempExternalLanguage
    
    \brief copy entries from temp_dir_item_external_language to dir_item_external_language
    
    @param[in] $validatedId new module id, $modid is item_id of temp table
**/
function copyFromTempExternalLanguage($validatedId,$modid) 
{
    global $DB2,$modules_addTempDirItemExternalLanguage,$modules_deleteTempDirItemExternalLanguage;
    $DB2->execute($modules_addTempDirItemExternalLanguage,$DB2->escape($validatedId),$DB2->escape($modid));
    $DB2->execute($modules_deleteTempDirItemExternalLanguage,$DB2->escape($modid));
}
/**
    \name loadDatasToGenerateCacheFiles
    
    \brief get widget informations in dir_item_external_language to generate cache file
    
    @param[in] $id module id, $format widget format
**/
function loadDatasToGenerateCacheFiles($id,$format)
{
    global $DB2,$modules_getDirItemExternalLanguage,$module_getSource;
    $hash=array();
    if ($format!="U" && $format!="R") {
        $DB2->getResults($modules_getDirItemExternalLanguage,$DB2->escape($id));

        if ($DB2->nbResults()>0) {
            while($row = $DB2->fetch(0))
            {   
                $hash['format']=$format;
                $hash['widget_id']=$id;
                $hash['lang']=$row['lang'];
                $hash['source']=$row['source'];
                $hash['viewtype']=$row['viewtype'];
                $hash['view']=$row['view'];
                if ($hash['viewtype']!="U") {
                    $hash['filename']=buildCacheFileName($hash); 
                    deleteCacheFile($hash);
                    createCacheFile($hash);
                }                
            }
            $DB2->freeResults();
        }

        //get also datas if widget is not localized
       $DB2->getResults($module_getSource,$DB2->escape($id)); 
       if ($DB2->nbResults()>0) {
           $row = $DB2->fetch(0);
           $hash['format']=$format;
           $hash['widget_id']=$id;
           $hash['lang']='';
           $hash['source']=$row['source'];
           $hash['view']=$row['view'];
            $hash['filename']=buildCacheFileName($hash);
            deleteCacheFile($hash);
            createCacheFile($hash);
           
       }

    }
}

function getUserByUsername ($username) {
    global $DB;
    global $users_getMd5user;
    $chk = $DB->getResults(
                $users_getMd5user, 
                    $DB->quote($username)
                );
    if ($DB->nbResults()>0) {            
        $row = $DB->fetch(0);        
        return array($row['id'],$row['username']);
    }

}

/*! getAdminPages

    /brief get admin pages from database and return array hash 
    
*/
function getAdminPages ($datas) {
    $DB      = $datas['db'];
    $user_id = $datas['user_id'];
    global $tabs_getUpdateAdminTabs;
    $pageList = array();
    $DB->getResults($tabs_getUpdateAdminTabs,$DB->escape($user_id));
    while ($row = $DB->fetch(0))
    {   
        $pageList[$row["tab_id"]] = 1;
    }
    return $pageList;    
}
/*!
    Function createParamL10nFiles
*/
function copyParamL10nFiles ($id,$newid) {
        global $__AVLANGS;
        foreach ( $__AVLANGS as $lang) { 
            $filename = "../modules/tmp_module".$id."_".$lang."_param.xml";
            
            if (  file_exists($filename) ) {
                $newfilename = "../modules/module".$newid."_".$lang."_param.xml";
                copy($filename,$newfilename);
                @chmod($newfilename, 0766);
            }
        }
}
function createParamL10nFiles ($newid) {
        global $__AVLANGS;
        foreach ( $__AVLANGS as $lang) { 
                $newfilename = "../modules/module".$newid."_".$lang."_param.xml";
                copy($filename,$newfilename);
                @chmod($newfilename, 0766);
        }
}
        

?>