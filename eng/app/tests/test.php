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

 
$text=$_GET['text']; 
$LANG = detect_language($text);
print "<hr>$text<br>ergibt language: $LANG";


?>
