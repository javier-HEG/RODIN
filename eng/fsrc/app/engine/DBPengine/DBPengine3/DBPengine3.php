<?php

	# DBP Engine 3
	#
	# Mai 2011
	# Fabio.fr.Ricci@hesge.ch  
	# HEG 

$THISFILE=__FILE__;
$THISCLASSNAME = basename(dirname($THISFILE)); // 
$BASECLASSNAME = basename(dirname(dirname($THISFILE))); // 

#Automatically load upper class
$filename="$BASECLASSNAME.php"; $max=10;
#######################################
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}


	
	
	
	
class DBPengine3 extends DBPengine 
{
	
	private $dbpedia_base;
	
	function __construct() 
	#########################
	{
		parent::__construct();
		
		global $DBPEDIA_BASE;
		
		$this->dbpedia_base=$DBPEDIA_BASE;
		$this->setWordbinding('DBP');
		//print "<br> STWengine1<hr>"; var_dump($this->get_zbwdbpedia_store());print "<hr>";
	} //STWengine1 
	
	

	
	
	
	
	protected function refine_method($term,$action,$lang)
	############################################################
	/*
	 * Finde Terme zu $action (lade wenn notw. Triples in den store)
	 */
	{
		$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
		global $DBPEDIA_BASE;
		
		if ($this->getSrcDebug()) 
		{
			$href_dbpedia_category="<a href=\"$DBPEDIA_BASE/page/Category:".dirtydown_viki_tokens($word)."\" title='As DBPedia category' target=_blank>Category</a>";	
			$href_dbpedia_normal="<a href=\"$DBPEDIA_BASE/page/".dirtydown_viki_tokens($word)."\" title='As DBPedia resource' target=_blank>Resource</a>";	
			print "<br>find_dbpedia <b>$action</b> to <i><b>'$term'</b></i>: $href_dbpedia_category/$href_dbpedia_normal:<br>";
		}
	
		
		$skos_terms=$this->get_dbpedia_skos_terms($term,$lang,$action);
		
		#Add category link to term to form an absolute URI to each skos term
		$DBPEDIACAT="http://dbpedia.org/resource/Category:";
		if (count($skos_terms))
		foreach($skos_terms as $st=>$Rank)
		{
			if ($this->getSrcDebug()) print "<br> $st --> $DBPEDIACAT$st";
			$dbpedia_raw_terms{$st}=$DBPEDIACAT.$st;
		}		
		return array($skos_terms,$dbpedia_raw_terms); 
	} // refine_method
	
	
	
function get_dbpedia_skos_terms($term,$language,$action)
/*
 * 
 */
{
	$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
	
	#### Open SKOS Self Category
	switch($action)
	{
		case('broader'):
			$skos_terms=$this->get_dbpedia_triples_broader_direct($term,$lang);
			break;
		case('narrower'):
			$skos_terms=$this->get_dbpedia_triples_narrower_direct($term,$lang);
			break;
		case('related'):	
			$skos_terms=$this->get_dbpedia_triples_related_direct($term,$lang);
		break;
	} // switch
	
	
	
	if ($this->getSrcDebug()) print "<br>$METHODNAME<br><b>get_dbpedia_skos_terms($term) </b> returning '$skos_terms'";

	
	return $skos_terms; // je action
} //get_dbpedia_skos_terms






function get_dbpedia_triples_broader_direct($term,$language='en')
#############################################
{
	$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
	//Zuerst oeffne wikipedia mit Term
	//Dann gewinne Resource-Name nach Disambiguation
	if ($this->getSrcDebug()) print "<br>$METHODNAME<br>get_dbpedia_triples_direct($term)...";

	//Selektiere alles, was gebraucht wird, um einen Triple-"Einkauf" zu tätigen:
	// B = broaders zu $term
	// N = narrower zo $term
	// R = related zu $term (schwieriger)
	// bnrB = zu jedem Word in B jeweilig die Anzahl der Summe seiner broaders/narrowers/relateds
	// bnrN = zu jedem Word in N jeweilig die Anzahl der Summe seiner broaders/narrowers/relateds
	// bnrR = zu jedem Word in R jeweilig die Anzahl der Summe seiner broaders/narrowers/relateds
	//

	/*
	 * QUERY Broader + (B N) + R (todo)
	 * 
	 * broaders
	 * broader broader
	 * broader narrower
	 * broader related
	 * 
	 * 
	 */
	//Folgende Subquery generiert die broaders auch dann, wenn es keine direkte 
	//DBPEDIA SKOS Category zum Term $term gibt. Diese Subquery wird deshalb wiederholt in 
	//der Gesamtquery, um zu den gefundenen Elementen jeweils die b/n/r zu ermitteln
	
	############################################
	list($node,$label) = DBPengine::extract_DBPnode($term);
	############################################
	if ($node) # Request is on a node (label) exactely
	{
		#Re call wikipedia onto search with this word
		$term = dirtydown_viki_tokens($label);
		
	} # node
	###########################################
	
	
	
	$SUBQUERY_GEN_xxx=<<<EOSQ
	{
		{
			<http://dbpedia.org/resource/Category:$term>  <http://www.w3.org/2004/02/skos/core#broader> ?xxx .
		}
		UNION
		{
			<http://dbpedia.org/resource/$term>  <http://www.w3.org/2004/02/skos/core#subject> ?sub .
			?sub  <http://www.w3.org/2004/02/skos/core#broader> ?xxx .
		}
	}
EOSQ;
	
	$SUBQUERY_GEN_b = str_replace('xxx','b',$SUBQUERY_GEN_xxx);
	
	
	$QUERY=<<<EOQ
SELECT ?bx ?b
WHERE
{ 
	$SUBQUERY_GEN_b
	
	UNION
	
	
	{
		{
			{
				<http://dbpedia.org/resource/Category:$term>  <http://www.w3.org/2004/02/skos/core#broader> ?bx .
				?bx <http://www.w3.org/2004/02/skos/core#broader> ?b .
			}
			UNION
			{
				<http://dbpedia.org/resource/$term>  <http://www.w3.org/2004/02/skos/core#subject> ?sub .
				?sub  <http://www.w3.org/2004/02/skos/core#broader> ?bx .
				?bx <http://www.w3.org/2004/02/skos/core#broader> ?b .
			}
		}
	}
	UNION
	
	
	{
		{
			<http://dbpedia.org/resource/Category:$term>  <http://www.w3.org/2004/02/skos/core#broader> ?bx .
			?b <http://www.w3.org/2004/02/skos/core#broader> ?bx .
		}
		UNION
		{
			<http://dbpedia.org/resource/$term>  <http://www.w3.org/2004/02/skos/core#subject> ?sub .
			?sub  <http://www.w3.org/2004/02/skos/core#broader> ?bx .
			?b <http://www.w3.org/2004/02/skos/core#broader> ?bx .
		}
	
	}
}
EOQ;

	$QUERY= urlencode($QUERY);
	
	
	
	if ($this->getSrcDebug()) 
	{
		print "<br>$METHODNAME<br><b>QUERY</b> (<a href='http://dbpedia.org/sparql' target=_blank>Checkout in Virtuoso</a>):<br>".show_xml_string(urldecode($QUERY))."<br>";
	}
	
	
	
	$SubtractFromValue="http://dbpedia.org/resource/Category:";
	$endpointResults=get_dbpediaendpoint_results($QUERY,$SubtractFromValue);

	
	if ($this->getSrcDebug()) 
		print_endpoint_results($endpointResults);
	
	// Berechne die Gewichtung von jedem Broaderterm und wähle/liefere die ersten $m terme zurück
	$ordered_results_array=waight_and_order_refine_results($endpointResults);
	
	// returns an array of pairs (cleanterm, raw_term)
	return $ordered_results_array;
} //get_dbpedia_triples_broader_direct












function get_dbpedia_triples_narrower_direct($term,$language='en')
#############################################
{
	$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
	
	//Zuerst oeffne wikipedia mit Term
	//Dann gewinne Resource-Name nach Disambiguation
	if ($this->getSrcDebug()) print "<br>get_dbpedia_triples_direct($term)...";

	//Selektiere alles, was gebraucht wird, um einen Triple-"Einkauf" zu tätigen:
	// B = broaders zu $term
	// N = narrower zo $term
	// R = related zu $term (schwieriger)
	// bnrB = zu jedem Word in B jeweilig die Anzahl der Summe seiner broaders/narrowers/relateds
	// bnrN = zu jedem Word in N jeweilig die Anzahl der Summe seiner broaders/narrowers/relateds
	// bnrR = zu jedem Word in R jeweilig die Anzahl der Summe seiner broaders/narrowers/relateds
	//

	/*
	 * Probiere zunaechst mir Category, sonst mit Resource->skos:subject->Category
	 */
	############################################
	list($node,$label) = DBPengine::extract_DBPnode($term);
	############################################
	if ($node) # Request is on a node (label) exactely
	{
		#Re call wikipedia onto search with this word
		$term = dirtydown_viki_tokens($label);
	} # node
	###########################################
	
	
	$QUERY_SIMPLE=<<<EOQ
SELECT ?nx ?n 
WHERE
{ 	
	{
		?n <http://www.w3.org/2004/02/skos/core#broader> <http://dbpedia.org/resource/Category:$term>.
	}
	UNION
	{
		?n <http://www.w3.org/2004/02/skos/core#broader> <http://dbpedia.org/resource/Category:$term>.
  		?n <http://www.w3.org/2004/02/skos/core#broader> ?nx.	
	}
 	UNION
	{
		?n <http://www.w3.org/2004/02/skos/core#broader> <http://dbpedia.org/resource/Category:$term>.
		?nx <http://www.w3.org/2004/02/skos/core#broader> ?n.
		
	}
}
EOQ;
	$QUERY_SIMPLE= urlencode($QUERY_SIMPLE);
	
	
	
	
	//Folgende Subquery generiert die narrower auch dann, wenn es keine direkte 
	//DBPEDIA SKOS Category zum Term $term gibt. Diese Subquery wird deshalb wiederholt in 
	//der Gesamtquery, um zu den gefundenen Elementen jeweils die b/n/r zu ermitteln
	$SUBQUERY_GEN_xxx=<<<EOSQ
	{
		{
			?xxx <http://www.w3.org/2004/02/skos/core#broader> <http://dbpedia.org/resource/Category:$term>.
		}
		UNION
		{
			<http://dbpedia.org/resource/$term>  <http://www.w3.org/2004/02/skos/core#subject> ?sub .
			?xxx <http://www.w3.org/2004/02/skos/core#broader> ?sub.
		}
	}
EOSQ;
	
	$SUBQUERY_GEN_n = str_replace('xxx','n',$SUBQUERY_GEN_xxx);	
	
	
	
	
	
	$QUERY_EXTENDED=<<<EOQ
SELECT ?nx ?n 
WHERE
{ 
	$SUBQUERY_GEN_n
UNION
	{
  		{
			?nx <http://www.w3.org/2004/02/skos/core#broader> <http://dbpedia.org/resource/Category:$term> .
	  		?n <http://www.w3.org/2004/02/skos/core#broader> ?nx.	
		}
		UNION
		{
			<http://dbpedia.org/resource/$term>  <http://www.w3.org/2004/02/skos/core#subject> ?sub .
			?nx <http://www.w3.org/2004/02/skos/core#broader> ?sub .
			?n <http://www.w3.org/2004/02/skos/core#broader> ?nx .			
		}
	}
 	UNION
	{
		{
			?nx <http://www.w3.org/2004/02/skos/core#broader> <http://dbpedia.org/resource/Category:$term>.
			?nx <http://www.w3.org/2004/02/skos/core#broader> ?n.
		}
		UNION
		{
			<http://dbpedia.org/resource/$term>  <http://www.w3.org/2004/02/skos/core#subject> ?sub .
			?nx <http://www.w3.org/2004/02/skos/core#broader> ?sub .
			?nx <http://www.w3.org/2004/02/skos/core#broader> ?n .
		}
	}
}
EOQ;

	
/*
 *	
 */	
	
	$QUERY_EXTENDED= urlencode($QUERY_EXTENDED);
	$QUERY=$QUERY_SIMPLE;
	$SubtractFromValue="http://dbpedia.org/resource/Category:";
	
	//Try to get values mit $QUERY_SIMPLE
	
	$endpointResults=get_dbpediaendpoint_results($QUERY,$SubtractFromValue);
	
	if (count($endpointResults['results']) == 0)
	{
		if ($this->getSrcDebug()) 
			print "<br><b>QUERY</b> (<a href='http://dbpedia.org/sparql' target=_blank>Checkout in Virtuoso</a>):<br>".show_xml_string(urldecode($QUERY_SIMPLE)).
			"<br><b>hat zu einem LEEREN RESULTAT gefuehrt. <br>Probiere nun ueber skos:subject mit </b>".show_xml_string(urldecode($QUERY_EXTENDED));
	
		
		$QUERY=$QUERY_EXTENDED;
		$endpointResults=get_dbpediaendpoint_results($QUERY,$SubtractFromValue);
	} // $QUERY_EXTENDED
	

	if ($this->getSrcDebug()) 
		print_endpoint_results($endpointResults);
	
		// Berechne die Gewichtung von jedem Broaderterm und wähle/liefere die ersten $m terme zurück
	$ordered_results_array=waight_and_order_refine_results($endpointResults);
	
	
	
	
	return $ordered_results_array;
} //get_dbpedia_triples_narrower_direct










function get_dbpedia_triples_related_direct($term,$language='en')
#############################################
{
	$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
	//Dann gewinne Resource-Name nach Disambiguation
	if ($this->getSrcDebug()) print "<br>get_dbpedia_triples_direct($term)...";

	//Selektiere alles, was gebraucht wird, um einen Triple-"Einkauf" zu tätigen:
	// B = broaders zu $term
	// N = narrower zo $term
	// R = related zu $term (schwieriger)
	// bnrB = zu jedem Word in B jeweilig die Anzahl der Summe seiner broaders/narrowers/relateds
	// bnrN = zu jedem Word in N jeweilig die Anzahl der Summe seiner broaders/narrowers/relateds
	// bnrR = zu jedem Word in R jeweilig die Anzahl der Summe seiner broaders/narrowers/relateds
	//

	/*
	 * QUERY Broader + (B N) + R (todo)
	 * 
	 * Get bx=broaders
	 * Get bx=broaders, b=broaders of broader
	 * Get bx=broaders, b=narrower of broader
	 * TODO: related !!!!!
   */
	
	############################################
	list($node,$label) = DBPengine::extract_DBPnode($term);
	############################################
	if ($node) # Request is on a node (label) exactely
	{
		#Re call wikipedia onto search with this word
		$term = dirtydown_viki_tokens($label);
	} # node
	###########################################
	
	
	$QUERY= urlencode("
SELECT  ?rx ?r
WHERE
{
	{ 
		<http://dbpedia.org/resource/$term> <http://www.w3.org/2004/02/skos/core#subject> ?r . 
	}
	UNION
	{ 
		<http://dbpedia.org/resource/$term> <http://purl.org/dc/terms/subject> ?r . 
	}
	UNION
	{ 
		<http://dbpedia.org/resource/$term> <http://www.w3.org/2004/02/skos/core#subject> ?rx . 
		?rx <http://www.w3.org/2004/02/skos/core#broader> ?r . 
	}
	UNION
	{ 
		<http://dbpedia.org/resource/$term> <http://www.w3.org/2004/02/skos/core#subject> ?rx . 
		?r <http://www.w3.org/2004/02/skos/core#broader> ?rx . 
	}
	
	UNION
	{ 
		<http://dbpedia.org/resource/$term> <http://purl.org/dc/terms/subject> ?rx . 
		?rx <http://www.w3.org/2004/02/skos/core#broader> ?r . 
	}
	UNION
	{ 
		<http://dbpedia.org/resource/$term> <http://purl.org/dc/terms/subject> ?rx . 
		?r <http://www.w3.org/2004/02/skos/core#broader> ?rx . 
	}
	
	
}	
");

	if ($this->getSrcDebug()) 
	{
		print "<br>$METHODNAME<br><b>QUERY</b> (<a href='http://dbpedia.org/sparql' target=_blank>Checkout in Virtuoso</a>):<br>".show_xml_string(urldecode($QUERY));
	}
	
	
	
	$SubtractFromValue=array("http://dbpedia.org/resource/Category:","http://dbpedia.org/resource/","Place_name_disambiguation_pages");
	$endpointResults=get_dbpediaendpoint_results($QUERY,$SubtractFromValue);

	if (!count($endpointResults['results'])) 
	{
			// Try $term as category
		$QUERY= urlencode("
SELECT  ?rx ?r
WHERE
{
	{ 
		?r dcterms:subject <http://dbpedia.org/resource/Category:$term>. 
	}
	UNION
	{ 
		<http://dbpedia.org/resource/$term> <http://www.w3.org/2004/02/skos/core#subject> ?rx . 
		?rx <http://www.w3.org/2004/02/skos/core#broader> ?r . 
	}
	UNION
	{ 
		<http://dbpedia.org/resource/$term> <http://www.w3.org/2004/02/skos/core#subject> ?rx . 
		?r <http://www.w3.org/2004/02/skos/core#broader> ?rx . 
	}
}	
");

		if ($this->getSrcDebug()) 
		{
			print "<br>$METHODNAME<br><b>QUERY</b> (<a href='http://dbpedia.org/sparql' target=_blank>Checkout in Virtuoso</a>):<br>".show_xml_string(urldecode($QUERY));
		}
		
		$SubtractFromValue=array("http://dbpedia.org/resource/Category:","http://dbpedia.org/resource/","Place_name_disambiguation_pages");
		$endpointResults=get_dbpediaendpoint_results($QUERY,$SubtractFromValue);
		
	}
	
	
	if ($srcdebug) 
		print_endpoint_results($endpointResults);
	
		// Berechne die Gewichtung von jedem Broaderterm und wähle/liefere die ersten $m terme zurück
	$ordered_results_array=waight_and_order_refine_results($endpointResults);
	
	
	
	
	return $ordered_results_array;
} //get_dbpedia_triples_related_direct


	
	
	
	
	
} // class DBPengine3



?>