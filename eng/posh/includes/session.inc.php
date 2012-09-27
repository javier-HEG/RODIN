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
#  SESSION management 
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

session_name(__SESSION);
session_start();

/*! init_session

    @param[in]  $id, $user, $type, $lang, $longname

*/
function init_session($id,$user,$type,$lang,$longname,$activity)
{
	if (isset($id))
    {
        $_SESSION['user_id'] = $id;
    }
	if (isset($user))
    {
        $_SESSION['username'] = $user;
    }
	if (isset($type))
    {
        $_SESSION['type'] = $type;
    }
	if (isset($lang) && $lang)
    {
        $_SESSION['lang'] = $lang;
    }
	if (isset($longname))
    {
        $_SESSION['longname'] = $longname;
    }
    $_SESSION['availability'] = (isset($activity) ? $activity : 'o');
}
/*! close_session

    
    
*/
function close_session()
{
	session_unset();
	session_destroy();
}


/*! parseHTTPLanguage

    If variable disapppear, we add en language

*/
function parseHTTPLanguage () {
    global $languages;
  /* default to "everything is acceptable", as RFC2616 specifies */
  //fr-fr,fr;q=0.8,en-us;q=0.5,en;q=0.3

  if ( isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])  )
  {  
    $acceptLang=(($_SERVER["HTTP_ACCEPT_LANGUAGE"] == '') ? 'en,fr' :
    $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
    $alparts = @preg_split("/,/",$acceptLang);
  
    /* Parse the contents of the Accept-Language header.*/
    foreach($alparts as $part)
    {
        $part = trim($part);
        if(preg_match("/;/", $part))
        {
            $lang = @preg_split("/;/",$part);
            $score = @preg_split("/=/",$lang[1]);
            $languages[strToLower($lang[0])]=$score[1];
        }
        else
        {
            $languages[strToLower($part)]=1;
        }
    }
 }
 else
 {
   $languages['en']=1;
 }
 arsort($languages);
}
/*!  getDefaultLanguage

    using parseHTTPLanguage before 

*/
function getDefaultLanguage ($__AVLANGS)
{
    global $languages;
    //default language in __AVLANGS
    $chosen_language = $__AVLANGS[0];
	
    foreach ( $languages as $lang => $score)
    {
        $lc = @preg_split("/-/", $lang);
        $deflang = $lc ?  $lc[0] : $lang;
        if ( $score == 1 && in_array($deflang, $__AVLANGS) )
        {
            $chosen_language = $deflang;
            break;
        }
        else if (  in_array($deflang, $__AVLANGS)  )
        {
            $chosen_language = $deflang;
            break;
       }
    }
    define("__LANG", $chosen_language);
}

//language selection
if (isset($_GET["lang"]))
{
    if (    preg_match( '/^[a-z]{2}$/', $_GET["lang"] ) 
         || preg_match( '/^[a-z]{2}\-[a-z]{2}$/', $_GET["lang"] ) 
          ) {
        $_SESSION['lang'] = $_GET["lang"];
    } else {
        $_SESSION['lang'] = $__AVLANGS[0];
    }
}
if ($_SESSION["lang"])
{ 
	define("__LANG",$_SESSION['lang']);
}
else
{
    //parse http_accept_language, then get default language (score == 1)
   parseHTTPLanguage();
   getDefaultLanguage($__AVLANGS);
}


// Check that connection level is OK depending on the one requested in page
if( isset($not_access) && $not_access == 1)
{

	if ((!is_array($_SESSION) || !array_key_exists("user_id",$_SESSION)) || ($_SESSION['type']<>$granted && $granted!="" && $_SESSION['type']!="A"))
	{
		close_session();
		if (isset($pagename))
		{
            if (isset($disconnected_mode_allowed) && $disconnected_mode_allowed == 'yes')
                exit(); 
			header('location:'.__LOCALFOLDER.'portal/login.php?message=You have been disconnected');
		}
		else
		{
			header('location:'.__LOCALFOLDER.'/index.php');
		}
        exit();
	}
}

?>
