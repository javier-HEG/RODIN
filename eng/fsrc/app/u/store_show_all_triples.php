<html>
<head>
	<title>
		LOCAL STORE SPARQL Explorer
	</title>
	<link rel="stylesheet" type="text/css" href="../../../app/css/rodin.css.php?" />
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

$path_arcUtilities = $LOCALARCCONFIG{'arcUtilities'}	;
include_once($path_arcUtilities);

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
	<tr><td>Your SPARQL query for storename <b>$storename</b> &nbsp;&nbsp;
	<span style="color:gray"> (Special function: PREFIX EXPRS + SUBTREE(ns:entity_id,n) displays a subgraphtree of depth n)</span>:</tr>
	<tr><td><textarea rows='20' cols='100' name='QUERY'>$QUERY</textarea></tr>
	<tr><td><input type=submit style='width:100%' value='Submit'></tr>
	<input type=hidden name=storename value='$storename'>
</form>
</table>
EOP;



include_once "../../../app/u/RodinResult/RodinRDFResult.php";


$RDFobj = new RodinRDFResult($nix,null,null,-1);
$RDFC=get_class($RDFobj);
$NAMESPACES=$RDFC::$NAMESPACES;
//Fill namespaces into config
$LOCALARCCONFIG{'ns'}=$NAMESPACES;


//Show triples
$triples = exec_print_sparql_xyz($limit,$token,$verb,$QUERY);


#Get namespaces from RDF class


if (0)
{
print "<br>Namespaces used: ";
foreach($LOCALARCCONFIG{'ns'} as $ns=>$url)
{
	print "<br> $ns => $url ";
} 
print "<br>";
}

#GRAPHVIZ Instantiation:

$viz = ARC2::getComponent('TriplesVisualizerPlugin', $LOCALARCCONFIG);

/* display an svg image 
$svg = $viz->draw($triples, 'svg', 'base64');
echo '<embed type="image/svg+xml" src="data:image/svg+xml;base64,' . $svg . '"/>';
*/

/* display a png image */
$png = $viz->draw($triples, 'png', 'base64');
print '<img src="data:image/png;base64,' . $png . '"/>';

/* generate a dot file */
//$dot_src = $viz->dot($triples);


/*
 * Computes the query to SKOS $verb
 * $verb= related, broader, narrower
 */
function exec_print_sparql_xyz($limit,$token='',$verb='',$QUERY='')
{
	global $store;
	global $storename;
	global $NAMESPACES;
	
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

/* TOOL FOR DISPLAYING (LIMITED) SUBGRAPHS
 * ACCEPTING A COMMANDO SUBTREE like:
PREFIX rodin: <http://localhost/rodin/eng/app/w3s/resource/> 
PREFIX rodin_a: <http://localhost/rodin/eng/app/w3s/resource/a/> 
PREFIX rodin_e: <http://localhost/rodin/eng/app/w3s/resource/e/> 
PREFIX dce: <http://purl.org/dc/elements/1.1/>

SUBTREE(rodin_a:src_81_lodfetch_1366815014_024512,3) 
 */
	else if (preg_match("/SUBTREE\((.+),(\d)\)/",$QUERY,$match))
	{
		if(0) {
			print "<br>match ".$match[0];
			print "<br>match ".$match[1];
			print "<br>match ".$match[2];
		}
		
		$x = substr($QUERY,strpos($match[0], $QUERY)); 
		$PREFIX=trim(str_replace($match[0],'', $x));
		$ENTITY_ID=$match[1];
		$DEPTH=$match[2];
		
		print "<br>SHOWING <b>SUBTREE(depth=$DEPTH)</b> from ENTITY $ENTITY_ID "
					."<br><br>Using:<br>".str_replace("\n","<br>",htmlentities($PREFIX))."<br><br>";
					
		if($DEPTH==1)			
$QUERY=<<<EOQ
$PREFIX
select distinct ?s ?p ?o
WHERE
 #Level 1 = get triples from tree root node:
 {
  FILTER(?s=$ENTITY_ID ) .
  ?s ?p ?o.
 }
EOQ;
		else if($DEPTH==2)
$QUERY=<<<EOQ
$PREFIX
select distinct ?s ?p ?o
WHERE
{
#Level 1 = get triples from tree root node:
 {
  FILTER(?s=$ENTITY_ID ) .
  ?s ?p ?o.
 }
 UNION 
 #Level 1 = get triples to tree root node:
 {
  FILTER(?o=$ENTITY_ID) .
  ?s ?p ?o.
 }
 
 UNION 
 #Level 2 = get triples from triples from tree root node
 {
  FILTER(?s_m1=$ENTITY_ID) .
  ?s_m1 ?p_m1 ?o_m1. FILTER(?o_m1=?s) .
  ?s ?p ?o.
 }
 UNION
 #Level 2 = get triples to triples from tree root node
 {
  FILTER(?s_m1=$ENTITY_ID) .
  ?s_m1 ?p_m1 ?o_m1. FILTER(?o_m1=?o) .
  ?s ?p ?o.
 }
}
EOQ;
		else if($DEPTH==3)
$QUERY=<<<EOQ
$PREFIX
select distinct ?s ?p ?o
WHERE
{
#Level 1 = get triples from tree root node:
 {
  FILTER(?s=$ENTITY_ID ) .
  ?s ?p ?o.
 }
 UNION 
 #Level 1 = get triples to tree root node:
 {
  FILTER(?o=$ENTITY_ID) .
  ?s ?p ?o.
 }
 
 UNION 
 #Level 2 = get triples from triples from tree root node
 {
  FILTER(?s_m1=$ENTITY_ID) .
  ?s_m1 ?p_m1 ?o_m1. FILTER(?o_m1=?s) .
  ?s ?p ?o.
 }
 UNION
 #Level 2 = get triples to triples from tree root node
 {
  FILTER(?s_m1=$ENTITY_ID) .
  ?s_m1 ?p_m1 ?o_m1. FILTER(?o_m1=?o) .
  ?s ?p ?o.
 }
 UNION 
 #Level 3 direct = get triples from triples from triples from tree root node
 {
  FILTER(?s_m2=$ENTITY_ID) .
  ?s_m2 ?p_m2 ?o_m2. FILTER(?o_m2=?s_m1) .
  ?s_m1 ?p_m1 ?o_m1. FILTER(?o_m1=?s) .
  ?s ?p ?o.
 }
 UNION 
 #Level 3 inverse = get triples to triples from triples from tree root node
 {
  FILTER(?s_m2=$ENTITY_ID) .
  ?s_m2 ?p_m2 ?o_m2. FILTER(?o_m2=?s_m1) .
  ?s_m1 ?p_m1 ?o_m1. FILTER(?o_m1=?o) .
  ?s ?p ?o.
 }
}
EOQ;
		else fontprint("SORRY: Only a SUBTREE depht of 1,2, or 3 is accepted - ($DEPTH provided)",'red');

	}

	if ($debug)
	print "<br>>Your Query: <br>".str_replace("\n","<br>",htmlentities($QUERY))."<br>";

	$selection_vars=get_sparql_selection_vars($QUERY);
	//$selection_vars=array('x','z','p','g');
	
	if ($QUERY)
	$rows = $store->query($QUERY, 'rows'); 
  if (($errs = $store->getErrors())) {
      foreach($errs as $err)
      fontprint("<br>ARC ERROR: $err",'red');
			print "<br>";
  }
	
	if (strstr($QUERY,'ASK'))
	{
		print "<br>ASK returns: "; var_dump($rows);
	}
		
	if ($rows) 
	{
		
    $NRECORDS = count($rows);
		
		if (($c=count($selection_vars))==3)
		{
			$ITEMS='TRIPLES';
			$EXPL='';
		}
		else
		{
			$ITEMS='RECORDS';
			if ($c>3)
				$EXPL="($c-tuples)";
			else if ($c==2)
				$EXPL="(pairs)";
			else if ($c==1)
				$EXPL="(single objects)";
		}
		print "$NRECORDS $ITEMS $EXPL selected from STORE <b>'$storename'</b> ($triplesinstore triples) "
					 ."<span style='color:green'> - See also graph below, after displayed RECORDS</span>";
		;
		print '<table bgcolor=gray border=1>';
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
				$elem = separate_namespace($NAMESPACES,$row[$var],':',true);
				$r.="<td>&nbsp;".$elem."</td>";
			}
  		$r."</tr>";
			print $r;
		}
		
		print '</table>';

	}
	return $rows;
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
		$vars=trim(str_replace('distinct','',$vars));
		
		$vars_arr = explode(' ',$vars);
		
		//print "<br>RETURNING vars ($vars) arr: "; var_dump($vars_arr);
		return $vars_arr;	
	}
	return array();
}

?>
</body>
</html>