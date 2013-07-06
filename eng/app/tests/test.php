<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 
require_once("../u/LanguageDetection.php");


$text='Graphene';
print $text.' -> '.detectLanguage($text);

		
?>
