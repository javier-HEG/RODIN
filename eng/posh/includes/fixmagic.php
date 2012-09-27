<?php

if ( get_magic_quotes_gpc() && get_magic_quotes_gpc() == 1) {
    
    error_log("magic quotes are set to on, must be off if you can");
    function stripslashes_r($str)
    {
        if (is_array($str)) {
            foreach ($str as $k => $v) {
                $str[$k] = stripslashes_r($v);
            }
            return $str;

        } else {
            return stripslashes($str);
        }
    } 
    function dispelMagicQuotes()
    {
        if (ini_get('magic_quotes_gpc')) {
            foreach (array('_GET', '_POST', '_COOKIE') as $super) {
                foreach ($GLOBALS[$super] as $k => $v) {
                    $GLOBALS[$super][$k] = stripslashes_r($v);
                }
            }
        }
    } 
    dispelMagicQuotes();
}

?>
