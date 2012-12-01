<?php

//print " init.php in ".getcwd()."<br>";

$SOLARIUMDIR="../solarium/library/Solarium";

error_reporting(E_ALL);
ini_set('display_errors', true);

require('Autoloader.php');

if (file_exists("$SOLARIUMDIR/config.php")) {
    require("$SOLARIUMDIR/config.php");
} 


function htmlHeader(){
    echo '<html><head><title>Solarium examples</title></head><body>';
}

function htmlFooter(){
    echo '</body></html>';
}