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
# Expert modules - Generate the module file and save module information
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$pagename="modules/xml_getwidget.php";
//include_once('../../app/exposh/l10n/'.__LANG.'/tutorial.lang.php');
require_once('../includes/config.inc.php');
$folder="";
$not_access=0;
$isScript=true;
$isPortal=false;
$granted='I';

require_once('../includes/connection_'.__DBTYPE.'.inc.php');
require_once('../includes/session.inc.php');
require_once('../db_layer/'.__DBTYPE.'/widget.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../includes/modules_tools.php');

global $DB;
$DB = new connection(__SERVER,__LOGIN,__PASS,__DB);

require_once('../includes/file.inc.php');
require_once('../includes/xml.inc.php');

$internationalized=false;
$content="";
$getParam = isset($_GET['getparam']) ?  $_GET['getparam'] : '';
$getSource = isset($_GET['getsource']) ?  $_GET['getsource'] : '';
$dir_item_id =  isset($_GET['pitem']) ?  $_GET['pitem'] : '';
$format =  isset($_GET['format']) ?  $_GET['format'] : '';
$p_tab = isset($_GET['p']) ?  $_GET['p'] : 0;
$getView = isset($_GET['view']) ?  $_GET['view'] : 'home';
$l10n = isset($_GET['l10n']) ?  $_GET['l10n'] : '';
$code = isset($_POST['code']) ? $_POST['code'] : null;
$prof = isset($_GET['prof']) ? $_GET['prof'] : 0;
$useCache = isset($_GET['useCache']) ? $_GET['useCache'] : 0;

$lang='en';

if ( !isset($l10n) || $l10n=='' || $l10n=='undefined' ) {
    $internationalized=false;
}
else if (isset($_SESSION['lang']) && (strpos($l10n,$_SESSION['lang']) > 0)) {
    $internationalized=true;
    $lang=$_SESSION['lang'];
}
else {
    $internationalized=true;
    $lang=__LANG;
}

$lang = isset($_GET['plg']) ? $_GET['plg'] : $lang;

//get xml only
$getxml=null;
$env = isset($_GET['env']) ? $_GET['env'] : null;
if ( $env && $env == 'tut' ) {
    $gettmpxml = isset($_GET['getxml']) ? $_GET['getxml'] : null;
} else {
    $getxml = isset($_GET['getxml']) ? $_GET['getxml'] : null;
}

$widHash['widget_id']=$dir_item_id;
if (isset($l10n) && ( $l10n == 'undefined' || $l10n == ''  ) ) {
    $widHash['lang']='';
} else {
    $widHash['lang']=$lang;
}
$widHash['format']=$format;
$widHash['view']=$getView;
$widHash['db'] = $DB;

$content="";
if (isset($_SERVER['HTTP_REFERER'])) {
    if (!preg_match('/tutorial/xmsi',$_SERVER['HTTP_REFERER']) || $useCache==1) {
        $content = getCacheFile($widHash);
    }
}

if ($format == "M" && $content!="") {
    //M cache exists, send cache
    //create cache file ?
    $file=new xmlFile();
    $content = replaceModuleJSVars($content,$prof,$p_tab);
    $content = replaceUPVars($content);
    /*
    $content = preg_replace(
                                '/__MODULE_ID__/xmsi',
                                $p_tab,
                                $content);     
*/                                
/*
    $content=preg_replace(
                            '/\"/xsmi',
                            '"',
                            $content);         
*/                            
    echo $content;
} 
else if($format == 'I' && $content!="") {
/*
    $content=preg_replace(
                            '/\"/xsmi',
                            '"',
                            $content);
                            */
/*
    $content = preg_replace(
                                '/__MODULE_ID__/xmsi',
                                $p_tab,
                                $content);         
*/                                
  //  $content = str_replace("\'", "'", $content);
   // $content = str_replace('\"', "'", $content);
    $content = replaceModuleJSVars($content,$prof,$p_tab);
    $content = replaceUPVars($content);
    echo $content;
}
else if (isset($code) && $code) {                            
    $file=new xmlFile();
    echo $code;   
} 
else if ( $getSource && $internationalized ) {

    $hash=array();
    $hash['widget_id']=$dir_item_id;
    $hash['lang']=$lang;
    $hash['view']=$getView;
    $hash['db']=$DB;
    
    $widgetInfos=getInternationalizedSource($widHash);

    if ($format == "M") {
        $widHash['source'] = $widgetInfos['source'];
        createCacheFile($widHash);
        $file=new xmlFile();
        $content = replaceModuleJSVars($widgetInfos['source'],$prof,$p_tab);
        $content = replaceUPVars($content);
        echo  $content;
    } 
    else if( $format == 'I' || $format == 'U' ) {
        $widHash['source'] = $widgetInfos['source'];
        if($widgetInfos['viewtype']=="I") {                 
            createCacheFile($widHash);
            $content = replaceModuleJSVars($widgetInfos['source'],$prof,$p_tab);
            $content = replaceUPVars($content);
            echo $content;   
        }  
        else if ($widgetInfos['viewtype']=="U") {
            $url = $widgetInfos['url'];
            $url = rewriteUrl($url);
            header('Location: '.$url);
        }
    }
    else { echo lg("lblNoModule"); }
}
else if ( $getSource && !$internationalized ) {

    //get datas from database about this module, type format, groups etc
    $DB->getResults(
                    $tutorial_getSourceAndView,
                    $DB->escape($dir_item_id),
					$DB->quote($getView)
                    ); 

    $result=$DB->nbResults();
    if ($result==0) {
        $DB->freeResults();
        $DB->getResults(
                    $tutorial_getTmpSourceAndView,
                    $DB->escape($dir_item_id),
					$DB->quote($getView)
                    ); 

       $result=$DB->nbResults();
    }     
    
    if ($result>0) {
        $row=$DB->fetch(0);
        //then extract content
        if ($format == "M") {
            $widHash['source'] = $row['xmlmodule'];
            createCacheFile($widHash);
            $file=new xmlFile();
            $content = replaceModuleJSVars($row['xmlmodule'],$prof,$p_tab);
            $content = replaceUPVars($content);
            echo $content;
        } 
        else if( $format == 'I' || $format == 'U' ) {
            $content_type=$row['type'];
            if($content_type=="I") {
                $source = $row['source'];                      
                $source = preg_replace(
                            '/__INSCRIPT_MODULE_ID_/xmsi',
                            $p_tab,
                            $source);  
                $widHash['source'] = $source;
                createCacheFile($widHash); 
                $source = replaceModuleJSVars($source,$prof,$p_tab);    
                $source = replaceUPVars($source);
/*                
    $source=preg_replace(
                            '/\"/xsmi',
                            '"',
                            $source);     
*/                            
                echo $source;
            }  
            else if ($content_type=="U") {
                $url = $row['url'];
                $url = rewriteUrl($url);
                header('Location: '.$url);
            }
		}
    } else {
        echo lg("lblNoModule");
    }
    $DB->freeResults();
} else if ( $getParam  ) {    
    //remove le content
} else if ( isset($getxml)  ) {  
        //only admin can get xml code
        $DB->getResults(
                $tutorial_getXMLCode,
                $dir_item_id
                );
        $row=$DB->fetch(0);        
        $file=new xmlFile();
        echo $row['xmlmodule'];
} else if ( isset($gettmpxml)  ) {  
        //only admin can get xml code
        $DB->getResults(
                $tutorial_getXMLCode_temp,
                $dir_item_id
                );
        $row=$DB->fetch(0);        
    $file=new xmlFile();
    echo $row['xmlmodule'];
} else {
    //return source from widget
}
?>