<?php
###########################################
#
# SERVICE get w response
# returns an xml object with informations on the widget call
# using sid
# 1. user informations 2. Widget informations
/*
 Input:
 ?sid=((sid)) 
& a=w_response	//a(ction) : give me response of widget w according to sid
& w=((widget id)) 


X1R.get w_response (Answer to X1.get w_results )
	<rodin_get>
	  <sid>((sid))</sid>
	  <a> w_response </a>
	  <wid>((wid))</wid>
	  <response>
	    <document>
	      <record>
	        <name>((the record=attribute name))</name>
	        <type>((type definition for this record))</type>
	        <value>
<![CDATA[      ((value for this record))     ]]> 
        </value>
	      </record>
	      <record>
	      	…
      </record>
…
	    </document>
	    …
   </response>
	</rodin_get> 
*/

	require_once("../u/FRIdbUtilities.php");

	$sid= $_REQUEST['sid']; 
	$a= $_REQUEST['a']; 
	$wid= $_REQUEST['wid']; 
	$show= $_REQUEST['show']; 
	if (  $show<>'all' 
			&&$show<>'token'
			&&$show<>'min' ) $show='token';

$badinput=((!is_a_value($sid))
					&&(!is_a_value($a))
					&&(!is_a_value($w))
					);

if ($badinput)
$OUT="<rodin_get>
<error>Bad Input: Please supply valid values for params sid,a,wid {,show}</error>
<rodin_get>";

else
{

	if ($a=='w_response')
		$RESPONSE=get_xml_widget_response($sid,$wid,$show);
	
	
$OUT.=<<<EOT
<rodin_get>
  <sid>{$sid}</sid>
  <a>{$a}</a>
  <wid>{$wid}</wid>
  <show>{$show}</show>
	$RESPONSE  
 </rodin_get>
EOT;
}


$now=date("D M j G:i:s T Y");


header ("content-type: text/xml");
print <<<EOX
<?xml version="1.0" encoding="UTF-8"?>
<!-- RODIN XML service getwinfo (HEG)-->
<!-- fabio.fr.ricci@hesge.ch -->
<!-- $now -->
<!-- Response to get w_response with params: -->
<!-- sid=$sid -->
<!-- a=$a -->
<!-- wid=$wid -->
<!-- show=$show -->
$OUT
EOX;





function clean_w_url($url)
{

	$cleaned=$url;
	if (preg_match("/(.*)\?&/",$url,$match))
	{
		//foreach ($match as $m) print "<br />1 match ".$m;
		$cleaned=$match[1];
	}
	else if (preg_match("/(.*)\?/",$url,$match))
	{
		//foreach ($match as $m) print "<br />2 match ".$m;
		$cleaned=$match[1];
	}
	
	//print "<br />returning $cleaned";
	return $cleaned;
}



?>