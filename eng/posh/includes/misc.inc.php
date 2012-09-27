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
function suppress_accent($str) 
{ 
    if (function_exists('mb_strtolower'))
	{
		$str= mb_strtolower($str, 'UTF-8');
	}
	else
	{
		$str= strtolower($str);
	}
    $str = str_replace( 
        array( 
            'à', 'â', 'ä', 'á', 'ã', 'å', 
            'î', 'ï', 'ì', 'í', 
            'ô', 'ö', 'ò', 'ó', 'õ', 'ø', 
            'ù', 'û', 'ü', 'ú', 
            'é', 'è', 'ê', 'ë', 
            'ç', 'ÿ', 'ñ', 
        ), 
        array( 
            'a', 'a', 'a', 'a', 'a', 'a', 
            'i', 'i', 'i', 'i', 
            'o', 'o', 'o', 'o', 'o', 'o', 
            'u', 'u', 'u', 'u', 
            'e', 'e', 'e', 'e', 
            'c', 'y', 'n', 
        ), 
        $str 
    ); 
    
    return $str;        
}

/*
 * verifies if the email is in a good format
 * Input:
 *	$email (string) : string to test
 * Returns :
 *	True if the input is in the good format
 */
function is_email($email)
{
	$search = "/^[a-z0-9\.\-_]+@[a-z0-9\.\-_]+\.[a-z]{2,3}$/i";
	
	return (preg_match($search,$email) !=0);
}

/**
     * Returns true if $string is valid UTF-8 and false otherwise.
     *
     * @since        1.14
     * @param [mixed] $string     string to be tested
     * @subpackage
     */
    function is_utf8($string) {
      
        // From http://w3.org/International/questions/qa-forms-utf-8.html
        return preg_match('%^(?:
              [\x09\x0A\x0D\x20-\x7E]            
            | [\xC2-\xDF][\x80-\xBF]             
            |  \xE0[\xA0-\xBF][\x80-\xBF]        
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  
            |  \xED[\x80-\x9F][\x80-\xBF]        
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     
            | [\xF1-\xF3][\x80-\xBF]{3}         
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}
        )*$%xs', $string);
    } 

    function isUTF8($str) {
        if ($str === mb_convert_encoding(mb_convert_encoding($str, "UTF-32", "UTF-8"), "UTF-8", "UTF-32")) {
            return true;
        } else {
            return false;
        }
    }
    
?>