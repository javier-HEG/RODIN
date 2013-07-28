<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 
require_once("../u/FRIdbUtilities.php");
$sid="20130723.122519.883.eng.6";
$collection='subject_ranking';
$deleteexpr="sid:$sid";

$ui= fri_get_user_info(4);

print "<br>USER INFO: <br>";
foreach($ui as $k=>$v) print "<br>$k=>$v";


?>
