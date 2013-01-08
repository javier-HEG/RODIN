<html>
<head>
</head>
<body>

<?php

//siehe http://arc.semsol.org/docs/v2/getting_started

include("../sroot.php");
include_once("../../../fsrc/gen/u/arc/ARC2.php");



$limit = $_GET['limit']; if (!$limit) $limit=0;
$storename = $_GET['storename']; 
if (!$storename)
 {
  print "You have to provide a name for your store ... ;-)";
  exit; 
}


$store = ARC2::getStore($ARCCONFIG);
if (!$store->isSetUp()) {
  $store->setUp();
}


$thislink	=$_SERVER['PHP_SELF'];
$term			=$_REQUEST['term'];


//$related				=exec_sparql('related' ,$term);
$related = exec_sparql_xyz();



print "<h2> Store SKOS-Navigator (using store $storename)</h2>";

print <<<EOP
<form >
	<b><i>Dein Term: </i></b><input type="text" name='term' value='$term' title='Gib ein oder mehrer W�rter und bestaetige mit ENTER'>
  <input type='hidden' name='storename' value='$storename'>
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





function exec_show_sparql_all_xyz()
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




function exec_show_sparql_all_xyz($term)
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