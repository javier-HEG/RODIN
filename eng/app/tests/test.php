<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("../u/RodinResult/RodinResultManager.php");
require_once("../u/SOLRinterface/solr_init.php");
//    global $SOLR_RODIN_CONFIG;
//    global $SOLARIUMDIR;
//    global $USER;
 
$expr="";
$expr2=str_replace("/","\/",$expr);
    $pattern="/$expr2(.*)/";
    
    //print "<br>Pattern $pattern ";
 $term="Statistik und Ãkonometrie";   
 
    if (preg_match($pattern,$term,$match))
    {
      $matched=1;
      $nsterm=$ns;
      $nakedterm=$match[1]; //cut first "/"
      if ($nakedterm[0]=='/')
          $nakedterm=substr($nakedterm,1);
      
      $returnterm=$ns.$sep.$nakedterm;
      //print " YES ";
      break;
    }
  }

?>
