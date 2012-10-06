<?php

/*
 * Removes prefix from STW taxonomy
 *
 * Copyright 2011 HTW Chur.
 */

class LabelCleaner {

    static function cleanup($in) {
        // http://zbw.eu/stw/thsys/ separator: two spaces
        // i.e. 'V.05.04  Zins'

        $pos = strpos($in, "  ");
        if ($pos !== false) {
            return substr($in, $pos + 2, strlen($in) - $pos - 2);
        } else {
            return $in;
        }
    }

}

?>