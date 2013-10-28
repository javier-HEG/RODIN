<?php
$format = $_GET['format']; 

include("../sroot.php");
include("FRIutilities.php");
include_once("../../../../gen/u/arc/ARC2.php");

$PAGETITLE= "dbRODIN LoD SPARQL endpoint ($RODINSEGMENT)";
if (!$format) {
?>
<html>
<head>
	<title>
		<?php print $PAGETITLE ?>
	</title>
	<link rel="stylesheet" type="text/css" href="../../../app/css/rodin.css.php?" />
	<script type='text/javascript' src='../../../app/u/RODINutilities.js.php?skin=<?php print $RODINSKIN;?>'></script>
</head>
<body>

<?php
}
else {
	$downloadfilename='myrodinloddownload.'.$format;
}

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



$LOCALARCCONFIG = $ARCCONFIG;
$LOCALARCCONFIG{'store_name'} = $storename;

$path_arcUtilities = $LOCALARCCONFIG{'arcUtilities'}	;
include_once($path_arcUtilities);


$thislink	=$_SERVER['PHP_SELF'];

include_once "../../../app/u/RDFprocessor.php";

$requesting_host=$_SERVER['SERVER_NAME'];
$RDFobj = new RDFprocessor($sid,$USER_ID=-1,$requesting_host);
$RDFC=get_class($RDFobj);
$NAMESPACES=RDFprocessor::$NAMESPACES;
//Fill namespaces into config
$LOCALARCCONFIG{'ns'}=$NAMESPACES;
$store=$RDFobj->store;
$storename=$RDFobj->storename;

if (!$format) {
	
$downloadurl=$WEBROOT.$thislink
						."?QUERY=".urlencode($QUERY)
						."&limit=$limit"
						."&token=$token"
						."&verb=$verb"
						."&format"
						;
$dtitle="Download your records as";

$PAGETITLE=str_replace("($RODINSEGMENT)","<span title='$storename'>($RODINSEGMENT)</span>",$PAGETITLE);
$on_turtle="window.open('$downloadurl=turtle','_self'); return false;";
$on_ntriples="window.open('$downloadurl=n-triples','_self'); return false;";
$on_rdfjson="window.open('$downloadurl=rdfjson','_self'); return false;";
$on_rdfxml="window.open('$downloadurl=rdfxml','_self'); return false;";


$DOWNLOAD_AS="<table cellspacing='2' cellpadding='0'><tr><td style='color:gray'>Download qualified records as </td>"
						."<td> <input type='button' value='TURTLE' title='$dtitle turtle' onclick=\"$on_turtle\"> </td>"
						."<td> <input type='button' value='N-TRIPLES' title='$dtitle turtle' onclick=\"$on_ntriples\"> </td>"
						."<td> <input type='button' value='RDF/JSON' title='$dtitle turtle' onclick=\"$on_rdfjson\"> </td>"
						."<td> <input type='button' value='RDF/XML' title='$dtitle turtle' onclick=\"$on_rdfxml\"> </td>"
						."</tr></table>";

if ($token)
print "<h3> Matching $token</h3>";
if ($verb)
print "<h3> Predicate $verb</h3>";

print <<<EOP
<table border=0>
		<tr height="5">
			<td align='left' valign='top'>
				<span class='lodsparqlendpointtitle'>$PAGETITLE</span>
				<br>
				<br>
				$DOWNLOAD_AS
			</td><td/>
			<td align='right' valign='top'>
				<span class='$TRIPLEPAGECLASS'>$URL_MANTIS</span>
				<div id='headerlogo'>
					<table>
					<tr>
					<td>
						<!--a href='$lodLABHOMEPAGEURL' title="Click to go back to RODIN's lod LAB homepage"-->
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
	<tr><td colspan=3>Select RODIN LoD data with your SPARQL query &nbsp;&nbsp;
	<span style="color:gray"> (Special function: PREFIX EXPRS + SUBTREE(ns:entity_id,n) displays a subgraphtree of depth n)</span>:</tr>
	<tr><td colspan=3><textarea style="width:100%;height:200px" name='QUERY'>$QUERY</textarea></tr>
	<tr><td colspan=3><input type=submit style='width:100%' value='Submit'></tr>
	<input type=hidden name=storename value='$storename'>
</form>
</table>
EOP;
}


//Show triples
list($ITEMKIND,$triples) = exec_print_sparql_xyz($doprint=!$format, $limit, $token, $verb, $QUERY);

if ($format)
{
	header('Content-Disposition: attachment; filename="'. $downloadfilename . '";');
	switch($format)
	{
		case 'turtle':
			header('Content-type: text/turtle');
			output_as_turtle($store,$triples);
			break;
		case 'n-triples':
			header('Content-type: text/plain');
			output_as_ntriples($store,$triples);
			break;
		case 'rdfjson':
			header('Content-type: application/rdf+json');
			output_as_rdfjson($store,$triples);
			break;
		case 'rdfxml':
			header('Content-type: application/rdf+xml');
			output_as_rdfxml($store,$triples);
			break;
	}
}
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

if ($ITEMKIND=='TRIPLES')
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
 */
function exec_print_sparql_xyz($doprint,$limit,$token='',$verb='',$QUERY='')
{
	global $store;
	global $storename;
	global $NAMESPACES;
	global $PROT;
	
	global $RODINUTILITIES_GEN_URL;
	$IMG3P_ICON = "$RODINUTILITIES_GEN_URL/images/icon_arrow_right2.png";
	$IMG3P="<img src='$IMG3P_ICON' width='15'>";
	$ownnamespacename='rodin';
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
		
		if($doprint)
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
	
	if ($QUERY)
	$rows = $store->query($QUERY, 'rows'); 
	if (0)
	{
	  if (($errs = $store->getErrors())) {
	      foreach($errs as $err)
	      fontprint("<br>ARC ERROR: $err",'red');
				print "<br>";
	  }
	}
	
	if (strstr($QUERY,'ASK'))
	{
		if($doprint)
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
		$EVTL_TRIPLEGRAPH=($c==3)?"<br><span style='color:green'> - See also graph below (if triples selected), after displayed RECORDS</span>":"";
		
		if($doprint)
		print "<br>$NRECORDS $ITEMS $EXPL selected from STORE <b>'$storename'</b> ($triplesinstore triples) "
					 .$EVTL_TRIPLEGRAPH;
		;
		if($doprint)
		print '<table bgcolor=gray border=1 cellspacing=0 cellpadding=1>';
    $i=-1;
		
		$r="<tr><td align='right'></td>";
		foreach($selection_vars as $var)
		{
			$r.="<th align=left>?$var:</th>";
		}
		$r."</tr>";
		if($doprint) print $r;
					
			
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
			if($doprint) print $r;
		}
		
		if($doprint) print '</table>';

	}
	return array($ITEMS,$rows);
}







/**
 * @return a serialisation of triples 
 */
function output_as_turtle(&$store,&$triples)
{
	/* Serializer instantiation */
	$ser = ARC2::getTurtleSerializer();
	
	/* Serialize a triples array */
	$doc = $ser->getSerializedTriples($triples);
	
	print $doc;
} // get_turtle




/**
 * @return a serialisation of triples 
 */
function output_as_ntriples(&$store,&$triples)
{
	/* Serializer instantiation */
	$ser = ARC2::getNTriplesSerializer();
	
	/* Serialize a triples array */
	$doc = $ser->getSerializedTriples($triples);
	
	print $doc;
} // output_as_ntriples





/**
 * @return a serialisation of triples 
 */
function output_as_rdfjson(&$store,&$triples)
{
	/* Serializer instantiation */
	$ser = ARC2::getRDFJSONSerializer();
	
	/* Serialize a triples array */
	$doc = $ser->getSerializedTriples($triples);
	
	print $doc;
} // output_as_rdfjson




/**
 * @return a serialisation of triples 
 */
function output_as_rdfxml(&$store,&$triples)
{
	/* Serializer instantiation */
	$ser = ARC2::getRDFXMLSerializer();
	
	/* Serialize a triples array */
	$doc = $ser->getSerializedTriples($triples);
	
	print $doc;
} // output_as_rdfxml








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
if (!$format) {
?>
</body>
</html>
<?php } ?>