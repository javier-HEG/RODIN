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
$pagename="tutorial/scr_savewidget.php";
require_once('../includes/config.inc.php');
$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted=__USERMODULE;

require_once('includes.php');
include_once('../../app/exposh/l10n/'.__LANG.'/tutorial.lang.php');
require_once('../includes/file.inc.php');
require_once('../includes/xml.inc.php');
require_once('../includes/http.inc.php');
require_once('../includes/log.inc.php');
require_once('../includes/misc.inc.php');
include('../includes/modules_tools.php');
include('../includes/fixmagic.php');


$file=new xmlFile();
$file->header();
if ($_SESSION['type'] == 'I' && $granted  == 'A' && $granted != $_SESSION['type'] ) {
    //return error message
    exit;
}

$status = 'quarantine';
$validate = isset($_POST["validate"]) ? $_POST["validate"] : '';
$id = isset($_POST['id']) ? $_POST['id'] : exit ;
$format = isset($_POST["format"]) ? $_POST["format"] : '';
$type = isset($_POST["type"]) ? $_POST["type"] : 'html';
$toupdate=0;
$url = ( isset($_POST['url']) && $_POST['url'])   ? $_POST['url'] :  '' ;
$icon = ( isset($_POST['icon']) && $_POST['icon'])   ? $_POST['icon'] :  '' ;
$pfid = ( isset($_POST['pfid']) && $_POST['pfid'])   ? $_POST['pfid'] : -1 ;
$l10nSelected = ( isset($_POST['l10nSelected']) )   ? substr($_POST['l10nSelected'],0,-1) :  '' ;
$l10nSelected = explode(",",$l10nSelected);
$code=isset($_POST['code']) ? $_POST['code'] : exit ;
if ( !isUTF8($code)) {
    $code = utf8_encode($code);
}

global $unsecured_widget;
if ( $code ) {
    $coderet = $_SESSION['type']=="A"?checkWidgetContents($code,$format):secureWidgetCode($code,$format);
}
if ($unsecured_widget) {
    echo "<unsecured><![CDATA[".lg($unsecured_widget)."]]></unsecured>";
    $file->status(0);
    $file->footer();
    exit;
}

if (isset($_POST['defvar'])) {
    $_POST['defvar']=preg_replace("/%3D/xmsi","=",$_POST['defvar']);
    $_POST['defvar']=preg_replace("/%26/xmsi","&",$_POST['defvar']);
    $_POST['defvar']=preg_replace("/%25/xmsi","%",$_POST['defvar']);
}

$unsecured_widget=null;
$contents=getContents($code);
$views=getAllViews($contents);
// Logo is not in dir_item
// Nor keywords
$params_array = array();
if (strlen($id) > 10) {
	// set id icon in dir_rss
	$DB->execute($dir_rss_setIconIdByPFID,$DB->quote($icon),$DB->escape($pfid));
    // Insert into base temp_dir_item 
	// To create a widget : save date in dir_item
    $DB->execute($tutorial_addModule,
                $DB->quote(   $DB->escape( $url ) ),
                $DB->quote($DB->escape( (( isset($_POST['defvar']) && $_POST['defvar'] ) ? $_POST['defvar'] : '' )  )),
                $DB->noHTML(htmlspecialchars(( isset($_POST['name']) ? $_POST['name'] : '' ),ENT_QUOTES,'UTF-8')),
                $DB->noHTML(htmlspecialchars(( isset($_POST['description']) ? $_POST['description'] : '' ),ENT_QUOTES,'UTF-8')),  
                $DB->quote($DB->escape(( isset($_POST['status']) ? $_POST['status'] : '' )) ),
                $DB->quote($DB->escape(( isset($_POST['format']) ? $_POST['format'] : '' )) ),
                $DB->escape(( isset($_POST['height']) ? $_POST['height'] : '' )),
                $DB->escape(( isset($_POST['minwidth']) ? $_POST['minwidth'] : '' )),
                $DB->escape(( isset($_POST['sizable']) ? $_POST['sizable'] : '' )),
                $DB->quote($DB->escape(( (isset($_POST['website']) && $_POST['website']) ? $_POST['website'] : '' ))),
                $DB->escape(( isset($_POST['editor_id']) ? $_POST['editor_id'] : '' )),
                $DB->escape(( isset($_POST['nbvariables']) ? $_POST['nbvariables'] : '' )),
                $DB->quote($DB->escape(( isset($_POST['lang']) ? $_POST['lang'] : '' ))),
                $DB->quote($DB->escape(( isset($_POST['icon']) ? $_POST['icon'] : '' ))),
                $DB->quote($DB->escape(( (isset($_POST['keyword']) && $_POST['keyword']) ? $_POST['keyword'] : '' ))),
                $DB->quote($DB->escape(( (isset($_POST['keywords']) && $_POST['keywords']) ? $_POST['keywords'] : '' ))),
                $DB->escape(( isset($_POST['autorefresh']) ? $_POST['autorefresh'] : '' ) ),
                $DB->quote($views)
        );
    $id=$DB->getId();
    
    $datas = insertInTempDirItemExternal($contents,$id,$format,$code,0,1,$DB);
    $external_source = $datas[0];
    $external_xmlmodule = $datas[1];
    $contentsByView = $datas[2];
    $contentsComplementaryInfos = $datas[3];
    $prefs_hash = getModulesPrefAttributes($external_xmlmodule);
    $require_hash = getRequireParams($external_xmlmodule);
    //insert external language in external_language table
    //get datas from datas saved
    $errors = array();
    $lang_hash = getUrlLocales($external_xmlmodule,1,$errors);

    if ( !isset($errors[0]) ) {
        
        $params_array = savel10nContent( array(
                            'lang_hash'             => $lang_hash,
                            'contents'              => $contents,
                            'id'                    => $id,
                            'l10nSelected'          => $l10nSelected,
                            'external_source'       => $external_source,
                            'external_xmlmodule'    => $external_xmlmodule,
                            'format'                => $format,
                            'prefs_hash'            => $prefs_hash,
                            'contentsByView'        => $contentsByView,
                            'require_hash'          => $require_hash,
                            'complementaryInfos'    => $contentsComplementaryInfos,
                            'action'                => 'insert',
                            'db'                    => $DB
                            )
                        );
    }
    
    if ($format == 'I' || $format == 'U') {
        if ($type == "url") {
            if ( preg_match('/\?/',$url) ) {
                if ( !preg_match('/\?$/',$url) ) {
                    if ( !preg_match('/\&$/',$url) ) {
                        $url = $url . "&";
                    }
                }
            } else {
                $url = $url . "?";
            }
            $DB->execute(
                    $tutorial_updateUrl,
                        $DB->quote($url),
                        $id
                );
        } else {
            $DB->execute(
                    $tutorial_updateUrl,
                        $DB->quote("$url?getsource=1&pitem=$id&format=I&"),
                        $id
                );
        }
        createParamXMLFile ($id,$params_array,$code);
        
    }
    if ( $format == 'M' ) {
            $DB->execute(
                    $tutorial_updateUrl,
                        $DB->quote("$url?getxml=1&format=M&pitem=$id&"),
                        $id
             );
    }
    // Insert or update cat
    $DB->execute($tutorial_insertTempDirCat,
                 $id,
                 $DB->escape(( isset($_POST['category_id']) ? $_POST['category_id'] : '' ))
                );
                            
    // If user feed, map it with module
    if ( isset($_POST["fid"]) &&  $_POST["fid"]!="0") {
        $DB->execute($scrrssadd1_addRedactorLink,$DB->escape($id),$DB->escape($_POST["fid"]));
    }
    if ( $format == "R" ) {
        // Insert or update rss  url in dir_rss, oups non! c'est enregistré avant
    }
	$_SESSION['moduleValidatedModify'] = 0;
} 
else {
		// Update  the data in the table temp_dit_item after the test step
		if( $validate == '' || ( isset($_SESSION['moduleValidatedModify']) and $_SESSION['moduleValidatedModify']==1 ) ) {
        
            $DB->execute($tutorial_updateModule,
		            $DB->quote($DB->escape( (( isset($_POST['defvar']) && $_POST['defvar'] ) ? $_POST['defvar'] : '' )  )),
		            $DB->noHTML(htmlspecialchars(( isset($_POST['name']) ? $_POST['name'] : '' ),ENT_QUOTES,'UTF-8')),
		            $DB->noHTML(htmlspecialchars(( isset($_POST['description']) ? $_POST['description'] : '' ),ENT_QUOTES,'UTF-8')),                
		            $DB->quote($DB->escape(( isset($_POST['status']) ? $_POST['status'] : '' )) ),
		            $DB->quote($DB->escape(( isset($_POST['format']) ? $_POST['format'] : '' )) ),
		            $DB->escape(( isset($_POST['height']) ? $_POST['height'] : '' )),
		            $DB->escape(( isset($_POST['minwidth']) ? $_POST['minwidth'] : '' )),
		            $DB->escape(( isset($_POST['sizable']) ? $_POST['sizable'] : '' )),
		            $DB->quote($DB->escape(( (isset($_POST['website']) && $_POST['website']) ? $_POST['website'] : '' ))),
		            $DB->escape(( isset($_POST['editor_id']) ? $_POST['editor_id'] : '' )),
		            $DB->escape(( isset($_POST['nbvariables']) ? $_POST['nbvariables'] : '' )),
		            $DB->quote($DB->escape(( isset($_POST['lang']) ? $_POST['lang'] : '' ))),
		            $DB->quote($DB->escape(( (isset($_POST['icon']) && $_POST['icon']) ? $_POST['icon'] : '' ))),
		            $DB->quote($DB->escape(( (isset($_POST['keyword']) && $_POST['keyword']) ? $_POST['keyword'] : '' ))),
		            $DB->quote($DB->escape(( (isset($_POST['keywords']) && $_POST['keywords']) ? $_POST['keywords'] : '' ))),
		            $DB->escape(( isset($_POST['autorefresh']) ? $_POST['autorefresh'] : '' ) ),
		            $DB->quote($views),
					$DB->escape($id)
		    );

            $datas = insertInTempDirItemExternal($contents,$id,$format,$code,0,2,$DB);
            $external_source = $datas[0];
            $external_xmlmodule = $datas[1];
            $contentsByView = $datas[2];
            $contentsComplementaryInfos = $datas[3];

            $prefs_hash = getModulesPrefAttributes($external_xmlmodule);
            $require_hash = getRequireParams($external_xmlmodule);
            if ( $format == 'M' ) {
                    $replace = 0;
                    $url = preg_replace('/getsource/','getxml', $url,-1,$replace);
                    if ($replace > 0) {
                        $url .= "&format=M&pitem=$id&";
                    } else {
                        $url = "$url?getxml=1&format=M&pitem=$id&";
                    }
                    $DB->execute(
                            $tutorial_updateUrl,
                                $DB->quote($url),
                                $id
                     );
            }            

            
            //insert external language in external_language table
            //get datas from datas saved
            $errors = array();
            $lang_hash = getUrlLocales($external_xmlmodule,1,$errors);
            if ( !isset($errors[0]) ) {
                        $params_array = savel10nContent( array(
                            'lang_hash'             => $lang_hash,
                            'contents'              => $contents,
                            'id'                    => $id,
                            'l10nSelected'          => $l10nSelected,
                            'external_source'       => $external_source,
                            'external_xmlmodule'    => $external_xmlmodule,
                            'format'                => $format,
                            'prefs_hash'            => $prefs_hash,
                            'contentsByView'        => $contentsByView,
                            'require_hash'          => $require_hash,                            
                            'complementaryInfos'    => $contentsComplementaryInfos,
                            'action'                => 'update',
                            'db'                    => $DB
                            )
                        );
            }            
            
			unset($_SESSION['moduleValidatedModify']); 
            
	    } else {
			// To modify a widget
			// Delete a record from the table temp_dir_item and  temp_dir_item_external when modifying a widget
			$_SESSION['moduleValidatedModify'] = 1;
			$idModuleValidated = $id;
		    // Delete previous entries in the temp_dir_item
			$DB->execute($tutorial_DeleteTemp_Dir_Item,$DB->escape($id));
			// Delete previous entries in the temp_dir_item_external
			$DB->execute($tutorial_DeleteTemp_Dir_Item_External,$DB->escape($idModuleValidated));
		    // Insert into base temp_dir_item 
		    $DB->execute($tutorial_addModuleModify,
		                $DB->quote(   $DB->escape( $url ) ),
		                $DB->quote($DB->escape( (( isset($_POST['defvar']) && $_POST['defvar'] ) ? $_POST['defvar'] : '' )  )),
		                $DB->noHTML(htmlspecialchars(( isset($_POST['name']) ? $_POST['name'] : '' ),ENT_QUOTES,'UTF-8')),
		                $DB->noHTML(htmlspecialchars(( isset($_POST['description']) ? $_POST['description'] : '' ),ENT_QUOTES,'UTF-8')),  
		                $DB->quote($DB->escape(( isset($_POST['status']) ? $_POST['status'] : '' )) ),
		                $DB->quote($DB->escape(( isset($_POST['format']) ? $_POST['format'] : '' )) ),
		                $DB->escape(( isset($_POST['height']) ? $_POST['height'] : '' )),
		                $DB->escape(( isset($_POST['minwidth']) ? $_POST['minwidth'] : '' )),
		                $DB->escape(( isset($_POST['sizable']) ? $_POST['sizable'] : '' )),           
		                $DB->quote($DB->escape(( (isset($_POST['website']) && $_POST['website']) ? $_POST['website'] : '' ))),
		                $DB->escape(( isset($_POST['editor_id']) ? $_POST['editor_id'] : '' )),
		                $DB->escape(( isset($_POST['nbvariables']) ? $_POST['nbvariables'] : '' )),
		                $DB->quote($DB->escape(( isset($_POST['lang']) ? $_POST['lang'] : '' ))),
		                $DB->quote($DB->escape(( isset($_POST['icon']) ? $_POST['icon'] : '' ))),
		                $DB->quote($DB->escape(( (isset($_POST['keyword']) && $_POST['keyword']) ? $_POST['keyword'] : '' ))),
		                $DB->quote($DB->escape(( (isset($_POST['keywords']) && $_POST['keywords']) ? $_POST['keywords'] : '' ))),
		                $DB->escape(( isset($_POST['autorefresh']) ? $_POST['autorefresh'] : '' ) ),
		                $DB->quote($views),
						$DB->escape($id)
		        );
				$id=$DB->getId(); 
                //savel10nInfo($hash,$id);
               $datas = insertInTempDirItemExternal($contents,$id,$format,$code,$idModuleValidated,1,$DB);
             
                $external_source = $datas[0];
                $external_xmlmodule = $datas[1];
                $contentsByView = $datas[2];
                $contentsComplementaryInfos = $datas[3];
                $prefs_hash = getModulesPrefAttributes($external_xmlmodule);
                $require_hash = getRequireParams($external_xmlmodule);
                //insert external language in external_language table
                //get datas from datas saved
                $errors = array();
                $lang_hash = getUrlLocales($external_xmlmodule,1,$errors);
                if ( !isset($errors[0]) ) {
                        $params_array = savel10nContent( array(
                            'lang_hash'             => $lang_hash,
                            'contents'              => $contents,
                            'id'                    => $id,
                            'l10nSelected'          => $l10nSelected,
                            'external_source'       => $external_source,
                            'external_xmlmodule'    => $external_xmlmodule,
                            'format'                => $format,
                            'prefs_hash'            => $prefs_hash,
                            'contentsByView'        => $contentsByView,
                            'require_hash'          => $require_hash,
                            'complementaryInfos'    => $contentsComplementaryInfos,
                            'action'                => 'update',
                            'db'                    => $DB
                            )
                        );
                }    
                
			    $log=new log();
			    $log->write($DB,$_SESSION['longname'].lg("addedNewModule").' : '.$_POST['name'],$_SESSION['type']);
			    $log->generateRss($DB,md5(__KEY));
			   //insertInTempDirItemExternal($contents,$id,$format,$code,$idModuleValidated,1,$DB);
				// Insert or update cat
			    $DB->execute($tutorial_insertTempDirCat,
			                 $id,
			                 $DB->escape(( isset($_POST['category_id']) ? $_POST['category_id'] : '' ))
			                );
				if ($format == 'I' || $format == 'U') {			
                    $url = "../modules/xml_getwidget.php?getsource=1&pitem=$id&format=$format&";
                } else if ($format == 'M') {
                    $url = "../modules/xml_getwidget.php?getxml=1&pitem=$id&format=$format&";
                }
			    $DB->execute(
                    $tutorial_updateUrl,
                        $DB->quote($url),
                        $id
				);
		}
        
    if ($format=="I" || $format=="U") {
        createParamXMLFile ($id,$params_array,$code);
    }
}
	
    if ($type == "url") {
        if ( preg_match('/\?/',$url) ) {
                if ( !preg_match('/\?$/',$url) ) {
                    if ( !preg_match('/\&$/',$url) ) {
                        $url = $url . "&";
                    }
                }
            } else {
                $url = $url . "?";
            }
        $DB->execute(
            $tutorial_updateUrl,
            $DB->quote("$url"),
            $id
        );
    }     
    // Insert or update cat
    $DB->execute($tutorial_updateTempDirCat,
                    $DB->escape(( isset($_POST['category_id']) ? $_POST['category_id'] : '' )),
                    $DB->quote($DB->escape(( isset($_POST['lang']) ? $_POST['lang'] : '' ))),
                    $id,
                    $DB->escape(( isset($_POST['editor_id']) ? $_POST['editor_id'] : '' ))
                );
                 
echo "<dir_id>$id</dir_id>";

$DB->close();
$file->footer();
?>
