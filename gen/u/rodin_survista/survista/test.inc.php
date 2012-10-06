<?php

/*
 * Minimal test framework
 *
 * Copyright 2011 HTW Chur.
 */

function a($msg, $val) {
    if ($val === true) {
        echo "OK... " . $msg . PHP_EOL;
    } else {
        echo "FAILED " . $msg;
        exit();
    }
}

?>