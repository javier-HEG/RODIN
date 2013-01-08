<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("../u/RodinResult/RodinResultManager.php");
require_once("../u/SOLRinterface/solr_interface.php");
//    global $SOLR_RODIN_CONFIG;
//    global $SOLARIUMDIR;
//    global $USER;
 


$expr="http://lod.gesis.org/thesoz/term/10034304-en";


list($left,$right) = splitn($expr,'/',3);
print "<hr>splitn($expr,'/',3) = ($left, $right)<br>";

list($left,$right) = splitn($expr,'/',4);
print "<hr>splitn($expr,'/',4) = ($left, $right)<br>";


list($left,$right) = splitrn($expr,'/',1);
print "<hr>splitrn($expr,'/',1) = ($left, $right)<br>";

list($left,$right) = splitrn($expr,'/',2);
print "<hr>splitrn($expr,'/',2) = ($left, $right)<br>";

list($left,$right) = splitrn($expr,'/',3);
print "<hr>splitrn($expr,'/',3) = ($left, $right)<br>";





?>
