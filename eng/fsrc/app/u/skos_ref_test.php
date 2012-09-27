<html>
<head>
<SCRIPT TYPE="text/javascript">
<!--
function submitenter(myfield,e)
{
var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return true;

if (keycode == 13)
   {
   myfield.form.submit();
   return false;
   }
else
   return true;
}
//-->
</SCRIPT>

<title>Fabio's SKOS-Navigator</title> 
</head>
<body>

<?php

//siehe http://arc.semsol.org/docs/v2/getting_started

include("sroot.php");
//print "qua: ".getcwd();
include_once("../../u/arc/ARC2.php");


$LOCALCONFIG=$ARCCONFIG;
$LOCALCONFIG{'store_name'}='zbw';


$store = ARC2::getStore($LOCALCONFIG);
if (!$store->isSetUp()) {
  $store->setUp();
}



$thislink	=$_SERVER['PHP_SELF'];
$term			=$_REQUEST['term'];
$L				=$_REQUEST['L'];
if (!$L) $L='de';
$LANGUAGES=explode(',',$L);



if (!$term) $term="Wachstumsbranche";

$related				=exec_sparql('related' ,$term);
$relatedAlt			=exec_sparql('related' ,$term,'altLabel');
$relatedHidden	=exec_sparql('related' ,$term,'hiddenLabel');
$broader				=exec_sparql('broader' ,$term);
$broaderAlt			=exec_sparql('broader' ,$term,'altLabel');
$broaderHidden	=exec_sparql('broader' ,$term,'hiddenLabel');
$narrower				=exec_sparql('narrower',$term);
$narrowerAlt		=exec_sparql('narrower',$term,'altLabel');
$narrowerHidden	=exec_sparql('narrower',$term,'hiddenLabel');



print "<h2> Fabio's STW SKOS-Navigator</h2>";
print "Geladen:";
//print "<br><a href='$SKOS_DBPEDIA_FILE' title='File einsehen im anderen Tab' target='_blank'>DBPEDIA STW</a>";
print "<br><a href='$SKOS_ZBW_FILE' title='File einsehen im anderen Tab' target='_blank'>SCHWEIZERISCHE NATIONALBANK STW (Standard Thesaurus Wirtschaft)</a><br><br>";

$SUBMITONENTER=" onKeyPress=\"return submitenter(this,event)\"";

print <<<EOP
<form name='f'>
	<b><i>Dein Term: </i></b><input type="text" name='term' value='$term' title='Gib ein oder mehrer Wörter und bestaetige mit ENTER' $SUBMITONENTER>
	&nbsp;<b><i>die Sprache(n): </i></b><input type="text" size="4" name='L' value='$L' title='Gib eine - en - oder mehrere Sprachbezeichnungen - en,de - ein' $SUBMITONENTER>  	
	
	
</form>
EOP;




//FRI
//List all:
print "<br><b><i>Related:</i></b><hr>";
foreach ($related as $word)
{
	print $word."<br>";
}
if ($relatedAlt) 
{
	print "<br> <i>(alternatives:)</i><br>";
  foreach ($relatedAlt as $word) print $word."<br>";
}

if ($relatedHidden) 
{
	print "<br> <i>(hidden:)</i><br>";
  foreach ($relatedHidden as $word) print $word."<br>";
}






print "<br><b><i>Broader:</i></b><hr>";
foreach ($broader as $word)
{
	print $word."<br>";
}


if ($broaderAlt) 
{
	print "<br> <i>(alternatives:)</i><br>";
  foreach ($broaderAlt as $word) print $word."<br>";
}

if ($broaderHidden) 
{
	print "<br> <i>(hidden:)</i><br>";
  foreach ($broaderHidden as $word) print $word."<br>";
}







print "<br><b><i>Narrower:</i></b><hr>";
foreach ($narrower as $word)
{
	print $word."<br>";
}

if ($narrowerAlt) 
{
	print "<br> <i>(alternatives:)</i><br>";
  foreach ($narrowerAlt as $word) print $word."<br>";
}

if ($narrowerHidden) 
{
	print "<br> <i>(hidden:)</i><br>";
  foreach ($narrowerHidden as $word) print $word."<br>";
}




function exec_sparql($verb,$term,$labeltype='prefLabel')
##################################
# 
# Computes the query to SKOS $verb
# $verb= related, broader, narrowe
#
# example: exec_sparql('related' ,$term);
#
{
	global $store;
	global $LANGUAGES;
	
	$QUERY=<<<EOQ
	
	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	
	select ?x 
	where
		 {
			?d1 	skos:$labeltype  ?x.
			?d1 	skos:$verb 			?d2 .
			?d2 	skos:prefLabel 	'$term' .
		 } 
EOQ;
	//print "<br><br>exec_sparql: $QUERY <br>";

	$result=array();
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		foreach($rows as $row) 	
		{
			if (in_array($row['x lang'],$LANGUAGES)) 
				$result[]= make_browse_link($row['x']);
		}
	}
	return $result;
}








function exec_sparql_explore($storename)
##################################
# 
# Computes the query to SKOS $verb
# $verb= related, broader, narrower
{
	global $ARCCONFIG;
	$LOCALARCCONFIG = $ARCCONFIG;
	$LOCALARCCONFIG{'store_name'} = $storename;
	$store = ARC2::getStore($LOCALCONFIG);
	if (!$store->isSetUp()) {
	  $store->setUp();
	}
	
	$QUERY=<<<EOQ
	
	construct {?s  ?p  ?o.}  
	where
		 {
			?s 	?p  ?o.
		 } 
EOQ;

	$result=array();
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		print "<table>";
		foreach($rows as $row) 	
		{
			print "<tr>";
			print "<td>";
			print $row['s'];
			print "</td>";
			print "<td>";
			print $row['p'];
			print "</td>";
			print "<td>";
			print $row['o'];
			print "</td>";
			print "</tr>";
		}
		print "</table>";
	}
	return $result;
}



function make_browse_link($term)
################################
{
	global $thislink, $L;

	$LINK=<<<EOL
	<a href="#" onClick="window.open('$thislink?term=$term&L='+f.L.value,'_self')" title="Klick um im aktuellen SKOS repository nach '$term' zu browsen... ">$term</a>
EOL;
	return $LINK;
}





?>
</body>
</html>