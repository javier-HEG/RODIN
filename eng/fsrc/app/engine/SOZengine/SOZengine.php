<?php

	# SOZ Engine abstract
	#
	# Dec 2012 HEG
	# fabio.ricci@ggaweb.ch  
	# Tel.: +41-76-5281961

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
	




abstract class SOZengine extends SRCengine
{

	
	function __construct() 
	#########################
	{
   	parent::__construct();
		$this->setStores();
    $this->currentclassname='SOZengine';

		//print "<br> STWengine<hr>"; var_dump($this);print "<hr>";
		//print "<br>cons SOZengine executed";
	
	} //constructor 
	
	
	protected function setStores()
	{
		
	}
	
	/**
	 * SOZ implementation of the function, since the data is held
	 * in our server, this function will "succeed" in case there is data
	 */
	protected function testDataSourceResponsiveness($user) 
  {
    $isempty=solr_collection_empty($this->solr_collection);
    if ($isempty)
      $response="<error>SOLR connection for this ontology seems to be empty!</error>";
    else 
      $response="<user>$user</user>";
    return $response;
  }

	/**
	 * @see SRCengine.preprocess_refine() for details.
	 */
	protected function preprocess_refine($terms, $wordbinding='STW', $lang='en', $mode='web') {
		if ($this->getSrcDebug()) {
			print "<br>STWengine->preprocess_refine($terms, $wordbinding, $lang);";
		}
		
		// TODO Implement ZBW_cleanup() and replace
		$terms = ZBW_dirtydown($terms);
		
		list($descriptor, $label) = $this->extractDescriptor($terms);

		if ($descriptor) {
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
			
			if ($wordbinding == 'SOZ') {
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
	protected function extractDescriptor($term) {
		$ok= false;
		$node = $label = 0;
    
		if ($this->getSrcDebug()) {
			print "<br><b>SOZengine->extractDescriptor($term):";
		}
		
		$descriptorBase = "http://lod.gesis.org/thesoz/concept/";
    $regExpDescriptor = "/^http:\/\/lod.gesis.org\/thesoz\/concept\/(\d*)$/";
		
    if (preg_match($regExpDescriptor, $term, $match)) {
			$ok = true;
			$node = $descriptorBase . $match[1];
			$label = $match[3];
			
			if($this->getSrcDebug()) {
				print "<br>Matched $regExpDescriptor in ($term), node=($node) label=($label)";
			}
		} else if ($this->getSrcDebug()) {
			print "<br><b>NO match </b> ($term) as SOZ descriptor";
		}
		
		return array($node,$label);		
	}
	
	protected function validateTermInOntology($term, $lang = 'en') {
		if ($this->getSrcDebug()) {
			print "<br>stw_validate_term($term,$lang='en') ...";
		}
		
		$suggested_term = $this->checkterm_in_soz($term,'X',$lang, $this->get_store());
		
		if ($suggested_term != '') { // let's get the terms URI
			$query = <<<EOQ
				prefix skos:  <http://www.w3.org/2004/02/skos/core#>
				select ?uri where { 
						 { ?uri skos:prefLabel '$term' . }
						 UNION
						 { ?uri skos:altLabel '$term' . }
					}
EOQ;

			$store = $this->get_store();
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
  * Formats $term as in SOZ, first letter capital.
	*/
	protected function formatAsInThesaurus($term) {
		return ucfirst(strtolower($term));
	}

  
  
  
	/**
	 * Checks if $term is contained as a label in the SKOS ontology,
	 * returns a suggested term or ''.
	 */
	protected function checkterm_in_soz($term, $SearchType, $lang, $store) {			
//		if ($this->getSrcDebug()) {	
//			print "<br>STORE: <br>";
//			var_dump($store);
//		}
		
	
				$QUERY = <<<EOQ
					prefix skosxl:  <http://www.w3.org/2008/05/skos-xl#>
					select ?d2 where { 
							?d2 skosxl:literalForm '$term' .
					}
EOQ;
				
		
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
	
} // class SOZengine



?>