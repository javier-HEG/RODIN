<?php

$filename = "sroot.php";
for ($x = 1, $updir = ''; $x <= 10; $x++, $updir .= "../") {
	if (file_exists("$updir$filename")) {
    $sroot_loaded=true;
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

//require_once "$DOCROOT$APPPATH/tests/Logger.php";


/**
 * An abstract implementation of a SRC engine
 */
abstract class SRCengine implements SRCEngineInterface {
	
  public  $currentclassname;
  private $term_separator;
	public $verbose;
	public $srcdebug;
	private $wordbinding;
	private $maxresults;
	private $preprocessFunctionName;
	private $refineFunctionName;
	
	private $store;
	private $dbpedia_remote_store;
	private $sparql_limit_results;
	public static $maxsuggestions = 500; // on autocomplete each SRC
	
	function SRCengine() 
	#########################
	{
		global $TERM_SEPARATOR;
		global $SPARQL_LIMIT_RESULTS;
		global $VERBOSE; 
		global $SRCDEBUG; 
		global $SOLRCLIENT;
		$this->term_separator = $TERM_SEPARATOR;
		$this->verbose = $VERBOSE;
		$this->srcdebug = $SRCDEBUG;
		$this->wordbinding='?';
		$this->maxresults = 10; //standard but superseeded by $m
		$this->refineFunctionName='abstractRefineFuntion';
		$this->preprocessFunctionName='abstractPreprocessFunctionName';
		$this->store=NULL;
		$this->zbw_store = NULL;
		$this->zbwdbpedia_store = NULL;
		$this->sparql_limit_results=$SPARQL_LIMIT_RESULTS;
    $this->currentclassname='SRCengine';
		
		$SOLRCLIENT=null; // very important on repeated internal calls (using the same)
		//print "<br>cons SRCengine executed";
	} //constructor 

	
	
	public function get_store()
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
  
  public function refine_skos_solr_available()
	{
    return false; // at this level. (overloading base class method)
	}
	
  public function refine_skosxl_solr_available()
  {
    return false; // at this level. (overloading base class method)
  }

  public function refine_gnd_solr_available()
  {
    return false; // at this level. (overloading base class method)
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
	 * Tunes the ontology on term in $v and construct a response to 
   * a refinement. Uses a cache technique for equal requests.
   * This function responds to an SRC web requests 
   * and caches the response (see root.php for cache expiry times)
   * It returns the response with a tag 'age_in_seconds' to inform
   * the user on the age of the data set.
	 * 
	 * @param 
	 */
	public function webRefine($sid, $qb64, $vb64, $w, $lang, $m, $sortrank='standard', $maxdur/*USED?*/, $c, $cid, $action, $reqClassName, $mode='web') 
	{
		if ($mode=='direct') $action='preall'; // forced
		$SHOULD_CACHE =( !strstr($mode,'autocomplete')
									&& !strstr($mode,'direct')
									); // cache only if not SOLR tecnology and never on autocomplete
		
		$SORT_FACETS_LEXICOGRAPHICALLY = (strstr($mode,'sortfacetslex'));
		
    $q=base64_decode($qb64);
    $v=base64_decode($vb64);
    $this->maxresults=$m; // fix it in object for every computation
    //LOG SRC TIME HERE:
    
    $cache_id="$reqClassName-$action-$lang-$q/$v";
    //Logger::logAction(25, array('from'=>'WebRefine','msg'=>'Started with cacheid:'.$cache_id));
    if ($SHOULD_CACHE)
		    list($xmlCached_src_content,
		            $creationtimestamp,
		            $age_in_sec,
		            $max_age_in_sec, // this is always set!! even if no data
		            $expiring_in_sec) = get_cached_src_response($cache_id);
     
    
    if (! $SHOULD_CACHE 
    ||  ! $this->src_cached_content_quality_control($xmlCached_src_content))
		{ // ask service and rebuild cache
       
      $timestamp=date("d.m.Y H:i:s");
      $timestamp0=time();
      $age_in_sec=0;
      $expiring_in_sec=$max_age_in_sec;
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
          if(trim($v)) // something to preprocess?
					{
	          $RESULTS_P = $this->preprocess_refine(($v), $this->getWordbinding(), $lang);
						$preprocessed=$RESULTS_P->results;
					}
					else // no $v given - use directly q
					{
						if ($this->srcdebug || $this->verbose) print "<br>SKIP PREPROCESSING, USE ($q)";
						$preprocessed=$q;
					}
					
          if ($this->refine_skos_solr_available()) {
              
              list($RESULTS_S,$RESULTS_B,$RESULTS_N,$RESULTS_R) 
                      = $this->refine_skos_solr( $preprocessed, 
                                                 ($q), 
                                                 $this->maxresults, 
                                                 $lang,
                                                 $sortrank,
                                                 $mode );
          } 
          else if ($this->refine_skosxl_solr_available()) {
              
              list($RESULTS_S,$RESULTS_B,$RESULTS_N,$RESULTS_R) 
                      = $this->refine_skosxl_solr( $preprocessed, 
	                                                 ($q), 
	                                                 $this->maxresults, 
	                                                 $lang,
	                                                 $sortrank,
	                                                 $mode );
          } 
          else if ($this->refine_gnd_solr_available()) {
              
              list($RESULTS_S,$RESULTS_B,$RESULTS_N,$RESULTS_R) 
                      = $this->refine_gnd_solr( 	$preprocessed, 
	                                                ($q), 
	                                                $this->maxresults, 
	                                                $lang,
	                                                $sortrank,
	                                                $mode );
          } 
          else
          {
          	$RESULTS_S = new SRCEngineResult(null,null,null.null);
            $RESULTS_B = $this->refine('broader', $preprocessed, ($q), $this->maxresults, $lang, $sortrank, $mode);
            $RESULTS_N = $this->refine('narrower', $preprocessed, ($q), $this->maxresults, $lang, $sortrank, $mode);
            $RESULTS_R = $this->refine('related', $preprocessed, ($q), $this->maxresults, $lang, $sortrank, $mode);
          }
          
					if ($this->verbose) {
						print "<hr>mode: $mode";
						print "<hr>SUGGESTIONS: <br>"; var_dump($RESULTS_S);
						print "<hr>";
						print "<hr>BROADERS: <br>"; var_dump($RESULTS_B);
						print "<hr>";
						print "<hr>NARROWERS: <br>"; var_dump($RESULTS_N);
						print "<hr>";
						print "<hr>RELATED: <br>"; var_dump($RESULTS_R);
						print "<hr>";
					}
         
				 
          $srv_p = base64_encode($this->cleanup_viki_tokens($RESULTS_P->results));
          $srv_p_raw = base64_encode($RESULTS_P->results_raw);

          $srv_b =      base64_encode($RESULTS_B->results);
          $srv_b_raw =  base64_encode($RESULTS_B->results_raw);
          $srv_b_root =              ($RESULTS_B->results_root);
          $srv_n =      base64_encode($RESULTS_N->results);
          $srv_n_raw =  base64_encode($RESULTS_N->results_raw);
          $srv_n_root =              ($RESULTS_N->results_root);
          $srv_r =      base64_encode($RESULTS_R->results);
          $srv_r_raw =  base64_encode($RESULTS_R->results_raw);
          $srv_r_root =              ($RESULTS_R->results_root);

          //Prepare XML response:
          $SRV_DATA = <<<SRV
            <pre><![CDATA[ $srv_p ]]></pre>
            <pre_raw><![CDATA[ $srv_p_raw ]]></pre_raw>
            <broader><![CDATA[ $srv_b ]]></broader>
            <broader_raw><![CDATA[ $srv_b_raw ]]></broader_raw>
            <broader_root><![CDATA[ $srv_b_root ]]></broader_root>
            <narrower><![CDATA[ $srv_n ]]></narrower>
            <narrower_raw><![CDATA[ $srv_n_raw ]]></narrower_raw>
            <narrower_root><![CDATA[ $srv_n_root ]]></narrower_root>
            <related><![CDATA[ $srv_r ]]></related>
            <related_raw><![CDATA[ $srv_r_raw ]]></related_raw>
            <related_root><![CDATA[ $srv_r_root ]]></related_root>
SRV;

          $ok = $RESULTS_P->ok;
          $explanation = $RESULTS_P->explanation;
          break;
      default:
        //Search for refinement
        set_time_limit ( $MAX_SRC_EXEC_TIME_LIMIT );
        if (!$lang) $lang=$_REQUEST['l'];
        $RESULTS_R=$this->refine( $action, ($v),($q),$this->maxresults, $lang);
        $srv=base64_encode( htmlentities( $RESULTS_R->results ) );
        $srv_raw=base64_encode( htmlentities( $RESULTS_R->results_raw ) );
        $SRV_DATA= "<![CDATA[  $srv   ]]>";
        $SRV_DATA_RAW= "<![CDATA[  $srv_raw   ]]>";

        $ok=$RESULTS_P->ok;
        $explanation=$RESULTS_R->explanation;
      }
      
      //In case of autocomplete we need 4 vectors
      //the succestions themselves, and in case of a match
      //even the SKOS neighbours

      
      
      
			if ($SHOULD_CACHE) //Linearize and cache
			{
	      $xml_src_content =<<< EOF
<refine>
<timestamp>$timestamp</timestamp>
<timestamp0>$timestamp0</timestamp0>
<age_in_sec>$age_in_sec</age_in_sec>
<max_age_in_sec>$max_age_in_sec</max_age_in_sec>
<expiring_in_sec>$expiring_in_sec</expiring_in_sec>
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
				$SRC_RESULT=$xml_src_content;
		    cache_src_response($cache_id,$xml_src_content);
			}
			else // Just give back the result object
			{
				if (! strstr($mode,'context'))
					$srv_b_root=$srv_n_root=$srv_r_root=array();
				$SRC_RESULT = new SRCEngineSKOSResult ($RESULTS_S, $RESULTS_B,$RESULTS_N,$RESULTS_R) ;
			}
    } // quality control of cache nok
    else 
    {// quality control of cache ok
      //Write age_in_sec into supplied data set:
     $xmlCached_src_content= xml_inject($xmlCached_src_content, 'age_in_sec', $age_in_sec);
     $xmlCached_src_content= xml_inject($xmlCached_src_content, 'max_age_in_sec', $max_age_in_sec);
     $xmlCached_src_content= xml_inject($xmlCached_src_content, 'expiring_in_sec', $expiring_in_sec);
     
     $xml_src_content = $xmlCached_src_content;
		 $SRC_RESULT = $xml_src_content;
    }
    if($this->srcdebug)
		{
			$SRC_RESULT = htmlentities(print_r($SRC_RESULT));
		}
    
    //LOG SRC TIME HERE:
    //Logger::logAction(25, array('from'=>'STWengine3->WebRefine','msg'=>'Delivered'));

    return $SRC_RESULT;			
	}
	
	/**
	 * 
	 */
	public function webStart($user) {
 
		$answer = $this->testDataSourceResponsiveness($user);
		$xml = "<src_init_response>$answer</src_init_response>";

		return $xml;		
	}


  
	

  
  
    /**
   * Sorts and cut labels ... according to $sortrank
   * Returns the descriptors base64 encoded
   * @param $CANDIDATE_LABELS
   * @param $CANDIDATE_DESCS - Descriptors
   * @param $m
   * @param $sortrank - standard or lexical
   */
  protected function cut_and_process_raw_results(&$CANDIDATE_LABELS,&$CANDIDATE_DESCS,$ROOTPATHS,$m,$sortrank)
 	#############################################################################
  {
    global $TERM_SEPARATOR;
    $ROOTPATHSelements=explode('|',$ROOTPATHS);
		
    if(count($CANDIDATE_LABELS))
    {
      // Create label-term map
      foreach($CANDIDATE_LABELS as $label=>$rank)
      {
        $TERMS_RAW{$label}=$CANDIDATE_DESCS[$i];
				$ROOTPATH_VALUE{$label}=$ROOTPATHSelements[$i];
				$i++;
      }

      // print "<br>cut_and_process_raw_results sort $sortrank";
				
      // START OF SORT RANK SECTION
      if ($sortrank=='standard') 
      {
      	#################################################################
        arsort($CANDIDATE_LABELS); // sort using values=ranks - top value first
        array_splice($CANDIDATE_LABELS, $m); // keep the first $m 
        #################################################################
     } // ($sortrank='standard') 
			elseif($sortrank=='lex') 
      {
      	#################################################################
        ksort($CANDIDATE_LABELS); // sort using values=ranks - top value first
        array_splice($CANDIDATE_LABELS, $m); // keep the first $m 
        #################################################################
     } // ($sortrank='standard') 
     
     // END OF SORT RANK SECTION
     
     //ALIGN ALL VECTORS AFTER SORTING ON THE BASIS OF the (number and sort of) $CANDIDATE_LABELS:
     $refined_terms=''; //reset
     $refined_terms_raw='';
     $root_paths='';
     $elemcount=0;
		 foreach($CANDIDATE_LABELS as $label=>$RANK)
     {
     	 if($this->srcdebug) {
         print "<hr>Considering candidate <b>$label</b> ...";
       } 
       
       //Construct a sequence of terms separated by $TERM_SEPARATOR
       if ($refined_terms) $refined_terms.="$TERM_SEPARATOR"; 
       $refined_terms.=$label;
       // construct a sequence of base64coded terms separated by $TERM_SEPARATOR
       if ($refined_terms_raw) $refined_terms_raw.="$TERM_SEPARATOR\n"; 
       $refined_terms_raw.=base64_encode($TERMS_RAW{$label});	 // encode its descriptor						

       if($root_paths) $root_paths.="|"; // Attention - variants are coded with |
			 
			 // print "<br>cut_and_process_raw_results ALIGNING ($label) -> ".$ROOTPATH_VALUE{$label};
			 
       $root_paths.=$ROOTPATH_VALUE{$label};	 // already encoded						
     }
   }
   
   if($this->srcdebug) {
         print "<br>process_raw_results() Delivering refined_terms_raw:<br>";
         var_dump($refined_terms_raw);
       } 
   
   return array($refined_terms,$refined_terms_raw,$root_paths);
 	} // cut_and_process_raw_results
  
	
  
  
  
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
		
    if ($SRCDEBUG) print "get_english_candidate_compounds";
		$tokenizationResults = $this->tokenizeCandidateCompounds(preg_replace('/\s+/', ' ', trim($terms)));
		$usercompounds = $this->flattenCompoundTerms($tokenizationResults['usercompounds']);
		$candidatecompounds = $this->flattenCompoundTerms($tokenizationResults['candidatecompounds']);
		$singletons = $this->flattenCompoundTerms($tokenizationResults['singletons']);

    if ($SRCDEBUG) 
    {
      print "\n<br>tokenizationResults: "; var_dump($tokenizationResults);
      print "\n<br>usercompounds: "; var_dump($usercompounds);
      print "\n<br>candidatecompounds: "; var_dump($candidatecompounds);
      print "\n<br>singletons: "; var_dump($singletons);
    }
    
    
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
		
    
    if ($SRCDEBUG) 
    {
      print "\n<br>validatedSingletons: "; var_dump($validatedSingletons);
    }
    
    
    
		// Now check validated singletons against the ontology of the SRC engine.
		foreach($validatedSingletons as $validSingleton) {
			if ($termValidation = $this->validateTermInOntology($validSingleton)) {
				$validatedCompounds[$termValidation[1]] = $termValidation[0];
			}
		}
		
    if ($SRCDEBUG) 
    {
      print "\n<br>validatedCompounds: "; var_dump($validatedCompounds);
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
				$arrayOfStrings[] = $this->formatAsInThesaurus($compoundTerm);
			}
		}
		
		return $arrayOfStrings;
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
	
	
	private function refine($action, $text, $q, $m, $lang, $sortrank='standard',$mode='web')
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

		//Do this for all but for RAMEAU
		//Since RAMEAU contains terms with commas "Delgado-Granado (1963), Affaire"
		//We do not segment processing on comma
		if(strstr($this->currentclassname,'RAMEAU'))
		{
			//Correct language (current recognizer is wrong)
			$lang = 'fr';
			$words = array( (($text)) );
		}
		else if(strstr($this->currentclassname,'LOC'))
		{
			//Correct language (current recognizer is wrong)
			$lang = 'en';
			$words = array( (($text)) );
		}
		else // split by comma:	
			$words=explode($TERM_SEPARATOR,$text);

		if ($this->srcdebug) {
			print "<br>SRCengine: Refining using text($text) and q($q) and m($m)"; 
			var_dump($words);
	 	}
		


		if ($ok)
		{
			foreach($words as $word)
			{
				if (trim($word))
				{
					list($terms,$descriptors) = 
								$this->refine_method( trim($word),$action,$lang );
					if ($this->srcdebug) {
						print "<hr>refine_method($word) returns: ".count($terms)." terms: <br>";
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
							
							if ($this->srcdebug) print "<br>PROCESSING ($term) - TERM RAW (".$TERMS_RAW{$term}.")";
							$patternterm=str_replace('/','\/',$term);
							if (
                  // !preg_match("/$patternterm/",$refined_terms) // FRI - refined_terms seems to be unused
							 /*&&*/ !preg_match("/$patternterm/",$text) 
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
        if ($sortrank=='standard')
        {
          arsort($CANDIDATES); // sortiere nach Values - top value first
					array_splice($CANDIDATES, $m); // behalte die ersten $m insgesamt
        }
          
				$refined_terms=''; //reset
				$refined_terms_raw='';
				if ($this->srcdebug) print "<br> SPLICED ($m) CANDIDATES: ".cout($CANDIDATES);

				foreach($CANDIDATES as $term=>$RANK)
				{
					if ($term) // term is non stopword
					{
						
						if ($this->srcdebug) print "<br> CANDIDATE ($term):";
						
						
						//-----------------------------------------------
						if ($this->getWordbinding() == 'STW')
						{
							$nextterm=trim(cleanup_stopwords_str($term));
							
							//Construct a sequence of terms separated by $TERM_SEPARATOR
							if ($refined_terms) $refined_terms.="$TERM_SEPARATOR\n"; 
								$refined_terms.=$nextterm;
							// construct a sequence of base64coded terms separated by $TERM_SEPARATOR
							if ($refined_terms_raw) $refined_terms_raw.="$TERM_SEPARATOR\n"; 
							{
								if ($this->srcdebug) print "<br>Assembling TERMS_RAW: ".$TERMS_RAW{$term};
								$refined_terms_raw.=base64_encode($TERMS_RAW{$term});
							}							
						}
						//-----------------------------------------------
						else if ( $this->getWordbinding() == 'RAMEAU'
				 					 || $this->getWordbinding() == 'LOC'
									 || $this->getWordbinding() == 'GND'
									 )
						{ // no stopword cleaning for RAMEAU nor for LOC !!!!
							$nextterm = $term;
							//Construct a sequence of terms separated by $TERM_SEPARATOR
							if ($refined_terms) $refined_terms.="$TERM_SEPARATOR\n"; 
								$refined_terms.=$nextterm;
							// construct a sequence of base64coded terms separated by $TERM_SEPARATOR
							if ($refined_terms_raw) $refined_terms_raw.="$TERM_SEPARATOR\n"; 
								$refined_terms_raw.=base64_encode($TERMS_RAW{$term});			
							
						}
						//-----------------------------------------------
						else if ($this->getWordbinding() == 'DBP')
						{
							$nextterm=trim(cleanup_stopwords_str($term));
							
						//Construct a sequence of terms separated by $TERM_SEPARATOR
						if ($refined_terms<>'') $refined_terms.="$TERM_SEPARATOR\n"; 
							$refined_terms .= $this->cleanup_viki_tokens($nextterm);
						// construct a sequence of base64coded terms separated by $TERM_SEPARATOR
						if ($refined_terms_raw<>'') $refined_terms_raw.="$TERM_SEPARATOR\n"; 
							$refined_terms_raw.=base64_encode($TERMS_RAW{$term});
						}
					}
				} // foreach
			}
		} // ok

		
		return new SRCEngineResult(trim($refined_terms), trim($refined_terms_raw));
	} // refine
	
  
  

  /*
   * Returns true if the content is a valid nonempty content
   * false otherwise 
   */
  protected function src_cached_content_quality_control($SRC_XML_CACHE_CONTENT)
  { 
    //Has the answer at least data inside one of (broader/related/narrower)?
    $need_src_log=false;

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

    //if (! $ok) print "NOK src_cached_content_quality_control";
    
    return $ok; // debug
  }
 
  /**
	 * Returns array of suggestions
	 * @param &$SOLRCLIENT 
	 * @param $labelkind - skos_prefLabel_de or skos_prefLabel_en or ...
	 */
	 
	//collect_labels($SOLRCLIENT,'skos_prefLabel_de',$term,$this->STW_SUGGESTION_FIELDS)
	protected function collect_labels_for_autocomplete(&$SOLRCLIENT,$maxsuggestions,$labelkind,$term,&$SUGGESTION_FIELDS)
	{
		$DEBUG=0;
		if ($DEBUG) 
		{ print "<br>collect_labels_for_autocomplete SOLRCLIENT: ";var_dump($SOLRCLIENT);}
		
		if (!$maxsuggestions) $maxsuggestions=10; // default
		$labels=array();
		$query = $SOLRCLIENT->createSelect();
		$query->createFilterQuery($labelkind)->setQuery("$labelkind:$term*");
		$query->setStart(0)->setRows($maxsuggestions);
		$query->setFields($SUGGESTION_FIELDS);
		$resultset = $SOLRCLIENT->select($query);
		$noofresults=$resultset->getNumFound();
		if ($noofresults)
		{
			foreach ($resultset as $document) 
	    { $d++;
			
			if ($DEBUG) { print "<br>SOLR DOC: "; var_dump($document);}
			
	     	$labels4onedoc = $document{$labelkind}[0];
				//print "<br><br>$d document: label=".$labels4onedoc;
	 			$labels[]=$labels4onedoc;
	  					
	     } // foreach $document AS $fieldname => $value
		} // $noofresults
		//foreach($labels as $l) print "<br> $l";
		return $labels;
	} //collect_labels_for_autocomplete
	
	 
} // class SRCengine








/**
 * Represents a result set returned by a SRC for a single action
 */
class SRCEngineResult {
	public $ok;
	public $results;
	public $results_raw;
	public $results_root;
	public $explanation;
	
	function SRCEngineResult($results, $results_raw = null, $results_root=null, $explanation = 'Coming soon', $ok = true) {
		$this->ok = $ok;
		$this->results = $results;
		$this->results_raw = $results_raw;
		$this->results_root = $results_root;
		$this->explanation = $explanation;
	}		
}


/**
 * Represents a result set returned by a SRC for a single action
 */
class SRCEngineSKOSResult {
	public $ok;
	public $suggested;
	public $broader;
	public $narrower;
	public $related;
	public $explanation;
	
	function SRCEngineSKOSResult( &$suggested, &$broader, &$narrower, &$related, 
																$ok = true )
	{
		$this->ok = $ok;
		$this->suggested=$suggested;
		$this->broader = $broader;
		$this->narrower = $narrower;
		$this->related = $related;
	}	
	
}

?>