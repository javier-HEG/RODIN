
<?php

function makeXMLvector($xml,&$vals)
###########################################
#
# Ueberführt ein XML-Ausdruck in einen Vektor
# $vals muss ein bereits instantiierter array sein.
#

{

$sxml = simplexml_load_string($xml);
$namespaces=$sxml->getNamespaces(true);
RecurseXML($sxml,$vals);

return $vals;
}





function RecurseXML($xml,&$vals,$parent="")
###########################################
#
# Ueberführt ein XML-Ausdruck in einen Vektor
#
#
#
{
   $child_count = 0;
   foreach($xml as $key=>$value)
   {
      $child_count++;     
      $k = ($parent == "") ? (string)$key : $parent . "." . (string)$key; 
      if(RecurseXML($value,$vals,$k) == 0)  // no childern, aka "leaf node"
         $vals[$k] = (string)$value;   
   }
   return $child_count;
}



?>

