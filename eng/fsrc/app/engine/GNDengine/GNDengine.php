<?php

	# GND Engine abstract
	#
	# Dec 2012 HEG
	# fabio.ricci@ggaweb.ch  
	# Tel.: +41-76-5281961
  # http://www.dnb.de/EN/Service/DigitaleDienste/LinkedData/linkeddata_node.html
  # 


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
	




abstract class GNDengine extends SRCengine
{

	
	function __construct() 
	#########################
	{
   	parent::__construct();
		$this->setStores();
    $this->currentclassname='GNDengine';
	
	} //constructor 
	
	
	protected function setStores()
	{
		global $ARCCONFIG;
		$localArcConfig = $ARCCONFIG;
		$this->store = NULL;
		$localArcConfig['store_name'] = 'dnb_gnd';
    $ARCCONFIG = $GLOBALS['ARCCONFIG'];
   
		$this->set_store(ARC2::getStore($localArcConfig));

		if (!$this->get_store()->isSetUp()) {
	  		$this->get_store()->setUp();
		}
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
	protected function preprocess_refine($terms, $wordbinding='GND', $lang='de') {
		if ($this->getSrcDebug()) {
			print "<br>GNDengine->preprocess_refine($terms, $wordbinding, $lang);";
		}
		// TODO Implement ZBW_cleanup() and replace
		// $terms = ZBW_dirtydown($terms);
		
		list($descriptor, $label) = $this->extractDescriptor($terms);

		if ($descriptor) {
			// Then $terms are already concept URIs
			$preResults = new SRCEngineResult($terms);
		} else {
			//Since in GND we do have as search terms such terms with commas,
			//we clean here nothing
			$preResults = new SRCEngineResult($terms);
		}
			
		return $preResults;
	}
		
	
	/**
	 * Checks if the $term matches : 
	 * - http://data.bnf.fr/ark:/12148/70244
	 * 
	 * Extracts the node ID (eg. 70244)
	 * from the $term and returns them in a list.
	 * 
	 * @param string $term
	 */
	protected function extractDescriptor($term) {
		$ok= false;
		$node = $label = 0;
    
		if ($this->getSrcDebug()) {
			print "<br><b>GNDengine->extractDescriptor($term):";
		}
		
		$descriptorBase = "http://d-nb.info/gnd/";
    $regExpDescriptor = "/^http:\/\/d-nb.info\/gnd\/(.*)$/";
		
    if (preg_match($regExpDescriptor, $term, $match)) {
			$ok = true;
			$node = $descriptorBase . $match[1];
			$label = $match[3];
			
			if($this->getSrcDebug()) {
				print "<br>Matched $regExpDescriptor in ($term), node=($node) label=($label)";
			}
		} else if ($this->getSrcDebug()) {
			print "<br><b>NO match </b> ($term) as GND descriptor";
		}
		
		return array($node,$label);		
	}
	
	
	
	
	protected function validateTermInOntology($term, $lang = 'en') {
		if ($this->getSrcDebug()) {
			print "<br>GND_validateTermInOntology($term,$lang='en') ...";
		}
		
		$suggested_term = $this->checkterm_in_gnd($term,'X',$lang, $this->get_store());
		
		return ($suggested_term!='');
	}
	
  
  /**
  	* Formats $term as in GND, We do not want first letter capital
	 	* and we need to preserve the term AS IS (at least inside sparql)
	*/
	protected function formatAsInThesaurus($term) {
		return $term;
	}

  
  
  
	/**
	 * Checks if $term is contained as a label in the SKOS ontology,
	 * returns a suggested term or ''.
	 */
	protected function checkterm_in_gnd($term, $SearchType, $lang, $store) {			
//		if ($this->getSrcDebug()) {	
//			print "<br>STORE: <br>";
//			var_dump($store);
//		}
		
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
			print "<br><br>checkterm_in_gnd($exactSearch): $QUERY <br>";
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
			print "<br><br>checkterm_in_gnd($exactSearch): returning found=$found<br>";
		}
		
		if ($found)	{
			$suggested_term = $term;
		} else {
			$suggested_term = '';
		}
		
		return $suggested_term;
	}
	
} // class GNDengine



?>