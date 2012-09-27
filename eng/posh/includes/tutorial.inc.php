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

function secureWidgetCode($code,$format)
{
    global $unsecured_widget;
    checkWidgetContents($code,$format);
    $coderet;

	//if (strpos($code,"<"."?")!==false) {$code=lg("phpnotAllowed");$unsecured_widget='phpnotAllowed';} 
	if (strpos($code,"<"."%")!==false) {$coderet=lg("ServerScriptNotAllowed");$unsecured_widget='ServerScriptNotAllowed';} 
	
    //search in script tag if no forbidden actions are launched
	//remove ends of line
    $code_tmp = preg_replace('/(\r\n|\r|\n)/','',$code);
    //prepare the split, eol after </script> 
    $code_tmp = preg_replace('/<[\s\t]*\/[\s\t]*script[^\>]*>/i',"</script>\n",$code_tmp);
    // and before <script.*>
    $code_tmp = preg_replace('/<([^\<]*)script([^\>]*)>/i',"\n<$1script$2>",$code_tmp);
    
    //split in parts where script items  are separated from other items
    $parts = preg_split("/\n/",$code_tmp);
    
   	for($i=0;$i<count($parts);$i++)
	{
		$scriptContent=strtolower($parts[$i]);
        
        if ( !preg_match('/script/',$scriptContent)) {
            if (preg_match( '/<.*[=:]*[\s\t]*(window\.|document\.)*parent[^\>]*>/',  $scriptContent )) {$coderet=lg("parentNotAllowed"); $unsecured_widget='parentNotAllowed';} 
            if (preg_match( '/<.*[=:]*[\s\t]*(window\.|document\.)*top[^\>]*>/',  $scriptContent )) {$coderet=lg("topNotallowed") ; $unsecured_widget='topNotallowed';}
            if (preg_match( '/<.*[=:]*[\s\t]*eval\([^\>]*>/',  $scriptContent )) {$coderet=lg("EvalNotAllowed"); $unsecured_widget='EvalNotAllowed';}
        } else {
            
    		if (strpos($scriptContent,"parent.")!==false) {$coderet=lg("parentNotAllowed") ;$unsecured_widget='parentNotAllowed';}
    		if (strpos($scriptContent,"window.parent")!==false) {$coderet=lg("parentNotAllowed") ; $unsecured_widget='parentNotAllowed';} 
    		if (strpos($scriptContent,"document.parent")!==false) { $coderet=lg("parentNotAllowed") ; $unsecured_widget='parentNotAllowed';} 
    		if (strpos($scriptContent,"=parent")!==false) {$coderet=lg("parentNotAllowed") ; $unsecured_widget='parentNotAllowed';} 
    		if (strpos($scriptContent,":parent")!==false) {$coderet=lg("parentNotAllowed") ; $unsecured_widget='parentNotAllowed';} 
            
            if (preg_match( '/[=:]*[\s\t]*parent/',  $scriptContent )) {$coderet=lg("parentNotAllowed") ; $unsecured_widget='parentNotAllowed';} 
            
    		if (strpos($scriptContent,"top.")!==false) {$coderet=lg("topNotallowed") ; $unsecured_widget='topNotallowed';}
    		if (strpos($scriptContent,"window.top")!==false) {$coderet=lg("topNotallowed") ; $unsecured_widget='topNotallowed';}
    		if (strpos($scriptContent,"document.top")!==false) {$coderet=lg("topNotallowed") ; $unsecured_widget='topNotallowed';}
    		if (strpos($scriptContent,"=top")!==false) {$coderet=lg("topNotallowed") ; $unsecured_widget='topNotallowed';}
    		if (strpos($scriptContent,":top")!==false) {$coderet=lg("topNotallowed") ; $unsecured_widget='topNotallowed';}
            
            if (preg_match( '/[=:]*[\s\t]*top/',  $scriptContent )) {$coderet=lg("topNotallowed") ; $unsecured_widget='topNotallowed';}
            
    		if (strpos($scriptContent,"eval(")!==false) {$coderet=lg("EvalNotAllowed") ; $unsecured_widget='EvalNotAllowed';}
    		if (strpos($scriptContent,"=eval")!==false) {$coderet=lg("EvalNotAllowed") ; $unsecured_widget='EvalNotAllowed';}   
            if (preg_match( '/[=:]*[\s\t]*eval/',  $scriptContent )) {$coderet=lg("EvalNotAllowed") ; $unsecured_widget='EvalNotAllowed';}
            
    		if (strpos($scriptContent," src=")!==false) {$coderet=lg("JSincludeNotAllowed") ; $unsecured_widget='JSincludeNotAllowed';}     

            
            
        }
	}

	return $coderet;
}
 /**
    \name
    checkWidgetContents
    \brief
    Check contents syxtaxe
     
    @param[in] $code xml code (widget code)
**/
function checkWidgetContents($code,$format)
{
    global $unsecured_widget;
    if ($format=="R") return $code;
    
    $code = preg_replace('/
                                      \\\"
                                /xmsi','"',$code);
    
    $contents=getContents($code);
    if (!isset($contents[0])) {
        $unsecured_widget="noContentSpecified";
    }
    foreach ($contents as $indice => $parameters)    
    {
        preg_match('/
                          type="([^"]+)"
                    /xmsi',$parameters,$c_type);   
        if (!isset($c_type[1])) 
        { 
            $unsecured_widget="noTypeParameter"; 
        }
        else if ($c_type[1]!='url' && $c_type[1]!='html') 
        {
            $unsecured_widget="noTypeParameter";
        }
        else 
        {
            $external_type=$c_type[1];  
            if ($external_type=="url") {
               preg_match('/
                                href=\\*?\"([^"]+)\\*?\"
                           /xmsi',$parameters,$c_href);  
               if (!isset($c_href[1])) { $unsecured_widget="noHrefParameter"; }      
            }
        }        
    } 
    return $code;
}
    
    
function formatWidgetCode($code)
{
	$code = stripslashes($code);
	$code = eregi_replace("<script","<script",$code);
	$code = eregi_replace("</script","</script",$code);

	return $code;
}
?>