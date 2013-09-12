<?php

$format = trim(strtolower($_GET['format']));
switch ($format)
{
	case 	'xmlrdf':		break;
	case 	'rdfjson':	break;
	case	'turtle':		break;
	case	'ntriples':	break;
	case	'html':			break;
	default: $format='html';
}
//siehe http://arc.semsol.org/docs/v2/getting_started

include("../sroot.php");
include("FRIutilities.php");
include_once("../../../gen/u/arc/ARC2.php");

if ($format=='html')
{
		$PAGETITLE= "dbRODIN LoD SPARQL endpoint ($RODINSEGMENT)"; 
		print <<<EOP
<html>
<head>
	<title>
		$PAGETITLE 
	</title>
	<link rel="stylesheet" type="text/css" href="../../../app/css/rodin.css.php?" />
	<script type='text/javascript' src='../../../app/u/RODINutilities.js.php?skin=<?php print $RODINSKIN;?>'></script>
</head>
<body>
EOP;
}

include_once "../../../app/u/RDFprocessor.php";

$filename="app/tests/Logger.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
{if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}}

$filename="app/u/FRIdbUtilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
{if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}}

$limit = $_GET['limit']; if (!$limit) $limit=0;
$token = $_GET['token']; 
$QUERY = $_GET['QUERY']; 
$verb = $_GET['verb']; 

$thislink	=$_SERVER['PHP_SELF'];
$requesting_host=$_SERVER['SERVER_NAME'];
$RDFobj = new RDFprocessor($sid,$USER_ID=-1,$requesting_host);
$RDFC=get_class($RDFobj);
$NAMESPACES=RDFprocessor::$NAMESPACES;
//Fill namespaces into config
$LOCALARCCONFIG = $RDFobj->LOCALARCCONFIG;
include_once($LOCALARCCONFIG{'arcUtilities'});

$store=$RDFobj->store;
$storename=$RDFobj->storename;
$PAGETITLE=str_replace("($RODINSEGMENT)","<span title='$storename'>($RODINSEGMENT)</span>",$PAGETITLE);

if ($format=='html')
{
	
	$SAMESTORY=$thislink.'?'.$_SERVER['QUERY_STRING'].'&format=';
	
if ($token)
print "<h3> Matching $token</h3>";
if ($verb)
print "<h3> Predicate $verb</h3>";

print <<<EOP
<table border=0 cellpadding=0 cellspacing=0>
		<tr height="5">
			<td align='left' valign='top'>
				<table border=0 cellpadding=0 cellspacing=0>
					<tr>
						<td>
							<span class='lodsparqlendpointtitle'>$PAGETITLE</span>
						</td>
					</tr>
					<tr height=15 />
					<tr>
						<td valign=bottom> <span class='lodsparqlendpointtitle'>Donwload results as </span> 
							<input 	type=button 
											name=wantxmlrdf
											value="XML/RDF"
											title="Download current results in xml/rdf format"
											onclick="window.open('{$SAMESTORY}xmlrdf','_self')" >
							<input 	type=button 
											name=wantxmlrdf
											value="RDF/JSON"
											title="Download current results in rdf/json format"
											onclick="window.open('{$SAMESTORY}rdfjson','_self')" >
							<input 	type=button 
											name=wantxmlrdf
											value="TURTLE"
											title="Download current results in turtle format"
											onclick="window.open('{$SAMESTORY}turtle','_self')" >
							<input 	type=button 
											name=wantxmlrdf
											value="N-TRIPLES"
											title="Download current results in ntriples format"
											onclick="window.open('{$SAMESTORY}ntriples','_self')" >
						</td>
					</tr>
				</table>
			</td><td/>
			<td align='right' valign='top'>
				<span class='$TRIPLEPAGECLASS'>$URL_MANTIS</span>
				<div id='headerlogo'>
					<table>
						<tr>
						<td>
								<img src='$RODINLOGO' width='80'>
							</a>
						</td>
						</tr>
							<td>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
<form name='a' >
	<tr><td colspan=3><b>Select RODIN LoD data with your SPARQL query</b> &nbsp;&nbsp;
	<span style="color:gray"> (Special function: PREFIX EXPRS + SUBTREE(ns:entity_id,n) displays a subgraphtree of depth n)</span>:</tr>
	<tr><td colspan=3><textarea style="width:100%;min-height:200px" name='QUERY'>$QUERY</textarea></tr>
	<tr><td colspan=3><input type=submit style='width:100%' value='Submit query'></tr>
	<input type=hidden name=storename value='$storename'>
</form>
</table>
EOP;
} // format=html

//Show triples
list($ITEMKIND,$triples) = exec_print_sparql_xyz($RDFobj,$limit,$token,$verb,$QUERY,$format);


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

if ($format=='html' && $ITEMKIND=='TRIPLES')
{
$viz = ARC2::getComponent('TriplesVisualizerPlugin', $LOCALARCCONFIG);

/* display an svg image 
$svg = $viz->draw($triples, 'svg', 'base64');
echo '<embed type="image/svg+xml" src="data:image/svg+xml;base64,' . $svg . '"/>';
*/

/* display a png image */
$png = $viz->draw($triples, 'png', 'base64');
print '<img src="data:image/png;base64,' . $png . '"/>';
}
/* generate a dot file */
//$dot_src = $viz->dot($triples);


/*
 * Computes the query to SKOS $verb
 * $verb= related, broader, narrower
 * 
 * if format=='html'
 *  it prints triples and returns ($,$triples)
 * if format not 'html'
 *  output result and returns no triples
 */
function exec_print_sparql_xyz(&$RDFobj, $limit, $token='', $verb='', $QUERY='', $format='html')
{
	global $store;
	global $storename;
	global $NAMESPACES;
	global $PROT;
	global $RODINUTILITIES_GEN_URL;
	
	switch ($format)
	{
		case 	'xmlrdf': 
						$serializer = ARC2::getRDFXMLSerializer($RDFobj->LOCALARCCONFIG);
					break;
		case 	'rdfjson':
						$serializer = ARC2::getRDFJSONSerializer($RDFobj->LOCALARCCONFIG);
					break;
		case	'turtle':
						$serializer = ARC2::getTurtleSerializer($RDFobj->LOCALARCCONFIG);
					break;
		case	'ntriples':
						$serializer = ARC2::getNTriplesSerializer($RDFobj->LOCALARCCONFIG);
					break;
		case	'html':
						break;
	}
	
	
	$IMG3P_ICON = "$RODINUTILITIES_GEN_URL/images/icon_arrow_right2.png";
	$IMG3P="<img src='$IMG3P_ICON' width='15'>";
	$ownnamespacename='rodin';
	$triplesinstore= count_ARC_triples($store);
	$triplesinstore= number_format($triplesinstore, 0, '.', "'");
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

	$DEBUG=0;

	if ($DEBUG)
	{
		print "<br>>Your Query in store '$storename': <br>".str_replace("\n","<br>",htmlentities($QUERY))."<br>";

		if(1)
		{print "<br>STORE: <br><br>"; var_dump($store); print "<br>";} 
	}

	$selection_vars=get_sparql_selection_vars($QUERY);
	//$selection_vars=array('x','z','p','g');
	
	//	$output = $serializer->getSerializedTriples( $this->toArcTriples() );
	
	
	
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
	
	
	if ($format=='html')
	{		
		if ($rows) 
		{
			$graph_is_displayed= ($rows{'s'});
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
			$SEEALSOGRAPH=($graph_is_displayed)?"<br><span style='color:green'> - See also graph below (if triples selected), after displayed RECORDS</span>":'';
			print "<br>$NRECORDS $ITEMS $EXPL selected from STORE <b>'$storename'</b> ($triplesinstore triples) "
						 .$SEEALSOGRAPH;
			;
			print '<table bgcolor=gray border=1 cellspacing=0 cellpadding=1>';
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
					$LINK=$POINTERTRIPLEPAGE_ELEM='';
					if ($DEBUG) print "<hr><b>prettyprintURI</b>";
					$elem	= trim(prettyprintURI($row[$var],$NAMESPACES)) ;
					if ($DEBUG) print "<hr><b>separate_namespace (false)</b>";
					$elem_simple = separate_namespace($NAMESPACES,$row[$var],':',false);
					$TPAGELINK_ELEM=	strstr($elem,$ownnamespacename)
								?	correct_rodin_url(  $row[$var], $NAMESPACES  )
								:'';
					
					if ($TPAGELINK_ELEM)
					{
							$TITLE_TPAGELINK="Click to open dbRODIN LoD browser on \"$elem_simple\" in new tab";
							$LINK=<<<EOX
								onclick="window.open('$TPAGELINK_ELEM','_blank')"
								title='$TITLE_TPAGELINK'
								style="cursor:pointer"
EOX;
						$POINTERTRIPLEPAGE_ELEM=	" $IMG3P ";
					}
					$r.="<td nowrap=nowrap $LINK>&nbsp;".$elem.$POINTERTRIPLEPAGE_ELEM."</td>";
	
				}
	  		$r."</tr>";
				print $r;
			}
			
			print '</table>';
	
		}
	} // 'html'
	else { // requested format not html -> output it singularly with ContentType
	
		$output = $serializer->getSerializedTriples( $rows );
	
		switch ($format)
		{
			case 	'xmlrdf': 
							header('application/rdf+xml');
						break;
			case 	'rdfjson':
							header('application/rdf+json');
						break;
			case	'turtle':
							header('text/turtle');
						break;
			case	'ntriples':
							header('application/text/plain');
						break;
		} // switch
		
		header("Content-Disposition: attachment; filename=\"dbRODINsparqlEndpointDownload.$format\"");
		print $output;
	} // format not html -> output it singularly with ContentType
	return array($ITEMS,$rows);
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
} // get_sparql_selection_vars





if ($format=='html')
print "</body></html>";
?>
