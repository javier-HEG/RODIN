<?php

	# STW Engine abstract
	#
	# Mai 2011
	# Fabio.fr.Ricci@hesge.ch  
	# HEG 

$filename="SRCengine.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) { include_once("$updir$filename");break;}

$filename="gen/u/arc/ARC2.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) { include_once("$updir$filename");break;}

$filename="fsrc/app/u/FRIutilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) { include_once("$updir$filename");break;}
	
$filename="../../../../../../app/u/FRIutilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) { include_once("$updir$filename");break;}
	

	




abstract class STWengine extends SRCengine
{

	
	function __construct() 
	#########################
	{
		parent::__construct();
		$this->setStores();
				
		//print "<br> STWengine<hr>"; var_dump($this);print "<hr>";
		
	} //constructor 
	
	
	protected function setStores()
	{
		global $ARCCONFIG;
		global $DBPEDIA_PREFIX;
		
		$localArcConfig = $ARCCONFIG;
		
		$this->store = NULL;
		
		$localArcConfig['store_name'] = 'zbw';
		$this->set_zbw_store(ARC2::getStore($localArcConfig));
		
		if (!$this->get_zbw_store()->isSetUp()) {
	  		$this->get_zbw_store()->setUp();
		}
		
		$localArcConfig['store_name'] = 'zbwdbpedia';
		$this->set_zbwdbpedia_store(ARC2::getStore($localArcConfig));
		
		if (!$this->get_zbwdbpedia_store()->isSetUp()) {
	  		$this->get_zbwdbpedia_store()->setUp();
		}
	}
	
	/**
	 * STW implementation of the function, since the data is held
	 * in our server, this function will always "suceed".
	 */
	protected function testDataSourceResponsiveness($user) {
		return "<user>$user</user>";
	}

	/**
	 * @see SRCengine.preprocess_refine() for details.
	 */
	protected function preprocess_refine($terms, $wordbinding='STW', $lang='en') {
		if ($this->getSrcDebug()) {
			print "<br>preprocess_refine($terms, $wordbinding, $lang);";
		}
		
		// TODO Implement ZBW_cleanup() and replace
		$terms = ZBW_dirtydown($terms);
		
		list($node, $label) = $this->extractNode($terms);

		if ($node) {
			// Then $terms are already concept URIs
			$preResults = new SRCEngineResult($terms);
		} else {
			$candidate_query_terms_str = group_contigous_words($terms,$lang);
			
			if ($lang=='en') {
				list($english_candidate_compounds_str, $english_candidate_compounds_str_raw) =
					$this->get_english_candidate_compounds($candidate_query_terms_str);
			} else {
				$english_candidate_compounds_str = $candidate_query_terms_str;
				$english_candidate_compounds_str_raw = '';
			}
			
			$stop_cleaned_words_str = cleanup_stopwords_str($english_candidate_compounds_str);
			
			if ($wordbinding == 'STW') {
				$preResults = new SRCEngineResult($stop_cleaned_words_str, $english_candidate_compounds_str_raw);
			}
				
			if($this->getSrcDebug()) {
				print "<br>english_candidate_compounds_str=($english_candidate_compounds_str)";
				print "<br>stop_cleaned_words_str=($stop_cleaned_words_str)";
				print "<br>preprocess_refine returning (" . $preResults->results . ")";	
			}
		}
			
		return $preResults;
	}
		
	
	/**
	 * Checks if the $term matches anyone of: 
	 * - http://zbw.eu/stw/thsys/70244 (LabelThsys)
	 * - http://zbw.eu/stw/descriptor/18017-3 (LabelDescriptor)
	 * 
	 * Extracts the node ID (eg. 70244) and the label (eg. LabelThsys)
	 * from the $term and returns them in a list.
	 * 
	 * @param string $term
	 */
	protected function extractNode($term) {
		$ok= false;
		$node = $label = 0;
		
		$thsysBase = "http://zbw.eu/stw/thsys/";
		$descriptorBase = "http://zbw.eu/stw/descriptor/";
		$regExpThsys = "/^http:\/\/zbw\.eu\/stw\/thsys\/(\d*) \((.*)\)$/";
		$regExpDescriptor = "/^http:\/\/zbw\.eu\/stw\/descriptor\/(\d*)-(\d*) \((.*)\)$/";
		
		if (preg_match($regExpThsys, $term, $match)) {
			$ok = true;
			$node = $thsysBase . $match[1];
			$label = $match[2];
			
			if($this->getSrcDebug()) {
				print "<br>Matched $regExpThsys in ($term), node=($node) label=($label)";
			}
		} else if (preg_match($regExpDescriptor, $term, $match)) {
			$ok = true;
			$node = $descriptorBase . $match[1] . "-" . $match[2];
			$label = $match[3];
			
			if($this->getSrcDebug()) {
				print "<br>Matched $regExpDescriptor in ($term), node=($node) label=($label)";
			}
		} else if ($this->getSrcDebug()) {
			print "<br><b>NO match </b> ($term)";
		}
		
		return array($node,$label);		
	}
	
	protected function validateTermInOntology($term, $lang = 'en') {
		if ($this->getSrcDebug()) {
			print "<br>stw_validate_term($term,$lang='en') ...";
		}
		
		$suggested_term = $this->checkterm_in_stw($term,'X',$lang, $this->get_zbw_store());
		
		if ($suggested_term != '') { // let's get the terms URI
			$query = <<<EOQ
				prefix skos:  <http://www.w3.org/2004/02/skos/core#>
				select ?uri where { 
						 { ?uri skos:prefLabel '$term' . }
						 UNION
						 { ?uri skos:altLabel '$term' . }
					}
EOQ;

			$store = $this->get_zbw_store();
			if ($rows = $store->query($query, 'rows')) {
				return array($term, base64_encode($rows[0]['uri']));
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Checks if $term is contained as a label in the SKOS ontology,
	 * returns a suggested term or ''.
	 */
	protected function checkterm_in_stw($term, $SearchType, $lang, $store) {			
		if ($this->getSrcDebug()) {	
			print "<br>STORE: <br>";
			var_dump($store);
		}
		
		switch($SearchType) {
			case 'X':
				$QUERY = <<<EOQ
					prefix skos:  <http://www.w3.org/2004/02/skos/core#>
					select ?d2 where { 
						 {
							?d2 skos:prefLabel '$term' .
						 }
						 UNION
						 {
							?d2 skos:altLabel '$term' .
						 }
					}
EOQ;
				break;

			case 'XX':
				$QUERY=<<<EOQ
					prefix skos:  <http://www.w3.org/2004/02/skos/core#>
					select ?d2 where {
						{
							?d2 	skos:prefLabel 	?p .
							FILTER regex(?p, "$term", "i") 
					 	}
					}
EOQ;
				break;
		
			case ('XXL'):
				$QUERY=<<<EOQ
					prefix skos:  <http://www.w3.org/2004/02/skos/core#>
					select ?d2 where {
						{
							?d2 	skos:prefLabel 	?p .
							FILTER regex(?p, "$term", "i")
						}
						UNION
						{
							?d2 	skos:altLabel 	?p .
							FILTER regex(?p, "$term", "i")
						}
					}
EOQ;
		}
		
		if ($this->getSrcDebug()) {
			print "<br><br>checkterm_in_stw($exactSearch): $QUERY <br>";
		}
		
		$found = false;
		if ($rows = $store->query($QUERY, 'rows')) {
			$found = count($rows);
			
			if ($this->getSrcDebug()) {	
				print "<br>DUMP RESULTS: <br>";
				var_dump($rows);
			}
		} else {
			if ($this->getSrcDebug()) {
				print "!!! NO RESULTS OR NO QUERY LAUNCHED!!!";
			}
		}
		
		if ($this->getSrcDebug()) {
			print "<br><br>checkterm_in_stw($exactSearch): returning found=$found<br>";
		}
		
		if ($found)	{
			$suggested_term = $term;
		} else {
			$suggested_term = '';
		}
		
		return $suggested_term;
	}
	
} // class STWengine



?>