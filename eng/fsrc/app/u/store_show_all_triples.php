<html>
<head>
	<title>
		LOCAL STORE SPARQL Explorer
	</title>
</head>
<body>

<?php

//siehe http://arc.semsol.org/docs/v2/getting_started

include("../sroot.php");
include("FRIutilities.php");
include_once("../../../fsrc/gen/u/arc/ARC2.php");

$limit = $_GET['limit']; if (!$limit) $limit=0;
$token = $_GET['token']; 
$QUERY = $_GET['QUERY']; 
$verb = $_GET['verb']; 
$storename = $_GET['storename']; 
if (!$storename)
 {
  print "You have to provide a name for your store ... ;-) with limit= and or a token to be matched";
  exit; 
}

$LOCALARCCONFIG = $ARCCONFIG;
$LOCALARCCONFIG{'store_name'} = $storename;

$store = ARC2::getStore($LOCALARCCONFIG);
if (!$store->isSetUp()) {
  $store->setUp();
}


$thislink	=$_SERVER['PHP_SELF'];

print "<h2> RODIN ARC LOCAL STORE<br>SPARQL EXPLORER 
<a href='mailto:fabio.ricci@ggaweb.ch?subject=RODIN ARC LOCAL STORE SPARQL EXPLORER' style='color:ccc;text-decoration:snone' title=''Send an issue to the developer> send issue </a></h2>";
if ($token)
print "<h3> Matching $token</h3>";
if ($verb)
print "<h3> Predicate $verb</h3>";


print <<<EOP
<table>
<form name='a' >
	<tr><td>Your SPARQL query for storename <b>$storename</b>:</tr>
	<tr><td><textarea rows='10' cols='60' name='QUERY'>$QUERY</textarea></tr>
	<tr><td><input type=submit style='width:100%' value='Submit'></tr>
	<input type=hidden name=storename value='$storename'>
</form>
</table>
EOP;


exec_print_sparql_xyz($limit,$token,$verb,$QUERY);










/*
 * Computes the query to SKOS $verb
 * $verb= related, broader, narrower
 */
function exec_print_sparql_xyz($limit,$token='',$verb='',$QUERY='')
{
	global $store;
	global $storename;
	
	$triplesinstore= count_ARC_triples($store);
	$triplesinstore=number_format($triplesinstore, 0, '.', "'");
	if ($token<>'')
		$searchexpr="FILTER ( regex(?x,\"$token\") || regex(?y,\"$token\") || regex(?z,\"$token\") ) .";
										
  if ($limit) $evtllimit="LIMIT $limit";
	
	if ($verb<>'') 
	{
		$YY1 ="";
		if (strstr($verb,'skos:'))
			$YY2 ="$verb";
		else
			$YY2 ="<http://$verb>";
	}
	else 
	{
		$YY1 ="?y";
		$YY2 ="?y";
	}
		
	if ($QUERY=='')
	$QUERY=<<<EOQ
	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	
	select ?x $YY1 ?z
	where
		 {
				?x $YY2 ?z .
				$searchexpr
		 } $evtllimit
EOQ;

	//print htmlentities($QUERY)."<br>";

	$result=array();
	$selection_vars=get_sparql_selection_vars($QUERY);
	//$selection_vars=array('x','z','p','g');
	
	
	if ($rows = $store->query($QUERY, 'rows')) 
	{
    $NTRIPLES = count($rows);
    print "$NTRIPLES (of $triplesinstore) TRIPLES extracted out of STORE <b>'$storename'</b>";

		print '<table>';
    $i=-1;
		
		$r="<tr><td align='right'></td>";
		foreach($selection_vars as $var)
		{
			$r.="<th align=left>?$var:</th>";
		}
		$r."</tr>";
		print $r;
			
		foreach($rows as $row) {
      
      $i++;
      if ($limit>0 && $i>$limit) break; // debug
	
	 		$r="<tr><td align='right'>$i</td>";
 			foreach($selection_vars as $var)
			{
				$r.="<td>".$row[$var]."</td>";
			}
  		$r."</tr>";
			print $r;
		}
		
		print '</table>';

	}
	return $result;
}






/*
 * Extracts and arrays the vars in the select clause in QUERY
 */
function get_sparql_selection_vars($QUERY)
{
	
	$pattern="/select\b(.*)/";
	
	if (preg_match($pattern,$QUERY,$match))
	{
		$vars=str_replace('?','',trim($match[1]));
		
		$vars_arr = explode(' ',$vars);
		
		//print "<br>RETURNING vars ($vars) arr: "; var_dump($vars_arr);
		return $vars_arr;	
	}
	return array();
}

?>
</body>
</html>