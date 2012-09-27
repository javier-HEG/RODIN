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
// Functions used in widget creation interfaces


$pagename="includes/modules_tools.php";
//require_once('../includes/config.inc.php');
$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$granted=__USERMODULE;

function Specialencode ($toencode) {
    $toencode = preg_replace(
                                   '/
                                   &
                                   /xmsi',
                                   '&amp;',
                                   $toencode);  
    return $toencode;
}
function Specialdecode ($todecode) {
    $todecode = preg_replace(
                                   '/
                                   &amp;
                                   /xmsi',
                                   '&',
                                    $todecode);  
    return  $todecode;                                       
}
/*! getParams

    /brief get xml params: userprefs
    
    all but content

*/
function getParams ($code) {
    $code = preg_replace('/<Content[^>]*>.*?<\/content>/xmsi','',$code);
    return $code;
}
/* 

    Get the different "content" tags of the widget
    
*/
function getContents($code) {
    $i=0;
    $generalContent=Array();
    preg_match_all("/
                        (<content[^>]*[^\/]>
                        .+?
                        <\/content>)
                   /xmsi",$code,$content); 
                   
    preg_match_all("/
                        (<content[^>]*\/>)
                   /xmsi",$code,$contentOther); 
    foreach($content[0] as $cle => $val)               
    {
        $generalContent[$i]=$val;
        $i++;
    }  
    foreach($contentOther[0] as $cle => $val)               
    {
        $generalContent[$i]=$val;
        $i++;
    }
    return $generalContent;    
}

function getHeaderContents ($code) {
    preg_match("/
                <\?xml[^>]*>
                 (.*?)
                    <content        
                   /xmsi",$code,$headers); 
    return $headers[1]; 
}

/**
    \name
    existsLang
    \brief
    Verify that the module is available for a specific language
     
    @param[in] $lang lang to check - $hashLang hash of the available languages 
    @param[out] true/false
**/
function existsLang($lang,$hashLang) 
{
    if (isset($hashLang[$lang])) {
        return true;
    }
    return false;  
}
/**
    \name
    getCacheFile
    \brief
    Get the cache file content
     
    @param[in] $hash widgetid,lang,format,view
    @param[out] string (file content)
 **/
function getCacheFile($hash)
{   
    $content = "";
    $hash['filename']=buildCacheFileName($hash);
    $filepath = '../cache/';
    $ext = ($hash["format"]=="M")?'.xml':'.html';
    $fullPath = $filepath.$hash['filename'].$ext;
    if (file_exists($fullPath)) {
        $fp=fopen($fullPath,"r");    
        while (!feof($fp)) { 
          $content .= fgets($fp, 4096);
        }
        fclose ($fp);
        return $content;
    }
    else {
        //create cache file
        return $content;
    }
    return "";
}
//TO DEPRECATE IN modules_updates_tools
/**
    \name
    createCacheFile
    \brief
    Create the cache file if not already existing
     
    @param[in] $hash filename,format,source,xmlmodule,view
    @param[out] 
**/
function createCacheFile($hash)
{   
    $hash['filename']=buildCacheFileName($hash);
    $filepath = '../cache/';
    $ext = '.html';
    $contentToWrite = $hash["source"];
    if ($hash["format"]=="M") {
        //$contentToWrite = $hash['xmlmodule'];
        $ext = '.xml';
    }
    $fullPath = $filepath.$hash['filename'].$ext;
    if (!file_exists($fullPath)) {
        $fp=fopen($fullPath,"w");
        fputs ($fp, $contentToWrite);     
        fclose ($fp);
         @chmod($fullPath, 0766);  
    }
}
function deleteCacheFile ($hash) {
    $hash['filename']=buildCacheFileName($hash);
    $filepath = '../cache/';
    $ext = '.html';
    $fullPath = $filepath.$hash['filename'].$ext;
    if ( file_exists($fullPath) ) {
        unlink( $fullPath );
    }
}

/**
    \name
    buildCacheFileName
    \brief
    Return the cache file name based on the parameters input
     
    @param[in] $hash widgetid,lang,format,view
    @param[out] MD5 filename
**/
function buildCacheFileName($fileHash)
{
    return md5(__KEY
              .$fileHash['widget_id']
              .$fileHash['lang']
              .$fileHash['format']
              .$fileHash['view']);
}

/**
    \name
    buildInternationalizedVersion
    \brief
    Replace the variables in the content by the translated labels
     
    @param[in] $hash labels and value, $content XML content
    @param[out] $iContent internationalized content 
**/
function buildInternationalizedVersion($labelsHash,$content)
{
    foreach ($labelsHash as $cle => $val) 
    {
        $nb=0;
        $regex = '__MSG_'.$cle.'__';
        $content = preg_replace("/$regex/xmsi",
                                            $val,
                                            $content
                                            
                                     );                          
    }    
    return $content;
}
/** 
    \name
    ParseCodeLocale
    \brief
    Get the msg names and value in the XML
     
    @param[in] $xmlCode XML to parse
    @param[out] Hash[name]=value  
**/
function ParseCodeLocale($xmlCode, $default_labelsHash = null)
{
    $tabMsg=array();
    
    preg_match_all('
                /
                <msg\s+                
                    name="([^"]*)"
                    [^>]*
                    >(.*?)<\/msg>
                /xsmi',$xmlCode,$msg,PREG_SET_ORDER);    
       
    foreach ($msg as $cle => $val) 
    {
        $val2 = $val[2];
        $val2 = preg_replace("/^(\r\n|\r|\n)/","",$val2);
        $val2 = preg_replace("/(\r\n|\r|\n)$/","",$val2);
       if ( !isUTF8($val2)) {
            $val2 = utf8_encode($val2);
        }
        $tabMsg[$val[1]]=$val2;     
    }
    if ($default_labelsHash!=null)  {
        foreach ( $default_labelsHash as $label => $value) {
            if (!isset($tabMsg[$label])) {
                $tabMsg[$label] = $value;

            }
        }
    }
    return $tabMsg;
}
/** 
     \name
     getAllViews
     \brief
     Get all the different views specified in the XML
     
    @param[in] $content XML content tags
    @param[out] String all views   
**/
function getAllViews($content) {
    $allviews='home';
    $multiviews=array();
    
    foreach ($content as $indice => $parameters)
    {
        preg_match('/
                            view="([^"]+)"
                    /xmsi',$parameters,$ct_view);      
        
        if(count($ct_view)>0) {  
            if (strpos($ct_view[1], ',') !== false) {
                $tmpViews = explode(",", $ct_view[1]);
                for ($j=0;$j<count($tmpViews);$j++) {
                    if ($tmpViews[$j]=="default" || $tmpViews[$j]=="profile") {
                        $tmpViews[$j]="home";
                    }
                    if (!in_array($tmpViews[$j], $multiviews)) {
                        array_push($multiviews,$tmpViews[$j]);
                    }
                } 
            } 
            else {
                if ($ct_view[1]=="default" || $ct_view[1]=="profile")
                $ct_view[1]="home";
                array_push($multiviews, $ct_view[1]);
            }
        }

        for($i=0;$i<count($multiviews);$i++)
        {
            if (strpos($allviews, $multiviews[$i]) === false) {
                $allviews.=",".$multiviews[$i];
            }  
        }
    } 
    return $allviews;
}   


/**

    Insert some widget informations in temp_dir_item_external
    
**/
function insertInTempDirItemExternal($contents,$id,$format,$code,$idModuleValidated,$queryType,$DB)
{
    global $tutorial_setTemp_Dir_Item_External,
    $tutorial_UpdateTemp_Dir_Item_External,
    $tutorial_getNbViews,
    $tutorial_getItemViews,
    $tutorial_DeleteTemp_Dir_Item_ExternalView;
    $external_source='';
    $external_xmlmodule='';
    $oldViews=array();
    $currentViews=array();
    $contentsByView = array();
    $contentsComplementaryInfos = array();

    $basename = "";
    $localfolder = "..";
    if (  preg_match( '/author_affiliation="Google\s[^"]*"/xmsi',$code)) {
        $basename = '<base href="http://www.google.com/" />';
        $localfolder = __LOCALFOLDER;
    }
    if ($queryType==1) {
        $DB->getResults($tutorial_getItemViews,$id);
        $row = $DB->fetch(0);
        $view = $row['view'];
        $oldViews = explode(",", $view);
    }
    if ($queryType==2) {
        $DB->getResults($tutorial_getNbViews,$id);
        while ($row = $DB->fetch(0)) {
            array_push($oldViews,$row['view']);
        }
    }
    foreach ($contents as $indice => $parameters)    
    {   
        $external_itemid=$id;
        $external_xmlmodule='';
        $external_url='';
        $external_view='';
        $external_type='';
        $external_source='';
        $multiviews=array();
        $htmlcode = <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
$basename
<title>Widget</title>
<link rel="stylesheet" type="text/css" media="screen" title="default" href="$localfolder/styles/module.css" />
<link rel="stylesheet" type="text/css" media="screen" title="default" href="$localfolder/styles/module1.css" />
<script type="text/javascript" src="$localfolder/includes/modules.js"></script>
<script type="text/javascript">
</script>
</head>
<body onload="_IG_AdjustIFrameHeight();">
__INSCRIPT_CONTENT_
</body>
</html>
EOT;
/*
        $parameters = preg_replace('/
                  \\\"
            /xmsi','"',$parameters);
            */
        // Get contents types
        preg_match('/
                          type="([^"]+)"
                    /xmsi',$parameters,$c_type);

        $external_type=$c_type[1];  
        
        if ($external_type=="url") {
            $external_xmlmodule=$code;  
           $external_type="U";
           // Get content href
           preg_match('/
                            href="([^"]+)"
                       /xmsi',$parameters,$c_href);  
           $external_url=$c_href[1];    
           
           if (substr($external_url,0,7)!="http://") {
               $external_url="http://".$external_url;
           }
        }
        elseif ($external_type=="html") {
            $external_type="I";
            //Get the xmlsource
            preg_match_all("/
                                <content[^>]*>(.+?)<\/content>
                            /xmsi",$parameters,$c_source); 
                            
            if ($format=="M") {
                    $external_xmlmodule=$code;
            }
            if ($format=="I") {
                $external_xmlmodule=$code;
                foreach ($c_source as $all => $ct_content)
                {
                    
                    $tmp_external_source=$ct_content[0];
                    $tmp_external_source = str_replace("<![CDATA[", "", $tmp_external_source);
                    $tmp_external_source = str_replace("]]>", "", $tmp_external_source);
                    if(!strpos($tmp_external_source,"builtin module")===false) {
                        $tmp_external_source=utf8_decode(lg("internalGoogleWidgetMsg"));
                    }

                    $external_source = Specialdecode($tmp_external_source);        
                    $external_source = preg_replace('/__INSCRIPT_CONTENT_/',$tmp_external_source,$htmlcode);
             //       $external_source = preg_replace("/'/xmsi","\'",$external_source);
                }
            }
        }     
        // Get content view
        preg_match('/
                        view="([^"]+)"
                    /xmsi',$parameters,$c_view);  

        if(count($c_view)>0) {
            if (strpos($c_view[1], ',') !== false) {
                $tmpViews = explode(",", $c_view[1]);
                for ($j=0;$j<count($tmpViews);$j++) {
                    if ($tmpViews[$j]=="default" || $tmpViews[$j]=="profile") {
                        $tmpViews[$j]="home";
                    }
                    if (!in_array($tmpViews[$j], $multiviews)) {
                        array_push($multiviews,$tmpViews[$j]);
                    }
                }
            } 
            else {
                if ($c_view[1]=="default" || $c_view[1]=="profile")
                $c_view[1]="home";
                array_push($multiviews, $c_view[1]);
                array_push($multiviews, $c_view[1]);
                array_push($currentViews, $c_view[1]);
            }
        }
        else {
            array_push($multiviews, "home");
            array_push($currentViews, "home");
        }
        
        $external_source2save = $external_source;
        $external_xmlmodule2save = $external_xmlmodule;
   
        if ( get_magic_quotes_gpc() && get_magic_quotes_gpc() == 1 ) { 
            $external_xmlmodule2save = preg_replace('!\\"!','\\\\"',$external_xmlmodule2save);
            $external_xmlmodule2save = preg_replace("!\\'!","\\\\'",$external_xmlmodule2save);
            $external_xmlmodule2save = preg_replace("!\\/!","\\\\/",$external_xmlmodule2save);
         
            $external_source2save = preg_replace("!\\'!","\\\\'",$external_source2save);
            $external_source2save = preg_replace('!\\"!','\\\\"',$external_source2save);    
            $external_source2save = preg_replace("!\\/!","\\\\/",$external_source2save); 
        }
/*
    for($i=0;$i<count($oldViews);$i++)
    {
        if (!in_array ($oldViews[$i], $currentViews)) {
            $DB->execute($tutorial_DeleteTemp_Dir_Item_ExternalView,$id,$DB->quote($oldViews[$i]));
        }      
    }
    */
        for($i=0;$i<count($multiviews);$i++)
        {
            
            $external_view=$multiviews[$i];
            if ($queryType==1) {

                $res = $DB->execute($tutorial_setTemp_Dir_Item_External,$DB->escape($external_itemid),
                                                                 $DB->quote($external_source2save),
                                                                 $DB->quote($external_xmlmodule2save),
                                                                 $DB->quote($external_url),
                                                                 $DB->quote($external_view),
                                                                 $DB->quote($external_type),
    															 $DB->escape($idModuleValidated)
    			 												 );       
            }
            else {
                $res = $DB->execute($tutorial_UpdateTemp_Dir_Item_External,
                                                    $DB->quote($external_source2save),
                                                    $DB->quote($external_xmlmodule2save),
                                                    $DB->quote($external_url),
                                                    $DB->quote($external_type),
                                                    $DB->escape($external_itemid),
                                                    $DB->quote($external_view)
                                                    );                                                 
           }
           $contentsByView[$external_view]=$external_source;
           $contentsComplementaryInfos['url']=$external_url;
           $contentsComplementaryInfos['type']=$external_type; 
        }             
    }


    
    return array($external_source,$external_xmlmodule,$contentsByView,$contentsComplementaryInfos);
}
/**
    \name getUrlLocales
    
    \brief get xmlcode, and get url locales if they exist
    
    @param[in] <xml code>, order,void array  reference to set errors
         
    @param[out]  true/false
    
    errors[0] == 401 //xml missing
    erros[0] == 402 //bad xml ou locale missing

    \details
    
    get datas, but if xml is not in normal order, get datas in other order
    
    $errors = array();  
    $lang_hash = getUrlLocales($xml,1,$errors);
    if ($errors[0] == 403) {
        $errors = array();
        $lang_hash = getUrlLocales($xml,2,$errors);
    }
    
**/
function getUrlLocales ($xml,$order,&$errors) {
    if (!$xml) {
        $errors[0] = 404;
        $errors[1] = "xml missing";
        return false;
    }
    $lang_hash = array();
    
    //control that one of the Locales syntax are correct
    preg_match_all('/
                        <Locale(.+?)>
                    /xmsi',$xml,$locales);  
          
    if (isset($locales[0])) { 
        $lang_temp = array();
        foreach($locales[0] as $cle => $val)               
        {
            preg_match_all('/
                        (\S+)="([^"]+)"
                            /xmsi',$val,$results,PREG_SET_ORDER); 
            foreach ($results as $indice => $res) {
                $param = $res[1];
                $value = $res[2];
                $lang_temp[$cle][$param] = $value;
            }
        }
        foreach ($lang_temp as $c => $array) {
            if (isset($array['lang'])) {
               $lang = $array['lang'];
               if (isset($array['country'])) {
                    $lang = "$lang-".$array['country'];
               }
               $lang_hash[$lang] = $array['messages'];
            } else {
                $lang_hash['en'] = $array['messages'];
                $lang_hash['default'] = $array['messages']; 
            }
        }
        return $lang_hash;
    } else {
        $errors[0] = 402;
        $errors[1] = "locales missing or bad xml ";        
        return $lang_hash;
    }

}

/*
    \brief setGoogleDomain
       
       For Google Widget url may be relative
       
       return absolute path

*/
function setGoogleDomain ($url,$require_hash) {
    if (isset($require_hash['internal-domain'])) {
        return $url;
    } 
    $googleDomain = 'http://www.google.com/ig/';
    $url2 = strtolower($url);
    if (strpos($url2,"http")===false) {
        if ( preg_match('/^\//',$url2)) {
            $googleDomain = 'http://www.google.com/';
            return "$googleDomain$url";
        } else {
            $googleDomain = 'http://www.google.com/ig/';
            return "$googleDomain$url";        
        }
    } else {
        return $url;
    }
    
}

/**
    \brief savel10nInfo
    
    save l10n info in database : table dir_item
    
    @param[in]    $lang_hash , $id_item,
    
     /brief 
     
        savel10nInfo($lang_hash,118);
        
        /see 
        
        getUrlLocales to get  $lang_hash
    
*/
function savel10nInfo ($lang_hash,$id,$DB) {
    $langs="";
    global $tutorial_updatel10n;
    
    foreach ($lang_hash as $lang => $url) {
        $langs .= "$lang,";
    }
    $langs = rtrim($langs,",");
    
    $DB->execute(
            $tutorial_updatel10n,
            $DB->quote($langs),
            $id
        );
            
}

/*

    \brief getLangFile
    
    @param[in] $lang_hash, $lang,&$errors
    
    @param[out] content of xml page 
    
    \see
    
      getUrlLocales to get  $lang_hash

*/
function getLangFile ($lang_hash,$lang,&$errors,$require_hash) {
    $url = $lang_hash[$lang];
    if (isset($require_hash['internal-domain'])) {
        $langfile=new file($url);
        return $langfile->read(); 
    }
    $h = new http($url);
    $content = $h->get(); 
    if ($h->code) {
        $errors['code'] = $h->code;
    }
    return $content;
}

/**
    \brief saveInternationalizedContent
    
    @param[in] $datas (hash)
    
    \description hash parameters
    
        array(
                                       'widget_id' => 12,
                                       'lang'   => 'fr',
                                       'url'    => '',
                                        'source' => $l10n_content,
                                        'view'  => 'home',
                                        'viewtype' => 'I'
                                       )
    

**/

function saveInternationalizedContent ($datas) {
    global $tutorial_insertTempL10NSource;
    $item_id = $datas['widget_id'];
    $lang = $datas['lang'];
    $source = $datas['source'];
    $url = $datas['url'];
    $view = $datas['view'];
    $viewtype = $datas['viewtype'];
    $params = $datas['params'];
    $DB = $datas['db'];
    //item_id,lang,url,source,view,viewtype,last_updated
    if ( get_magic_quotes_gpc() && get_magic_quotes_gpc() == 1 ) { 
        $source = preg_replace('!\\"!','\\\\"',$source);    
        $source = preg_replace("!\\'!","\\\\'",$source);
        $source = preg_replace("!\\/!","\\\\/",$source); 
    
        $params = preg_replace('!\\"!','\\\\"',$params);    
        $params = preg_replace("!\\'!","\\\\'",$params); 
        $params = preg_replace("!\\/!","\\\\/",$params);
    }
    $isOk = $DB->execute(
                   $tutorial_insertTempL10NSource,
                   $item_id,
                   $DB->quote($lang),
                   $DB->quote($url),
                   $DB->quote($source),
                   $DB->quote($view),
                   $DB->quote($viewtype),
                   $DB->quote($params)
                );

    return $isOk;
}

function updateInternationalizedContent ($datas) {
    global $tutorial_updateTempL10NSource;
    $item_id = $datas['widget_id'];
    $lang = $datas['lang'];
    $source = $datas['source'];
    $url = $datas['url'];
    $view = $datas['view'];
    $viewtype = $datas['viewtype'];
    $params = $datas['params'];
    $DB = $datas['db'];
   // $source = utf8_encode($source);
    //item_id,lang,url,source,view,viewtype,last_updated                         
    if ( get_magic_quotes_gpc() && get_magic_quotes_gpc() == 1 ) { 
        $source = preg_replace('!\\"!','\\\\"',$source);    
        $source = preg_replace("!\\'!","\\\\'",$source);  
        $source = preg_replace("!\\/!","\\\\/",$source); 
    
        $params = preg_replace('!\\"!','\\\\"',$params);    
        $params = preg_replace("!\\'!","\\\\'",$params); 
        $params = preg_replace("!\\/!","\\\\/",$params);
    }
    
    $isOk = $DB->execute(
                   $tutorial_updateTempL10NSource,
                   $DB->quote($source),
                   $DB->quote($params),
                   $DB->quote($viewtype),
                  $item_id,
                  $DB->quote($view),
                  $DB->quote($lang)
                );
    
    return $isOk;
}


/*

    \name saveSourceXMLCode
    
    \brief  save 

**/

function saveSourceXMLCode () {

}

/**

    \brief getInternationalizedSource
    
    @param[in] $datas hash
    
                        array(
                                       'widget_id' => 12,
                                       'lang'   => 'fr',
                                        'view'  => 'home'
                                       )

    @param[out] $datas hash
    
                        array(
                                       'source' => '<? xml ?>' or '<html>'
                                       'url'   => '' or 'http://www.google.fr'
                                        'viewtype'  => 'I' 'U'
                                       )

**/

function getInternationalizedSource ($datas) {
    global $tutorial_getL10NSource,$tutorial_getTempL10NSource;
    $item_id = $datas['widget_id'];
    $lang = $datas['lang'];
    $view = $datas['view'];
    $DB = $datas['db'];
    $widgetInfos = array();
    $source='';
    $widgetInfos["source"]="";
    $widgetInfos["viewtype"]="I";
    $widgetInfos["url"]="";
    
    $DB->getResults(
               $tutorial_getTempL10NSource,
               $item_id,
               $DB->quote($lang),
               $DB->quote($view)
            );   
    if ($DB->nbResults()==0) {
        $DB->freeResults();
        $DB->getResults($tutorial_getL10NSource,
           $item_id,
           $DB->quote($lang),
           $DB->quote($view)
        ); 
    }     
    if ($DB->nbResults() > 0 ) {
        $row=$DB->fetch(0);
        $source= $row['source'];
        $type= $row['viewtype'];
        $url= $row['url'];
        $widgetInfos["source"]=$source;
        $widgetInfos["viewtype"]=$type;
        $widgetInfos["url"]=$url;
    }
    $DB->freeResults();

    return $widgetInfos;
}
/**

    \brief getContentType
    
    Return the content 'type'
    
    @param[in] $parameters string

    @param[out] $c_type[1];  string ('url'/'html')

**/
function getContentType($parameters)
{
    preg_match('/
                      type="([^"]+)"
                /xmsi',$parameters,$c_type);   
    return $c_type[1];     
}
/**

    \brief getContentUrlParam
    
    Return the content 'href' parameter for a content type 'url'
    
    @param[in] $parameters string

    @param[out] $c_href[1];  string (www.website.com, http://www.website.com etc..)

**/
function getContentUrlParam($parameters) 
{

    preg_match('/
                    href="([^"]+)"
               /xmsi',$parameters,$c_href);       
    if (substr($c_href[1],0,7)!="http://") {
       $c_href[1]="http://".$c_href[1];
    }
    return $c_href[1];
}
/**

    \brief getContentViewParam
    
    Return the content 'view' parameter
    
    @param[in] $parameters string

    @param[out] $c_href[1];  string (default 'home')

**/
function getContentViewParam($parameters)
{
    $view='home';

    preg_match('/
                    view="([^"]+)"
                /xmsi',$parameters,$c_view);  
    if(count($c_view)>0) {
        $view = $c_view[1];
    }
    return $view;
}
/*
    /brief save title and desc in default portal language in dir_tem 

    __MSG_gadgetTitle__
    
*/
function updateL10nTitle ($lang,$labelsHash,$prefs_hash,$id,$DB) {
    global $__AVLANGS,$tutorial_updateTitle;
    $title = '';
    $desc = '';

    if (isset($prefs_hash['title'])) { $label_title = preg_replace('/__MSG_(.+)__/','\1',$prefs_hash['title']); }
    if (isset($prefs_hash['description'])) { $label_desc = preg_replace('/__MSG_(.+)__/','\1',$prefs_hash['description']); }
    
    $defaultlang = $__AVLANGS[0];
    if ($defaultlang != $lang ) {
        return;
    }
    if (  isset($labelsHash[$label_title]) ) {
        $title = $labelsHash[$label_title];
    }
    if (  isset($labelsHash[$label_desc]) ) {
        $desc = $labelsHash[$label_desc];
    }
    $desc = utf8_encode($desc);
    $title = utf8_encode($title);
    
    $res  = $DB->execute(
                $tutorial_updateTitle,
                    $DB->quote($title),
                    $DB->quote($desc),
                    $DB->escape($id)
            );
}

function getModulesPrefAttributes ($xml) {
    
    if ($xml=='') return "";
    preg_match_all(
                    '/
                    <ModulePrefs\s+
                        ([^>]+)
                        >
                    /xmsi',
                    $xml,
                    $array,
                    PREG_SET_ORDER
                    );
    $prefs = $array[0][1];
 
    preg_match_all(
                    '/
                    (\S+)="([^"]+)"
                    /xmsi',
                    $prefs,$array_pref,
                    PREG_SET_ORDER
                    );
    $prefs_hash=array();
    foreach ( $array_pref as $c ) {
        $prefs_hash[  $c[1] ]  = $c[2];
    }
    
    return $prefs_hash;
}
/*! getRequireParams

    get Require from xml params
*/
function getRequireParams ($xml) {
    if ($xml=='') return "";
    $require_hash = array();
    preg_match_all(
                    '/
                    <Require[^<]+feature="([^"]+)"[^>]*\/>
                    /xmsi',
                    $xml,
                    $array,
                    PREG_PATTERN_ORDER
                    );    
    foreach ($array[1] as $c => $require) {
        $require_hash[$require] = 1;
    }
    return $require_hash;
}
/*!  savel10nContent

    @parameters[in] hash
    
    \brief hash
        
    $errors = array();
    $lang_hash = getUrlLocales($external_xmlmodule,1,$errors);
    if ( !isset($errors[0]) ) {
        savel10nContent( array(
                            'lang_hash'             => $lang_hash,
                            'id'                    => $id,
                            'l10nSelected'          => $l10nSelected,
                            'external_source'       => $external_source,
                            'external_xmlmodule'    => $external_xmlmodule,
                            'l10n_content'          => $l10n_content,
                            'format'                => $format,
                            'prefs_hash'            => $prefs_hash
                            )
                        );
    }
*/
function savel10nContent ($datas) {
        $lang_hash  = $datas['lang_hash'];
        $id         = $datas['id'];
        $l10nSelected = $datas['l10nSelected'];
        $external_source        = $datas['external_source'];
        $external_xmlmodule     = $datas['external_xmlmodule'];
        //$l10n_content           = $datas['l10n_content'];
        $format                 = $datas['format'];
        $prefs_hash             = $datas['prefs_hash']; 
        $action                 = $datas['action'];
        $contentsByView         = $datas['contentsByView'];
        $complementaryInfos     = $datas['complementaryInfos'];
        $require_hash           = $datas['require_hash'];
        $DB                     = $datas['db'];
        $params_array_returned = array();

        savel10nInfo($lang_hash,$id,$DB);
        $errors = array();        
        //must get default hash
        $defaulturl = (isset($lang_hash['default']))?$lang_hash['default']:'';
        $content_default_langfile='';
        $default_labelsHash = '';
        if ($defaulturl!='') {
            $lang_hash['default'] = setGoogleDomain($lang_hash['default'],$require_hash);
            $content_default_langfile = getLangFile($lang_hash,'default',$errors,$require_hash);    
            $default_labelsHash = ParseCodeLocale($content_default_langfile);
        }
        $params = getParams($external_xmlmodule);
        $errors = array();
        foreach ($l10nSelected as $c) {
            if (isset($lang_hash[$c])) {

                $lang_hash[$c] = setGoogleDomain($lang_hash[$c],$require_hash);
                $content_langfile = getLangFile($lang_hash,$c,$errors,$require_hash);
                $labelsHash = ParseCodeLocale($content_langfile , $default_labelsHash );
                updateL10nTitle($c,$labelsHash,$prefs_hash,$id,$DB);

                foreach  ($contentsByView as $view => $content) {
                
                    $l10n_params = buildInternationalizedVersion($labelsHash,$params);
                    $params_array_returned[$c] = $l10n_params;
                    $l10n_content = buildInternationalizedVersion($labelsHash,$content);
                    if ($action == 'insert') {
                        saveInternationalizedContent(array(
                                           'widget_id' => $id,
                                           'lang'   => $c,
                                           'url'    => $complementaryInfos['url'],
                                           'source' => $l10n_content,
                                           'view'  => $view,
                                           'viewtype' => $complementaryInfos['type'],
                                           'params'   => $l10n_params,
                                           'db'       => $DB
                                           ));
                    } else {
                         updateInternationalizedContent(array(
                                           'widget_id' => $id,
                                           'lang'   => $c,
                                           'url'    => $complementaryInfos['url'],
                                           'source' => $l10n_content,
                                           'view'  => $view,
                                           'viewtype' => $complementaryInfos['type'],
                                           'params'   => $l10n_params,
                                           'db'       => $DB
                                           ));               
                    }
                }
            }
        }
        return $params_array_returned;
}

/*! createParamXMLFile

*/

function createParamXMLFile ($id,$params_array,$code) {
        global $__AVLANGS;
        if (isset( $params_array[$__AVLANGS[0]] )) {
            $modulecode=$params_array[$__AVLANGS[0]];
        }
        if (!isset($modulecode)) {
            $modulecode = preg_replace("/
                            <content[^>]+>
                            .*?
                            <\/content>
                                /xmsi","",$code);
                                
        }                                
        $paramfile=new file("../modules/tmp_module".$id."_param.xml");
        $paramfile->write($modulecode);
        @chmod("../modules/tmp_module".$id."_param.xml", 0766);
        
        foreach ( $__AVLANGS as $lang) {            
            if ( isset($params_array[$lang]) ) {
                $filename = "../modules/tmp_module".$id."_".$lang."_param.xml";
                $paramfile=new file($filename);
                $paramfile->write($params_array[$lang]);
                @chmod($filename, 0766);                
            }
            
        }
}

/*! rewriteUrl

    /brief
    
        rewrite url of url format widget to add variables 

*/
function rewriteUrl ($url) {
    return replaceUP($url,'url');
}

function replaceUPVars ($content) {
    return replaceUP($content,'content');
}

function replaceUP ($data,$type) {
                foreach ($_GET as $c => $v) {
                    if ( $c == 'getCacheFile'   ||
                        $c == 'format'   ||
                        $c == 'pitem'   ||
                        $c == 'pid'   ||
                        $c == 'prof'   ||
                        $c == 'p'   ||                        
                        $c == 'view'   ||
                        $c == 'getsource'   ||
                        $c == 'l10n' 
                        
                    ) {
                        continue;
                    } else {
                        $regex = '__UP_'.$c.'__';
                        $regex = '__UP_'.$c.'__';
                        $data = preg_replace("/
                                    $regex
                                    /xmsi",
                                        $v,
                                        $data
                                        );
                    }
                }
    return $data;
}

function replaceModuleJSVars ($content,$p_prof,$p_tab) {
    $content = preg_replace(
                                '/__MODULE_ID__/xmsi',
                                $p_tab,
                                $content);  
  //  $content = str_replace("\'", "'", $content);                            
    return $content;
}



?>
