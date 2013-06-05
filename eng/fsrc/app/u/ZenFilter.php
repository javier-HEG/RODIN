<?php

include_once '../sroot.php';
include_once '../../../../gen/u/arc/ARC2.php';
include_once '../../../../gen/u/simplehtmldom/simple_html_dom.php';
include_once 'FRIutilities.php';
include_once 'FRIdbUtilities.php';

class ZenFilter {
	private $preferredNumberOfTokens;
	private $queryTokens;
	private $textToFilterTokens;
	private $textToFilter;
	private $lastMethodUsed;
	private $language;
	
	private $dbPediaStore;
	
	private $replaceTextWithLabel = true;

	function __construct($textToFilter, $query, $lang) {
		global $ARCCONFIG;
		global $DBPEDIA_SPARQL_ENDPOINT;

		$this->queryTokens = cleanup_stopwords(tokenize($query));
		$this->textToFilter = $textToFilter;
		
		$this->textToFilterTokens = cleanup_stopwords(tokenize($textToFilter));
		$this->preferredNumberOfTokens = 10;
		
		if ($lang != '') {
			$this->language = $lang;
		} else { 
			$this->language = 'en';
		}

		if ($USE_LOCAL_DBPEDIA) {
			$dbPediaARCConfig = $ARCCONFIG;
			$dbPediaARCConfig['db_name'] = $LOCAL_DBPEDIA_DB_NAME;
			$dbPediaARCConfig['store_name'] = $LOCAL_DBPEDIA_ARC_NAME;
			
			$this->dbPediaStore = ARC2::getStore($dbPediaARCConfig);
			if (!$this->dbPediaStore->isSetUp()) {
				$this->dbPediaStore->setUp();
			}
		} else {
			global $PROXY_NAME, $PROXY_PORT;
			$dbPediaARCConfig = $ARCCONFIG;
			if ($PROXY_NAME != '') {
				$dbPediaARCConfig['proxy_host'] = $PROXY_NAME;
				$dbPediaARCConfig['proxy_port'] = $PROXY_PORT;
			}

			$dbPediaARCConfig['remote_store_endpoint'] = $DBPEDIA_SPARQL_ENDPOINT;
			$this->dbPediaStore = ARC2::getRemoteStore($dbPediaARCConfig);
		}
		
		$this->lastMethodUsed = "";
	}

	/**
	 *
	 * The public function to be called by the users of the class. It makes a call
	 * to DBPedia's Spotlight service hoping that there is at least one result. In
	 * the case where there is not any, the call will be cascadated to another
	 * function.
	 */
	function getFilteredText() {
		// We first try to get Spotlight terms
		$spotlightFilteredTerms = $this->getSpotlightedTerms(); 

		if (count($spotlightFilteredTerms) > 0) {
			arsort($spotlightFilteredTerms);
			return array_slice($spotlightFilteredTerms, 0, $this->preferredNumberOfTokens);
		} else {
			// Should Spotlight return nothing, we try the "related-by-dc:subject" method
			$subjectRelatedTerms = $this->getTermsRelatedToQueryBySubjectCategory();
			
			if (count($subjectRelatedTerms) > 0) {
				arsort($subjectRelatedTerms);
				return array_slice($subjectRelatedTerms, 0, $this->preferredNumberOfTokens);
			} else {
				// Should this not work, we return the preferred number of random tokens
				$mostCommonInTextTerms = $this->getMostCommonTextToFilterTerms();
				arsort($mostCommonInTextTerms);
				
				$termsToReturn = array();
				
				foreach ($mostCommonInTextTerms as $word => $freq) {
					$allSuggested = wikipedia_disambiguate($word, $this->language);
					
					//if (array_search($word, $allSuggested)) {
					if (count($allSuggested) > 0) {
						foreach ($allSuggested as $sug) {
							$index = strpos(strtolower($sug), strtolower($word));
							
							if ($index !== false) {
								$termsToReturn[$word] = $freq;
								break;
							}
						}
					}
					
					if (count($termsToReturn) > $this->preferredNumberOfTokens) {
						break;
					}
				}
				
				return $termsToReturn;
			}
		}
	}

	/**
	 * This function calls DBPedia's Spotlight service on $textTofilter.
	 *
	 * @return An associative array whose keys are the labels of the
	 * concepts found by the service and the values are a score by
	 * which we consider it is advisable to rank them.
	 */
	private function getSpotlightedTerms() {
		$serviceURL = 'http://spotlight.dbpedia.org/rest/annotate?';
		
		$fields = array(
			'disambiguator'=>'Document',
			'confidence'=>'0.2',
			'support'=>'20',
			'text'=>$this->textToFilter);
		$fieldsString = str_replace(
			array('+', '%7E', '&amp;'), 
			array('%20', '~', '&'),
			http_build_query($fields));

		$options = array(
			CURLOPT_HTTPHEADER => array('Accept:text/xml'),
			CURLOPT_TIMEOUT => $FSRC_CURL_TIMEOUT_SEC,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $fieldsString);

		$result = parametrizable_curl($serviceURL, array(), $options);
		
		$scoredLabels = array();

		if ($result) {
			$xml = str_get_html($result);
				
			$annotation = $xml->getElementByTagName('annotation');
			$resources = $annotation->find('resources resource');

			foreach ($resources as $resource) {
				$surfaceForm = $resource->getAttribute('surfaceform');
				$conceptURI = $resource->getAttribute('uri');
				$label = $this->getDBPediaLabel($conceptURI, "en");

				if (trim($label) == '') {
					$label = ucfirst($surfaceForm);
				}
				
			 	// use either the similarity or the support to rank
				// the concepts returned by spotlight
				//$score = round($resource->getAttribute('similarityscore')*100);
				$score = $resource->getAttribute('support');
					
				if (array_key_exists($label, $scoredLabels)) {
					$score = max($score, $scoredLabels[$label]);
					$scoredLabels[$label] = $score;
				} else {
					$scoredLabels[$label] = $score;
				}
			}
		}
			
		$this->lastMethodUsed = "Spotlight";
		
		return $scoredLabels;
	}
	
	/**
	 *
	 * related to the query terms by dcterms:subject. It is to be called by the
	 * This function computes a set of terms found on the result text that are
	 * function contacting the spotlight service.
	 */
	private function getTermsRelatedToQueryBySubjectCategory() {
		global $DBPEDIA_PREFIX;

		$queryToken = ucfirst($this->queryTokens[0]);

		$disambiguation = "?general dbpedia-owl:wikiPageDisambiguates ?concept";
	
		$unionQuery = "{ ?concept rdfs:label \"$queryToken\"@en . } UNION { ?general rdfs:label \"$queryToken\"@en .
		$disambiguation . } .";

		if (count($this->queryTokens) > 1) {
			for ($i=1; $i<count($this->queryTokens); $i++) {
				$queryToken = ucfirst($this->queryTokens[$i]);

				$unionQuery = "{ { ?concept rdfs:label \"$queryToken\"@en . } UNION { ?general rdfs:label \"$queryToken\"@en .
				$disambiguation . } . } UNION { $unionQuery } .";
			}
		}
		
		$query = $DBPEDIA_PREFIX;
		$query .= <<<EOF
PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX dbpprop: <http://dbpedia.org/property/>
SELECT ?label WHERE {
	$unionQuery
	?concept dcterms:subject ?category .
	?related dcterms:subject ?category .
	?related rdfs:label ?label .
	OPTIONAL { ?concept rdf:type ?type . } .
	FILTER ( langMatches( lang(?label), "$this->language" ) ) .
}
EOF;
		// Could add "FILTER ( !bound(?type) ) ."
		// Could add the "ORDER BY ?label" but it often gets timed out

		$relatedTermLabels = array();

		$rows = $this->dbPediaStore->query($query, 'rows');

//		if ($errors = $this->dbPediaStore->getErrors()) {
//			foreach($errors as $error) { ... }
//		}

		foreach($rows as $row) {
			$label = $row['label'];
			$label = trim(preg_replace("/\(.*\)/", "", $label));

			$relatedTermLabels[] = $label;
		}
		
		$subjectRelatedTerms = array_count_values($relatedTermLabels);
		
		// Keep only those terms that share at least a word with the text.
		$subjectRelatedTermKeys = array_keys($subjectRelatedTerms);
		
		$subjectRelatedTermsInText = array();
		foreach ($subjectRelatedTermKeys as $termKey) {
			foreach ($this->textToFilterTokens as $textToken) {
				$pos = stripos($termKey, $textToken);
				if(!($pos === false))
				{
					$subjectRelatedTermsInText[] = $termKey;
					break;
				}
			}
		}
			
		$countValues = array();
		foreach ($subjectRelatedTermsInText as $term) 
		{
				$countValues[$term] = $subjectRelatedTerms[$term];
		}
		
		$this->lastMethodUsed = "Sparql";
		
		return $countValues;
	}

	private function getMostCommonTextToFilterTerms() {
		$countValues = array_count_values($this->textToFilterTokens);

		$this->lastMethodUsed = "MostCommon";
		
		return $countValues;
	}
	
	/**
	 *
	 * Gets the concept label for the concept referred in the given URI.
	 * @param String $uri the URI of the concept whose label we should return.
	 * @param unknown_type $language the language in which the label shouls be
	 * returned.
	 */
	private function getDBPediaLabel($uri, $language = "en") {
		global $DBPEDIA_PREFIX;

		$query = $DBPEDIA_PREFIX;
		$query .= <<<EOF
SELECT ?label WHERE {
	<$uri> rdfs:label ?label .
	FILTER ( langMatches( lang(?label), "$language" ) ) .
}
EOF;

		$rows = $this->dbPediaStore->query($query, 'rows');

//		if ($errors = $this->dbPediaStore->getErrors()) {
//			foreach($errors as $error) { ... }
//		}

		if (count($rows) > 0) {
			$label = $rows[0]['label'];
			// Remove note withing brackets
			$label = trim(preg_replace("/\(.*\)/", "", $label));
			return $label;
		} else {
			return "";
		}
	}
	
	/**
	 * 
	 * A public function returning the name of the last method used to filter the
	 * the text. It should be used only after having called the getFilteredText()
	 * function.
	 */
	public function getLastMethodUsed() {
		return $this->lastMethodUsed;
	}
}

/**
 *
 * Utility funcion tokenizing text using spaces.
 * TODO Check if it can be replaced by another already implemented.
 * @param String $text
 */
function tokenize($text) {
	$tTokens = preg_split("/\W+/", strtolower($text));

	$tokens = array();
	foreach ($tTokens as $pToken) {
		$pToken = trim($pToken, ' ,');
		if ($pToken != '') {
			$tokens[] = ucfirst($pToken);
		}
	}

	return $tokens;
}

?>