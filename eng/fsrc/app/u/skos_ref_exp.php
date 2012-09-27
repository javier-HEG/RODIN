<html>
<head>
</head>
<body>

<?php

//siehe http://arc.semsol.org/docs/v2/getting_started

include("sroot.php");
include_once("$PATH2U/arc/ARC2.php");




$store = ARC2::getStore($ARCCONFIG);
if (!$store->isSetUp()) {
  $store->setUp();
}


$thislink	=$_SERVER['PHP_SELF'];
$term			=$_REQUEST['term'];

if (!$term) $term="Wachstumsbranche";

//$related				=exec_sparql('related' ,$term);
$related = exec_sparql_xyz();



print "<h2> Fabio's SKOS-Navigator (EXPERIMENTAL)</h2>";
print "<h6>SPARQL-Auswertung Als Basis zum Refininement </h6><br>";
print "Geladen:";
print "<br><a href='$SKOS_DBPEDIA_FILE' title='File einsehen im anderen Tab' target='_blank'>DBPEDIA</a>";
print "<br><a href='$SKOS_ZBW_FILE' title='File einsehen im anderen Tab' target='_blank'>SCHWEIZERISCHE NATIONALBANK</a><br><br>";


print <<<EOP
<form >
	<b><i>Dein Term: </i></b><input type="text" name='term' value='$term' title='Gib ein oder mehrer Wörter und bestaetige mit ENTER'>
</form>
EOP;




//FRI
//List all:
$i=0;
foreach ($related as $word)
{	$i++;
	print $word;
	if ($i > 1000) break;
}




function exec_sparql_xyz()
##################################
# 
# Computes the query to SKOS $verb
# $verb= related, broader, narrower
{
	global $store;
	
	$QUERY=<<<EOQ
	
	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	
	select ?x ?y ?z
	where
		 {
				?x ?y ?z.
		 } 
EOQ;

	$result=array();
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		$result[]='<table>';
		foreach($rows as $row) {
			$r=<<<EOR
		 <tr><td>{$row['x']}</td><td>{$row['y']}</td><td>{$row['z']}</td></tr>
EOR;
			$result[]=$r;
		}
		
		$result[]='</table>';

	}
	return $result;
}






?>
</body>
</html>