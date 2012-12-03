<?php

$filename = "sroot.php";
for ($x = 1, $updir = ''; $x <= 10; $x++, $updir .= "../") {
	if (file_exists("$updir$filename")) {
		include_once("$updir$filename");
		break;
	}
}

$filename = "SRCengineInterface.php";
for ($x = 1, $updir = ''; $x <= 10; $x++, $updir .= "../") {
	if (file_exists("$updir$filename")) {
		include_once("$updir$filename");
		break;
	}
}


/**
 * An abstract implementation of a SRC engine
 */
abstract class SRCengine implements SRCEngineInterface {
	private $term_separator;
	private $verbose;
	private $SRCDEBUG;
	private $wordbinding;
	private $maxresults;
	private $preprocessFunctionName;
	private $refineFunctionName;
	
	private $store;
	private $dbpedia_store;
	private $dbpedia_remote_store;
	private $zbwstore;
	private $zbwdbpedia_store;	
	private $sparql_limit_results;
	
	
	function SRCengine() 
	#########################
	{
		global $TERM_SEPARATOR;
		global $SPARQL_LIMIT_RESULTS;
		global $DEFAULT_MAX_REFINE_RESULTS;
		global $VERBOSE;
		global $SRCDEBUG;
		$this->term_separator = $TERM_SEPARATOR;
		$this->verbose = $VERBOSE;
		$this->srcdebug = $SRCDEBUG;
		$this->wordbinding='?';
		$this->maxresults=$DEFAULT_MAX_REFINE_RESULTS;
		$this->refineFunctionName='abstractRefineFuntion';
		$this->preprocessFunctionName='abstractPreprocessFunctionName';
		$this->store=NULL;
		$this->zbw_store = NULL;
		$this->zbwdbpedia_store = NULL;
		$this->sparql_limit_results=$SPARQL_LIMIT_RESULTS;
	} //constructor 

	
	
	protected function get_store()
	{
		return $this->store;
	}
	
	protected function set_store($val)
	{
		$this->store=$val;
	}
	
	protected function get_dbpedia_store()
	{
		return $this->dbpedia_store;
	}
	
	protected function set_dbpedia_store($val)
	{
		$this->dbpedia_store=$val;
	}
	
	protected function get_dbpedia_remote_store()
	{
		return $this->dbpedia_remote_store;
	}
	
	protected function set_dbpedia_remote_store($val)
	{
		$this->dbpedia_remote_store=$val;
	}
	
	protected function get_zbwdbpedia_store()
	{
		return $this->zbwdbpedia_store;
	}
	
	protected function set_zbwdbpedia_store($val)
	{
		$this->zbwdbpedia_store=$val;
	}
	
	protected function get_zbw_store()
	{
		//print "<br>get_zbw_store returning: ";var_dump($this->zbw_store);
		return $this->zbw_store;
	}
	
	protected function set_zbw_store($val)
	{
		$this->zbw_store=$val;
	}
	
	
	protected function setPreprocessFunctionName($name)
	{
		$this->preprocessFunctionName = $name;
	}

	protected function setRefineFunctionName($name)
	{
		$this->refineFunctionName = $name;
	}
	
	protected function setSrcDebug($val)
	{
		$this->srcdebug = $val;
	}

	protected function getSrcDebug()
	{
		return $this->srcdebug;
	}
	
	protected function setVerbose($val)
	{
		$this->verbose = $val;
	}
		
	protected function getVerbose()
	{
		return $this->verbose;
	}
	
	protected function setWordbinding($val)
	{
		$this->wordbinding = $val;
	}
	
	protected function getWordbinding()
	{
		return $this->wordbinding;
	}
	
	
	protected function set_sparql_limit_results($val)
	{
		$this->sparql_limit_results=$val;
	}
	
	protected function get_sparql_limit_results()
	{
		return $this->sparql_limit_results;
	}
	
	protected function set_term_separator($val)
	{
		$this->term_separator=$val;
	}
	
	protected function get_term_separator()
	{
		return $this->term_separator;
	}
	
	private function cleanup_viki_tokens($tokens) {
		$tokens = str_replace(',_',' ',$tokens); //Speciality of dbpedia
		$tokens = str_replace('_',' ',$tokens);
		$tokens = str_replace('Wikipedia:','',$tokens);
		$tokens = str_replace("%2C",'',$tokens);
		$tokens = str_replace("%2C",'',$tokens);
		$tokens = str_replace("%28",'(',$tokens);
		$tokens = str_replace("%27",' ',$tokens);
		$tokens = str_replace("%29",')',$tokens);
		$tokens = str_replace("  ",' ',$tokens);
		
		$tokens = cleanup_stopwords_str($tokens);
		
		return $tokens;
	}
	
	/**
	 * Returns a vector holding:
	 * 1. A vector of compound words if multiple possibilities can be found.
	 * 2. A vector of the Base64 encoded URI of the compound words.
	 * 
	 * Compounds are verified to be present in the SRC ontology.
	 * 
	 * NB. It starts with "a, b, c" in $terms and returns an array of candidate
	 * compounds "abc, ab, ac, bc".
	 * NB1. If $wordbinding is 'STW' then compound terms are separated by comma
	 * (Knowledge management, Engine), if it is 'DBP' instead, compounds terms
	 * are separated by underscore (Knowledge_management Engine)
	 */
	protected function get_english_candidate_compounds($terms) {
		global $VERBOSE, $SRCDEBUG;
		global $TERM_SEPARATOR;
		
		$tokenizationResults = $this->tokenizeCandidateCompounds(preg_replace('/\s+/', ' ', trim($terms)));
		$usercompounds = $this->flattenCompoundTerms($tokenizationResults['usercompounds']);
		$candidatecompounds = $this->flattenCompoundTerms($tokenizationResults['candidatecompounds']);
		$singletons = $this->flattenCompoundTerms($tokenizationResults['singletons']);

		// Now check each compound against the ontology of the SRC engine.
		$validatedCompounds = array();
		
		foreach(array_merge($usercompounds, $candidatecompounds) as $compound) {
			if ($termValidation = $this->validateTermInOntology($compound)) {
				$validatedCompounds[$termValidation[1]] = $termValidation[0];
			}
		}
		
		// From the list of singletons, keep only those not present in any
		// validated compound word.
		$validatedSingletons = $this->singletonsNotInCompounds($singletons, array_values($validatedCompounds));
		
		// Now check validated singletons against the ontology of the SRC engine.
		foreach($validatedSingletons as $validSingleton) {
			if ($termValidation = $this->validateTermInOntology($validSingleton)) {
				$validatedCompounds[$termValidation[1]] = $termValidation[0];
			}
		}
		
		// Implode array into two strings, one with the keys (URI) and another one with
		// the terms (items are comma separated).
		$allTermLabels = '';
		$allTermUris = '';
		foreach ($validatedCompounds as $uri => $label) {
			$allTermUris .= $uri . $TERM_SEPARATOR;
			$allTermLabels .= $this->code_binding(clean_puntuation($label)) . $TERM_SEPARATOR;
		}
		
		$allTermLabels = cleanup_commas($allTermLabels);
		$allTermUris = cleanup_commas($allTermUris);
		
		return array($allTermLabels, $allTermUris);
	}
	
	/**
	 * Extracts the terms that are already compounds in the list and puts them on the
	 * compound object. Returns an assosiative array with keys 'candidatecompounds',
	 * 'usercompounds', 'singletons', which values are arrays of arrays of words. Arrays
	 * of words should correspond to the words composing a compound term, and in the case
	 * of 'singletons' they should have only one string.
	 */
	private function tokenizeCandidateCompounds($terms) {
		$result = array();
		
		// if compounds were already delimited
		$chunks = explode(',', $terms);
		
		//If no comma => one element => then explode by space	
		
		if (count($chunks)==1 && count(explode(' ',trim($chunk)))>1)
			$chunks = explode(' ',$terms); // find each compound input (a b c d)
		
		
		//Extract the last chunk to the $restterms if they contain a blank (=2 or more words)
		if (preg_match('/ /',trim($chunks[count($chunks) - 1]))) {
			if ($SRCDEBUG) print "<br>pop last chunks because containts blank = (".$chunks[count($chunks) - 1].") words";
			$restterms = trim_array(explode(' ',array_pop ($chunks)));
		}
		
		
		
		//enumerate the words
		for($i=0;$i<count($chunks);$i++)
			$numberof[$chunks[$i]]=$i;
		
		foreach($chunks as $chunk)
		{
			//If there is a blank (severalwords) or just one word:
			if (preg_match('/ /',trim($chunk)) || count(explode(' ',trim($chunk)))==1)
				$usercompounds[]=explode(' ',$chunk);
			else 
				$restterms[]=$chunk;
		}
		
		//choose only those terms which are subsequent 
		//(have a subsequent number)
		//put other terms alone
		if (count($restterms)==1)
			$candidatecompounds[]=$restterms[0];
		
		else
		{
			$candidatecompounds=gen_sequences($restterms);
		}
		
		$usercompounds = trim_array($usercompounds);
		
		
		// Separate singletons from compounds
		if (count($usercompounds))
		foreach($usercompounds as $uc)
			if (is_singleton($uc))
				$singletons[]=$uc;
			else
				$real_usercompounds[]=$uc;
		
		if (count($candidatecompounds))
				foreach($candidatecompounds as $cc)
			if (is_singleton($cc))
				$singletons[]=$cc;
			else
				$real_candidatecompounds[]=$cc;
				
		$result['candidatecompounds']=trim_array($real_candidatecompounds);
		$result['usercompounds']=($real_usercompounds);
		$result['singletons']=($singletons);
		
		return $result;
	}
	
	/**
	 * Takes an array of arrays of strings and returns an
	 * array of strings. The strings of the second array are
	 * simply an implosion of the arrays of strings of the
	 * given array.
	 */
	private function flattenCompoundTerms($arrayOfArraysOfString) {
		$arrayOfStrings = array();
		
		if (is_array($arrayOfArraysOfString)) {
			foreach ($arrayOfArraysOfString as $arrayOfWords) {
				$compoundTerm = implode(' ', $arrayOfWords);
				$arrayOfStrings[] = $this->formatAsInSTW($compoundTerm);
			}
		}
		
		return $arrayOfStrings;
	}

	/**
	 * Formats $term as in STW, first letter capital.
	 */
	protected function formatAsInSTW($term) {
		return ucfirst(strtolower($term));
	}
	
	/**
	 * Checks the $singletons array for words that are not found in the compound
	 * terms in the two other arrays.
	 */
	private function singletonsNotInCompounds($singletons, $validatedCompounds) {
		$validatedSingletons = array();
		
		if (count($singletons)) {
			foreach($singletons as $singleton) {
				$found = false;
					
				foreach($validatedCompounds as $compound) {
					if (preg_match("/$singleton/i", $compound)) {
						$found = true;
						break;
					}
				}
					
				if (!$found) {
					$validatedSingletons[] = $singleton;
				}
			}
		}
		
		return $validatedSingletons;
	}
	
	private function code_binding($terms) {
		switch ($this->getWordbinding()) {
			case 'DBP':
				$res = str_replace(' ', '_', $terms);
				break;
			case 'STW':
			default:
				$res = $terms;
				break;
		}
		
		return trim($res);
	}
	
	protected function myFNameDebug($fun, $file, $line) { 
		return "(" . get_class($this) . "->$fun in $file:$line)"; 
	}
	
	
	/**
	 * Called by webStart() to test the responsiveness of the SRC
	 * data source. Returns a XML valid string, formatted as either :
	 * - <user>UserID</user>
	 * - <error>Error message (timeout/unavailable)</error>
	 * 
	 * @param unknown_type $user
	 */
	protected abstract function testDataSourceResponsiveness($user);
	
	/**
	 * 
	 * @param unknown_type $terms
	 * @param unknown_type $wordbinding
	 * @param unknown_type $lang
	 */
	protected abstract function preprocess_refine($terms,$wordbinding,$lang);
	
	protected abstract function refine_method($term,$action,$lang);
	
	/**
	 * Validates a term against the ontology used by the SRC engine
	 * 
	 * @return an array (label, Base64 encoded URI) of a matching concept in the ontology
	 */
	protected abstract function validateTermInOntology($term, $lang);
	
	
	private function refine($action, $text, $q, $m, $lang)
	##################################
	# 
	# Refines all relevant token in text using $this->refineFunctionName
	# To be set on init by the class involved
	# returns each token the related term
	#
	{	/*
		returns a list of ranked terms AND the same list as a base64encoded comma separated terms
		*/
		global $TERM_SEPARATOR;
		global $VERBOSE;
		global $SRCDEBUG;
		if ($this->srcdebug) print "<br>refine {$this->refineFunctionName} ($action, $text, $q, $m, $lang) ...";
		$ok=true;
		$TERMS_RAW=array();
		$words=explode($TERM_SEPARATOR,$text);
		if ($ok)
		{
			foreach($words as $word)
			{
				if (trim($word))
				{
					list($terms,$descriptors) = 
								$this->refine_method( trim($word),$action,$lang );
					if ($this->srcdebug) {
						print "<hr>refine_method returns: ".count($terms)." terms <hr>";
						if (count($terms))
						foreach($terms as $term=>$RANK)
							print "<br>$action: ".$term." ($RANK)";
						print "<br>";
					}
					//add singlewise! 
					if (count($terms))
					{
						foreach($terms as $term=>$RANK)
						{
							if (count($descriptors))
								$TERMS_RAW{$term}=$descriptors{$term}; 
							
							if ($this->srcdebug) print "<br>PROCESSING ($term)";
							$patternterm=str_replace('/','\/',$term);
							if (!preg_match("/$patternterm/",$refined_terms) 
							 && !preg_match("/$patternterm/",$text) 
							 && !preg_match("/$patternterm/",$q))
							{
								if ($refined_terms) $refined_terms.="$TERM_SEPARATOR\n"; 
									$refined_terms.=trim($term);
								$CANDIDATES{$term}=$RANK;
								if ($this->srcdebug) print "<br> Adding <b>$term</b> to candidates";
							}
							else if ($this->srcdebug) print "<br> DISCARDING <b>$term</b> from candidates";
							
						} //foreach
					} // terms da
				} // word nichtleer
			} // foreach word
	
			if ($this->srcdebug) print "<br> ".count($CANDIDATES)." CANDIDATES ($m):";
			
			
			if(count($CANDIDATES))
			{
				arsort($CANDIDATES); // sortiere nach Values - top value first
				if ($m) 
					array_splice($CANDIDATES, $m); // behalte die ersten $m insgesamt
				else
						if ($this->srcdebug) print "<br> !!! m is zero ??? <br>";
				
				$refined_terms=''; //reset
				$refined_terms_raw='';
				foreach($CANDIDATES as $term=>$RANK)
				{
					$nextterm=trim(cleanup_stopwords_str($term));
					if ($nextterm) // term is non stopword
					{
						if ($this->getWordbinding() == 'STW')
						{
							//Construct a sequence of terms separated by $TERM_SEPARATOR
							if ($refined_terms) $refined_terms.="$TERM_SEPARATOR\n"; 
								$refined_terms.=$nextterm;
							// construct a sequence of base64coded terms separated by $TERM_SEPARATOR
							if ($refined_terms_raw) $refined_terms_raw.="$TERM_SEPARATOR\n"; 
								$refined_terms_raw.=base64_encode($TERMS_RAW{$term});							
						}
						else if ($this->getWordbinding() == 'DBP')
						{
						//Construct a sequence of terms separated by $TERM_SEPARATOR
						if ($refined_terms<>'') $refined_terms.="$TERM_SEPARATOR\n"; 
							$refined_terms .= $this->cleanup_viki_tokens($nextterm);
						// construct a sequence of base64coded terms separated by $TERM_SEPARATOR
						if ($refined_terms_raw<>'') $refined_terms_raw.="$TERM_SEPARATOR\n"; 
							$refined_terms_raw.=base64_encode($TERMS_RAW{$term});
						}
					}
				}
			}
		} // ok

		
		return new SRCEngineResult(trim($refined_terms), trim($refined_terms_raw));
	} // refine
	
  
  /**
	 *
	 * Tunes the ontolgy on term in $v and construct a response to 
   * a refinement. Use a cache technique for equal requests.
	 * @param 
	 */
	public function webRefine($sid, $qb64, $vb64, $w, $lang, $maxdur, $c, $cid, $action) {
		global $DEFAULT_MAX_REFINE_RESULTS;
    global $BASECLASSNAME;
		$q=base64_decode($qb64);
    $v=base64_decode($vb64);
    
    $cache_id="$BASECLASSNAME-$action-$lang-$q/$v";
    
    $xmlCached_src_content = get_cached_src_response($cache_id);
    if (!src_cached_content_quality_control($xmlCached_src_content))
    { // ask service and rebuild cache

      if ($this->srcdebug) {
        print "sid=($sid) q=($q) v=(".($v).") w=($w) lang=($lang) action=($action)";
      }

      switch($action) {
        case 'pre':
          $RESULTS_P = $this->preprocess_refine(($v),$this->getWordbinding(),$lang);

          $srv = base64_encode($RESULTS_P->results);			
          $SRV_DATA = "<![CDATA[ $srv ]]>";

          $ok = $RESULTS_P->ok;
          $explanation = $RESULTS_P->explanation;
          break;

        case 'preall':
          $RESULTS_P = $this->preprocess_refine(($v), $this->getWordbinding(), $lang);

          $RESULTS_B = $this->refine('broader', $RESULTS_P->results, ($q), $this->maxresults, $lang);
          $RESULTS_N = $this->refine('narrower', $RESULTS_P->results, ($q), $this->maxresults, $lang);
          $RESULTS_R = $this->refine('related', $RESULTS_P->results, ($q), $this->maxresults, $lang);

          // Needs to be cleaned because they're obtained directly from
          // $this->get_english_candidate_compounds
          $srv_p = base64_encode($this->cleanup_viki_tokens($RESULTS_P->results));
          $srv_p_raw = base64_encode($RESULTS_P->results_raw);

          $srv_b = base64_encode($RESULTS_B->results);
          $srv_b_raw = base64_encode($RESULTS_B->results_raw);
          $srv_n = base64_encode($RESULTS_N->results);
          $srv_n_raw = base64_encode($RESULTS_N->results_raw);
          $srv_r = base64_encode($RESULTS_R->results);
          $srv_r_raw = base64_encode($RESULTS_R->results_raw);

          $SRV_DATA = <<<SRV
            <pre><![CDATA[ $srv_p ]]></pre>
            <pre_raw><![CDATA[ $srv_p_raw ]]></pre_raw>
            <broader><![CDATA[ $srv_b ]]></broader>
            <broader_raw><![CDATA[ $srv_b_raw ]]></broader_raw>
            <narrower><![CDATA[ $srv_n ]]></narrower>
            <narrower_raw><![CDATA[ $srv_n_raw ]]></narrower_raw>
            <related><![CDATA[ $srv_r ]]></related>
            <related_raw><![CDATA[ $srv_r_raw ]]></related_raw>
SRV;

          $ok = $RESULTS_P->ok;
          $explanation = $RESULTS_P->explanation;
          break;
      default:
        //Search for refinement
        set_time_limit ( $MAX_SRC_EXEC_TIME_LIMIT );
        if (!$lang) $lang=$_REQUEST['l'];
        $RESULTS_R=$this->refine( $action, ($v),($q),$DEFAULT_MAX_REFINE_RESULTS, $lang);
        $srv=base64_encode( htmlentities( $RESULTS_R->results ) );
        $srv_raw=base64_encode( htmlentities( $RESULTS_R->results_raw ) );
        $SRV_DATA= "<![CDATA[  $srv   ]]>";
        $SRV_DATA_RAW= "<![CDATA[  $srv_raw   ]]>";

        $ok=$RESULTS_P->ok;
        $explanation=$RESULTS_R->explanation;
      }

      $xml_src_content = <<<EOF
<refine>
  <cid>$cid</cid>
  <c>$c</c>
  <v><![CDATA[ $v ]]></v>  
  <l>$lang</l>
  <w>$w</w>
  <q>$q</q>
  <sid>$sid</sid>
  <srv>$SRV_DATA</srv>  
  <srv_raw>$SRV_DATA_RAW</srv_raw>
  <maxDur>$maxdur</maxDur>
  <rts>1255287670</rts>
  <cdur>3030</cdur>
  <action>$action</action>
</refine>
EOF;
      cache_src_response($cache_id,$xml_src_content);
    } 
    else 
      $xml_src_content = $xmlCached_src_content;
		return $xml_src_content;			
	}
	
	/**
	 * 
	 */
	public function webStart($user) {
		$answer = $this->testDataSourceResponsiveness($user);
		$xml = "<src_init_response>$answer</src_init_response>";

		return $xml;		
	}

} // class SRCengine


/*
 * Returns true if the content is a valid nonempty content
 * false otherwise 
 */
function src_cached_content_quality_control($SRC_XML_CACHE_CONTENT)
{
  //Has the answer at least data inside one of (broader/related/narrower)?
  $need_src_log=true;

  $ok = (trim($SRC_XML_CACHE_CONTENT));
  
  if ($ok) //Test any of the skos objects nonempty
  {
    if ($need_src_log)
    {
      global $SOLR_RODIN_LOCKDIR;
      $LOGfilename="$SOLR_RODIN_LOCKDIR/SRC_cache.LOG.txt";
      $log=fopen($LOGfilename,"a");
      $now=date("d.m.Y H:i:s").'.'.substr(microtime(false),2,6);
      fwrite($log, "\n\n$now src_cached_content_quality_control ($SRC_XML_CACHE_CONTENT)");
     }
     
    $pattern_empty_broader  ="/\<broader\>\<\!\[CDATA\[  \]\]\>\<\/broader\>/";
    $pattern_empty_narrower ="/\<narrower\>\<\!\[CDATA\[  \]\]\>\<\/narrower\>/";
    $pattern_empty_related  ="/\<related\>\<\!\[CDATA\[  \]\]\>\<\/related\>/";

    if (preg_match($pattern_empty_broader,$SRC_XML_CACHE_CONTENT)
      &&preg_match($pattern_empty_narrower,$SRC_XML_CACHE_CONTENT)
      &&preg_match($pattern_empty_related,$SRC_XML_CACHE_CONTENT)
        )
    {
      if ($need_src_log) fwrite($log, "\n\n$now MATCHED empty content in \n\n$SRC_XML_CACHE_CONTENT");
      $ok=false;
    }
    else
    {
      if ($need_src_log)
        fwrite($log, "\n\n$now NOT MATCHED empty content in \n\n$SRC_XML_CACHE_CONTENT");
    }

  } //Test any of the skos objects nonempty
  
  return $ok; // debug
}







/**
 * Represents a result set returned by a SRC for a single action
 */
class SRCEngineResult {
	public $ok;
	public $results;
	public $results_raw;
	public $explanation;
	
	function SRCEngineResult($results, $results_raw = null, $explanation = 'Coming soon', $ok = true) {
		$this->ok = false;
		$this->results = $results;
		$this->results_raw = $results_raw;
		$this->explanation = $explanation;
	}	
}

?>