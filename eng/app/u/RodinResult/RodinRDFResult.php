<?php

/**
 * An RDF enhancement to the basic rodin result
 * A helper class
 * @author Fabio Ricci fabio.ricci@ggaweb.ch
 */
 
 
 /**
  * TODO
  * 
  * 1. ACCESS europeana different - SPARQL query more precise, language in query
  * 2. Limit subjects ... they are still to many ... ?
  * 3. Rank documents
  * 4. Integrate in GUI (sid -> store ?)
  */
 
 
//Include ARC2 LOCAL STORE INFOS
$filename="u/arcUtilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}




//Include ARC2 LOCAL STORE INFOS
$filename="fsrc/gen/u/arc/ARC2.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
			


$filename="app/u/LanguageDetection.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}




class RodinRDFResult {
	
	private $my_result; //triples from this result were generated and inserted into store
	public  static $USER_ID						= null; 
	public  static $importGraph				= null;
	public  static $searchterm				= null; 
	public  static $searchtermlang		= null; 
	public  static $datasource				= null; 
	public  static $storename 				='rodin_rdf'; // Name of store where to insert triples
	public  static $store 						= null; // ARC localstore Config obj
	public  static $ownnamespacename 	='rodin';
	public 	static $NAMESPACES 				= null; // namespaces
	public	static $NAMESPACES_PREFIX = null;
	public  static $TOBECODED64 			= null; // to be used inside a SPARQL query
	private static $PUBBLICATION_URL 	= null; // to see/navigate/access triples
	public  static $one_work = null; // root node for triples page
	private static $TERM_SEPARATOR    =',';
	private static $DBPEDIA_BASE="http://dbpedia.org";
	private static $WIKIPEDIABASE="http://en.wikipedia.org";
	private static $DBPEDIA_SPARQL_ENDPOINT = "http://dbpedia.org/sparql";
	private static $WIKIPEDIABASEURL="http://en.wikipedia.org/wiki";
	private static $WIKIPEDIASEARCH= "http://en.wikipedia.org/w/api.php?action=opensearch&format=xml";
	private static $WIKIPEDIASEARCH2="http://en.wikipedia.org/w/index.php?";

	public function RodinRDFResult(&$my_result,$datasource,$searchterm,$USER_ID) {
		$this->my_result = $my_result;
		//init namespaces & co once for all 
		global $HOST, $RODINSEGMENT;
		
		if (!RodinRDFResult::$USER_ID) 					RodinRDFResult::$USER_ID					=$USER_ID;
		if (!RodinRDFResult::$datasource) 			RodinRDFResult::$datasource				=$datasource;
		if (!RodinRDFResult::$searchterm) 			RodinRDFResult::$searchterm				=$searchterm;
		if (!RodinRDFResult::$searchtermlang) 	RodinRDFResult::$searchtermlang		=detectLanguage($searchterm);
		if (!RodinRDFResult::$importGraph) 			RodinRDFResult::$importGraph			="http://$HOST/rodin/w3s/";
		if (!RodinRDFResult::$PUBBLICATION_URL) RodinRDFResult::$PUBBLICATION_URL	="http://$HOST/rodin/$RODINSEGMENT/app/w3s";
		
		//print "<br>INITIALIZING RodinRDFResult ... HOST=$HOST RODINSEGMENT=$RODINSEGMENT, PUBBLICATION_URL=".$this->PUBBLICATION_URL;
		
		if (!is_array(RodinRDFResult::$NAMESPACES))
				RodinRDFResult::$NAMESPACES= array(
					'foaf'	=> 'http://xmlns.com/foaf/0.1/',
					'rdf'		=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
			    'rdfs'	=> 'http://www.w3.org/2000/01/rdf-schema#',
			    'geo'		=> 'http://www.w3.org/2003/01/geo/wgs84_pos#',
					'dbo'		=> 'http://dbpedia.org/ontology/',
			    'dce'		=> 'http://purl.org/dc/elements/1.1/',
			    'dct'		=> 'http://purl.org/dc/terms/',
			    'dc'		=> 'http://purl.org/dc/',
			    'bio'		=> 'http://vocab.org/bio/0.1/',
			    'bibo'	=> 'http://bibliontology.com/bibo/bibo.php#',
			    'rodin_e'	=> RodinRDFResult::$PUBBLICATION_URL.'/resource/e/',//pubblication external imported e=europeana
			    'rodin'	=> RodinRDFResult::$PUBBLICATION_URL.'/resource/',	//pubblication internal resources
			    			
					// Europeana:
					// see also http://pro.europeana.eu/documents/866205/13001/EDM_v5.2.2.pdf
					'ore' 	=>'http://www.openarchives.org/ore/',
					'epp' =>'http://data.europeana.eu/proxy/provider/',
		     	'eedm' =>'http://www.europeana.eu/schemas/edm/',
					'e'	=> 'http://data.europeana.eu/',
					);

	 if (!is_array(RodinRDFResult::$TOBECODED64)) 
	 		RodinRDFResult::$TOBECODED64 = array('dce:description','masic:title_orig','masic:subtitle_orig','masic:genealogic_tree','masic:abbreviation');

		// Build NAMESPACES_PREFIX
		if (! RodinRDFResult::$NAMESPACES_PREFIX)
		{
			if (is_array(RodinRDFResult::$NAMESPACES) && count (RodinRDFResult::$NAMESPACES))
				foreach(RodinRDFResult::$NAMESPACES as $ns=>$nsurl)
					RodinRDFResult::$NAMESPACES_PREFIX.="PREFIX $ns: <$nsurl>\n";	
		}
		
		if (!RodinRDFResult::$store)
		{
			//print "<br>INITIALIZING STORE ".RodinRDFResult::$storename;
			global $ARCCONFIG;
			$LOCALCONFIG=$ARCCONFIG;
	    $LOCALCONFIG{'store_name'}=RodinRDFResult::$storename;
	    RodinRDFResult::$store = ARC2::getStore($LOCALCONFIG);
	    if (!RodinRDFResult::$store->isSetUp()) {
	       RodinRDFResult::$store->setUp();
	    } else
				{
					//Only iff the constructor is called with a result: 
					//ant only the first time when not yet initialized: 
					//reset triples in store!
					if ($my_result) 
						RodinRDFResult::$store->reset(); // clear store from Triples!
				}
	  }
	}
	
	
	
	/**
	 * Generate triples into local store 'rodin'
	 * which will be taken as a basis for further 
	 * semantic expansions (meshups)
	 * 1. generates triples
	 * 2. inport triples into store
	 * @param string $searchterm the searchterm for this result
	 */
	public function rdfize( $sid )
	{
		$DEBUG=0;
		$showsubjects=1;
		global $RDFLOG;
		
		##############################################
		#
		#In case the datasource supplies at least five subjects, 
		#do not calculate further subjects from title.
		#
		$THRESHOLD_DATASOURCE_MIN_SUBJECTS=5; 
		#
		##############################################
		
		$RDFLOG.="<hr>RDFIZE RESULT:<hr>";
		Logger::logAction(27, array('from'=>'rdfize','msg'=>'Started with sid:'.$sid));
		$lang=detectLanguage(RodinRDFResult::$searchterm);
		#####################################################################
		
		$triple= array();
		//Do we have one or more authors?
		$authors= $this->my_result->getAuthors() 
							?explode(',',$this->my_result->getAuthors())
							:null;
		$isbn= $this->extract_isbn($this->my_result->getProperty('isbn')); 
		$title= trim($this->my_result->getTitle()); 
		$date = $this->my_result->getDate();
		$urlPage = $this->my_result->getUrlPage();
		$publisher = $this->my_result->getProperty('publisher');
		//$datasource_subjects = explode(',',strtolower(trim($this->my_result->getProperty('subjects'))));
		$datasource_subjects = $this->compute_datasource_subjects(trim(strtolower($this->my_result->getProperty('subjects'))),$lang);
		if ($showsubjects) tell_subjects($datasource_subjects,"considered datasource subjects:");
		
		//print "<br>Datasource >Subjects (".$this->my_result->getProperty('subjects').")";
		
		//Prepare title category etc ... if possibile
		if (count($datasource_subjects) < $THRESHOLD_DATASOURCE_MIN_SUBJECTS)
		{
			list($title,$category,$presentation_at,$date_event) = $this->scan_datasource_title($title,RodinRDFResult::$datasource);
			$additional_subjects=$this->compute_title_subjects(RodinRDFResult::$searchterm,$title,RodinRDFResult::$datasource,$lang);
			if ($showsubjects) tell_subjects($additional_subjects,"extracted additional title subjects:");
	
	
			//print "<br>ADDITIONAL Subjects (".implode('+',$additional_subjects).")";
	
			$msubjects=array_unique(array_merge($datasource_subjects,$additional_subjects));
			$uniquesubjects = $this->compute_unique_subjects($msubjects,$lang);
			if ($showsubjects) tell_subjects($uniquesubjects,"extracted unique subjects:");
			
			$subjects=array_unique(array_merge($msubjects,$uniquesubjects));
			//print "<br>FINAL Subjects (".implode('+',$subjects).")";
			
			if ($showsubjects) tell_subjects($subjects,"globally considered subjects:");
		} // $THRESHOLD_DATASOURCE_MIN_SUBJECTS
		else
		{
			//Take simply these - they should be enaugh
			$subjects = $datasource_subjects;
		}
		
		
		$skos_subjects_expansions 
							= $this->expand_related_subjects_using_thesauri(	$subjects,
																																$sid,
																																RodinRDFResult::$USER_ID,
																																RodinRDFResult::$NAMESPACES,
																																$lang );
																																
		if ($showsubjects) tell_skos_subjects($skos_subjects_expansions, 'SKOS subjects');
		
		//If a work document is given: rdfize it
		//first of all create workuid
		if ($isbn)
			$work_uid=RodinRDFResult::$ownnamespacename.':'.'isbn_'.$isbn;
		else // in case no isbn be provided:
		if ($title) // a title HAS ALWAYS to be there
			$work_uid = $this->getWork_uid($title, $date);

		//Are there one or more authors?
		if (is_array($authors) && count($authors))
		foreach($authors as $author)
		{
			$authors_uid{RodinRDFResult::$ownnamespacename.':'.RodinRDFResult::adapt_name_for_uid($author)} = $author;
		} // authors
		
		//is there a publisher?
		if ($publisher)
		{
			 $publishers_uid{RodinRDFResult::$ownnamespacename.':'.RodinRDFResult::adapt_name_for_uid($publisher)} = $publisher;
		}
		
		
		//Publish the work:
		if ($work_uid)
		{
				$triple[]=array($work_uid,		'rdf:type', 		'dce:BibliographicResource'); 
				$triple[]=array($work_uid,		'dce:title', 		l($title)); 
				if ($isbn)
					$triple[]=array($work_uid,	'bibo:isbn', 		l($isbn)); 
				if($date)
					$triple[]=array($work_uid,	'dce:date', 			 l($date)); 
				if($urlPage) {
					$triple[]=array($work_uid,	'dce:source', 		 l($urlPage)); 
				}
				
				//Add subjects
				if (is_array($subjects) && count($subjects))
				{
					foreach($subjects as $subject)
					{
						if($subject)
						{
							$subject_uids[]=$subject_uid=RodinRDFResult::$ownnamespacename.':'.RodinRDFResult::adapt_name_for_uid($subject);
							//print "<br>Adding subject: ($subject)";
							//print "<br>subject=($subject) subject_uid=($subject_uid) asserting ($work_uid,	'dce:subject', 	$subject_uid)";
							$triple[]=array($work_uid,	'dce:subject', 	$subject_uid); 
							$triple[]=array($subject_uid,	'rdf:type', 	'dce:subject'); 
							$triple[]=array($subject_uid,	'rodin:label', l($subject)); 
							
							$subject=strtolower($subject);
							//add related subjects from thesauri to s
							if (count(($SKOSEXPANSIONS=$skos_subjects_expansions{$subject})))
							{
								foreach($SKOSEXPANSIONS as $SKOS)
								{
									list($src_name,$broaders,$narrowers,$related) = $SKOS;
	
									if (count($broaders))
									foreach($broaders as $bs)
									{
										if (strtolower($bs)<>$subject)
										{
											if ($DEBUG) print "<br>asserting ($s broader $bs)";
											$bs_uid=RodinRDFResult::$ownnamespacename.':'.RodinRDFResult::adapt_name_for_uid($bs);
											$triple[]=array($subject_uid,	'rodin:broader', 					$bs_uid); 
											$triple[]=array($subject_uid,	'rodin:subject_related', 	$bs_uid); 
											$triple[]=array($bs_uid,			'rodin:label', 	 					l($bs)); 
											$triple[]=array($bs_uid,			'rdf:type', 	 						'dce:subject'); 
										}
									}
	
									if (count($narrowers))
									foreach($narrowers as $ns)
									{
										if (strtolower($ns)<>$subject) 
										{
											if ($DEBUG) print "<br>asserting ($s narrower $ns)";
											$ns_uid=RodinRDFResult::$ownnamespacename.':'.RodinRDFResult::adapt_name_for_uid($ns);
											$triple[]=array($subject_uid,	'rodin:narrower', 				 $ns_uid); 
											$triple[]=array($subject_uid,	'rodin:subject_related', 	 $ns_uid); 
											$triple[]=array($ns_uid,			'rodin:label', 	 					l($ns));
											$triple[]=array($ns_uid,			'rdf:type', 	 						'dce:subject'); 
										}
									}
	
									if (count($related))
									foreach($related as $rs)
									{
										if (strtolower($rs)<>$subject) 
										{
											if ($DEBUG) print "<br>asserting ($s related $rs)";
											
											$rs_uid=RodinRDFResult::$ownnamespacename.':'.RodinRDFResult::adapt_name_for_uid($rs);
											$triple[]=array($subject_uid,	'rodin:related', 					$rs_uid); 
											$triple[]=array($subject_uid,	'rodin:subject_related', 	$rs_uid); 
											$triple[]=array($rs_uid,			'rodin:label', 	 					l($rs)); 
											$triple[]=array($rs_uid,			'rdf:type', 	 						'dce:subject'); 
										}
									}
								} // add related subjects from thesauri to s
								} // SKOS
						} // nonzero sobject
					} // for subjects
				} // subjects
		} // $work_uid
		
		// add/link author information:
		if (is_array($authors_uid) && count($authors_uid))
		foreach($authors_uid as $author_uid=>$authortxt)
		{
			$triple[]=array($author_uid, 	'rdf:type', 		'foaf:Person'); 
			$triple[]=array($author_uid, 	'foaf:name', 		l($authortxt)); 
			$triple[]=array($author_uid, 	'rodin:name', 	l($authortxt)); 
			$triple[]=array($author_uid, 	'dce:creator', 	 $work_uid); 
			$triple[]=array($author_uid,	'rodin:author',  $work_uid);
			if ($isbn) 
				$triple[]=array($author_uid, 	'bibo:isbn', 		l($isbn)); 
			if ($urlPage)
				$triple[]=array($author_uid,	'foaf:Document', l($urlPage)); 
			
			
			//Link author writes about subjects
			if (is_array($subject_uids) && count($subject_uids))
			{
				foreach($subject_uids as $subject_uid)
					$triple[]=array($author_uid, 'rodin:writes_about', $subject_uid); 
			} // some subjects
		} //authors_uid
		
		//add/link publisher information:
		if (is_array($publishers_uid) && count($publishers_uid))
		foreach($publishers_uid as $publisher_uid=>$publisher_txt)
		{
			$triple[]=array($publisher_uid, 'rdf:type', 		'foaf:Person'); 
			$triple[]=array($publisher_uid,	'foaf:name', 		l($publisher_txt)); 
			$triple[]=array($publisher_uid,	'rodin:name', 	l($publisher_txt)); 
			$triple[]=array($publisher_uid,	'dce:publisher', 		$work_uid); 
			$triple[]=array($publisher_uid,	'rodin:publisher', 	$work_uid); 
			
			//Link author writes about subjects
			if (is_array($subject_uids) && count($subject_uids))
			{
				foreach($subject_uids as $subject_uid)
					$triple[]=array($publisher_uid, 'rodin:writes_about', $subject_uid); 
			} // some subjects
				
		} // publishers
		
		Logger::logAction(27, array('from'=>'rdfize','msg'=>'Import triples start'));
	
		
		// IMPORT TRIPLE INTO LOCAL STORE:
		if (is_array($triple) && count($triple))
			$statistics = $this->import_triples($triple);

		//if ($statistics) print "<hr>$statistics";
		$return_value=$statistics?RodinRDFResult::$store:null;
				
		Logger::logAction(27, array('from'=>'rdfize','msg'=>'Exit'));
		return $return_value; 
	} // rdfize 
	
	
	
	
	
	
	/**
	 * Imports triples into store
	 * @param $storename The store to be used
	 * @param $triples A vector of triples (arrays(s,p,o))
	 * returns: Statistic object reflecting import process
	 * 
	 * Important: the object of a triple must have <> or "" to denote literal
	 */
	function import_triples(&$triples)
	{
		$statistics=null;
		$GRAPH=RodinRDFResult::$importGraph;
		$NAMESPACES_PREFIX = RodinRDFResult::$NAMESPACES_PREFIX;
		$TRIPLETEXT="
		$NAMESPACES_PREFIX
    INSERT INTO <$GRAPH> 
    {";
 	
		$i=0;
		
		
		//print "<br>import_triples called with:<br>"; var_dump($triples);
		if (is_array($triples) && count($triples)>0)
		{
			foreach($triples as $triple)
			{
				$i++;
				$s=$triple[0];
				$p=$triple[1];
				$o=cleanup4literal($triple[2]); // literals might contain ' '' ... / \ etc ... ?
				
		  	$TRIPLETEXT.="\n $s $p $o .";
				
				//if($i>13) break;
			}
			
			$TRIPLETEXT.='}';
			
			$debug=0;
			if($debug) print "<br>ARC CONSTRUCTING: <hr>".str_replace("\n","<br>",htmlentities($TRIPLETEXT));	
			
			$num_triples_before=count_ARC_triples(RodinRDFResult::$store);
	    $num_triples_before_formatted=number_format($num_triples_before, 0, '.', "'");
	
	    //We need on the server at HEG to enhance php execution time limit, 
	    //since this server is slowlee and need more time than the local power macs
	    set_time_limit ( 1000000 ); // 250h -> Feature in 5.3.0 deprecated, in 5.4.0 deleted - but useful right now
	    $rs=NULL;
	    $repetitions=0;
	    $added_triples=0;
			
	    $rs= RodinRDFResult::$store->query($TRIPLETEXT);
	    $added_triples = intval($rs['result']['t_count']);
	    $repetitions++;
	    if (($errs = RodinRDFResult::$store->getErrors())) {
	
	      foreach($errs as $err)
	      fontprint("<br>ARC ERROR: $err",'red');
				print "<hr>ARC CONSTRUCTING USING: <br>".str_replace("\n","<br>",htmlentities($TRIPLETEXT));	
				
	    }
	    
			$duration = $rs['query_time'];
			//$added_triples = $rs['result']['t_count'];
			$load_time = $rs['result']['load_time'];
	
	    $num_triples_before_formatted=number_format($num_triples_before, 0, '.', "'");
	
	    $added_triples_formatted=number_format($added_triples, 0, '.', "'");
	
		  $delta_triples=abs($num_triples_before - $added_triples);
		  $delta_triples_formatted=number_format($delta_triples, 0, '.', "'");
		 
		  $verb= ($delta_triples==0)?'Updated':'Loaded';  
	    
			if ($delta_triples > 0)
			$EVTL_ADDED="Added $delta_triples_formatted triples";
			
	    if ($added_triples==0) 
	        $statistics=null;
	    else
	    { 
	      $num_triples_after=count_ARC_triples(RodinRDFResult::$store);
	      $num_triples_after_formatted=number_format($num_triples_after, 0, '.', "'");
	      
	      $triples_delta=$num_triples_after - $num_triples_before;
	      $EVTL_DELTA=" (delta triples=$triples_delta)";
	        
	      $REPS=($repetitions>1)?" ($repetitions repetitions)":"";
	      $ESITO=($added_triples>0)
	                  ?"$added_triples triples$EVTL_DELTA$REPS"
	                  :"<b><font style='color:red'>No triples ($added_triples_formatted) added after $repetitions repetitions</font></b>";
	      $statistics="Triple file processed: $ESITO, duration: $duration sec, load_time: $load_time sec - total triples after processing: $num_triples_after_formatted";
	    }
	    //Avoid updating statistics if no triples added...
    }

    return $statistics;
		
	} // import_triples
	
	
	
	/**
		 * Construct an id for SPQRQL USE
		 * using name, born and place of birth
		 * returns uid (string)
		 */
		public function getWork_uid($title,$date,$namespace_short='rodin')
		{
				$wuid_short=
						$namespace_short.':'
							.RodinRDFResult::adapt_name_for_uid($title).'_'
							.RodinRDFResult::adapt_name_for_uid($date);
			return $wuid_short;
		} // getWork_uid
	
	
	/**
		 * Construct an id for SPQRQL USE
		 * using name, born and place of birth
		 * returns uid (string)
		 */
		public function getAuthor_uid($namespace_short='rodin_result')
		{
			//retrieve/generate uid long:
			if ($this->author_uid_long)
				$uid_long = $this->author_uid_long;
			else {
				$uid_long= 
							$namespace_short.':'
							.RodinRDFResult::adapt_name_for_uid($this->masic_author_name).'_'
							.RodinRDFResult::adapt_name_for_uid($this->masic_author_no).'_'
							.RodinRDFResult::adapt_name_for_uid($this->masic_author_fromplace).'_'
							.RodinRDFResult::adapt_name_for_uid($this->masic_author_life_range);
				$this->author_uid_long=$uid_long;
			} 
					
			//retrieve/generate uid short (to be used with a redirect):
			if ($this->author_uid_short)
				$uid_short = $this->author_uid_short;
			else {
				$uid_short= 
							$namespace_short.':'
							.RodinRDFResult::adapt_name_for_uid($this->masic_author_no).'__'
							.RodinRDFResult::adapt_name_for_uid($this->masic_author_life_range);
				$this->author_uid_short=$uid_short;
			}
			
			//print "<br>getAuthor_uid($namespace_short)=list($uid_long,$uid_short)";
			
			return array($uid_long, $uid_short);
		} // getAuthor_uid

	
	
	  public static function adapt_name_for_uid($str,$allowed_chars="-")
		{
			$maxlen=32; // limit each output to maxlen
			$str_orig=$str;
			$SUBST=(strstr($allowed_chars,' '))?'_':'';
			$str = str_replace(' ',$SUBST,$str);
	
			$SUBST=(strstr($allowed_chars,'-'))?'_':'';
			$str = str_replace('-',$SUBST,$str);
			
			$str = str_replace('(','_',$str);
			$str = str_replace(')','_',$str);
			$str = str_replace('/','_',$str);
			$str = str_replace('.','_',$str);
			
			// allow only normal chars...
			$language_specialcharsdd='ßÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
			$pattern = '/[^A-Za-z0-9'.$language_specialchars.'\-_]+/i';
			$replace = '';
			$str= trim(preg_replace($pattern, $replace, $str));
			if ($str=='')
			{
				$str=substr($str,0,$maxlen); // Do not supply an empty string
			}
			
			return strtolower(substr($str,0,$maxlen));
		}
	
	
	
	
	
	
	
	
	/** returns a vector of strings (subjects)
	 * gained from search and title text
	 * Uses only the subjects with the same language as $lang
	 * 
	 * @param string $subjects - the subjects
	 * @param string $lang - the abbreviation of the language of the search term
	 */
	public function compute_unique_subjects($subjects,$lang)
	{
		$DEBUG=0;
		global $RDFLOG;
	
		$uniquesubjects=array();
		
		if ($DEBUG) $RDFLOG.="<br>compute_unique_subjects(".count($n)." subjects,$lang)";		
		foreach( $subjects as $sss )
		{
			$subjects_cand=preg_split("/[ ]+/",$sss); //factorize by blank a compound subject
			foreach($subjects_cand as $candidate_subject)
			{
				//print "<br>Consider $candidate_subject";
				if ($DEBUG) $RDFLOG.="<br>Considering candidate_subject '$candidate_subject'";
				insert_filtered_once($candidate_subject,$uniquesubjects,$lang);
			} // foreach
		} // foreach
		
		return $uniquesubjects;
	} // compute_unique_subjects
	
	
	
	/** returns a vector of strings (subjects)
	 * gained from search and title text
	 * Uses only the subjects with the same language as $lang
	 * 
	 * @param string $ds_subjects - the original datasource subject string already trimmed and strlowered
	 * @param string $lang - the abbreviation of the language of the search term
	 */
	public function compute_datasource_subjects($ds_subjects,$lang)
	{
		$DEBUG=0;
		global $RDFLOG;
		
		//print "<br>compute_datasource_subjects($ds_subjects,$lang)";
		
		$subject_arr=array();
		$subjects_cand=preg_split("/[,:;\+\-*]+/",$ds_subjects);
		
		if(count($subjects_cand))
		{
			foreach($subjects_cand as $candidate_subject)
			{
				//print "<br>Consider $candidate_subject";
				insert_filtered_once($candidate_subject,$subject_arr,$lang);
			} // foreach
		}
		
		return $subject_arr;
	} // compute_datasource_subjects
	
	
	
	
		
	/**
	 * returns a vector of strings (subjects)
	 * gained from search and title text
	 * tries to discard event in structures like
	 * 1. text (event)
	 * 2. text [event]
	 * 
	 * @param string $search
	 * @param string $title
	 * @param string $datasource
	 * @param string $lang
	 */
	public function compute_title_subjects($search,$title,$datasource,$lang)
	{
		$DEBUG=0;
		global $RDFLOG;
	
		if (!$title) print "<br>compute_title_subjects(): Error compute_subjects called with empty title !!!";
	
	  if($DEBUG) $RDFLOG.="<br>compute_title_subjects($title)<br>TRY TO RECOGNIZE/DELETE EVENTS";
		//Recognize and filter out event
		$PATTERNEVENT[]="/(.*)\(.*\)/"; // text (event)
		$PATTERNEVENT[]="/(.*)\[.*\]/"; // text [event]
		foreach($PATTERNEVENT as $PATTERN)
		{
			if($DEBUG) $RDFLOG.="<br>CONSIDER PATTERN ($PATTERN) on ($title)";
			
			if (preg_match($PATTERN,$title,$match))
			{
				$title=$match[1];
				if($DEBUG) $RDFLOG.=" YES ($title)";
				break;
			}
			else 
			{
				if($DEBUG) $RDFLOG.=" NO!";
			}
		}
	
	
		$title_cleaned_arr=array_unique(cleanup_stopwords(explode(' ',strtolower(clean_spechalchars($title)))));
		$title_cleaned=implode(' ',$title_cleaned_arr); //separate into chunks
		
		if($DEBUG)
		{
			$RDFLOG.= "<br><b>compute_title_subjects</b>((($search)),(($title))):";
			$RDFLOG.= "<br>title_cleaned: (($title_cleaned))";
		}
		$subjects=array();	
		//Add as subject the whole title - without colons...
		if (($langt=detectLanguage($title_cleaned))==$lang)
		{
			$subjects[]=  trim(preg_replace("/[:;]/",'',$title_cleaned));
			if($DEBUG)  $RDFLOG.= "<br>TAKE($langt==$lang) SUBJ($title_cleaned):";
		} else {
			if($DEBUG)  $RDFLOG.= htmlprint("<br>DISC SUBJ($langt<>$lang) SUBJ($title_cleaned)",'red');
		}
			
		//Try to build complex compund and validate them in vikipedia
		//each validated vikipedia term is a subject.
		//use also RODIN's thesauri 
		//Use also EUROPEANA's thesaurus
		//Try to build biggest sub-compounds
		//A sub-compound is validated in at least one given LODstore
		//and must be matched without need of disambiguation!
		
		//$allTermLabels = $this->get_validated_english_candidate_compounds($title_cleaned,$remoteLODstores=array($nix));
			
		//Returns as speedup 1. the subject as is, 2. every single segment separated by nonblank of the $title_cleaned
		
		$segments=preg_split("/[,:;\+\-*]+/",$title_cleaned);
		
		if (count($segments))
		{
			foreach($segments as $segment)
			{
				insert_filtered_once($candidate_subject,$subjects,$lang);
			} // foreach
		}
		
		return $subjects;
	} // compute_subjects
	
	
	
	
	
	

	/**
	 * Extracts an isbn expression out of text which must begin with the isbn number
	 * returns a triple (isbn,rest)
	 * @param text $text
	 * 
	 * 978-0-273-76831-9 something
	 */
	public function extract_isbn($text)
	{
		$isbn=$text; //default
		$PATTERN_ISBN="/([\w-]+)/";
		//print "<br>extract_isbn ($text) ";
		if (preg_match($PATTERN_ISBN,$text,$match))
		{
			$isbn=$match[1];
		}
		//else print " NO using $PATTERN_ISBN in (($text))";
		return $isbn;
	}
	
	
		
	
	/**
	 * Returns a scanning of given retrieved title content
	 * in case of swissbib: array(($title),($congress),($place),($date))
	 * @param $text
	 * @param $datasource
	 */
	public function scan_datasource_title($text,$datasource)
	{
		$DEBUG=0;
		//tokenize and date_parse
		//print "<br>cleanup_dates($text,$datasource) ...";
		if (strstr($datasource,'swissbib'))
		{
			//print " SWISSBIB ";
			/**
			 * Scan one of the following text pattern:
			 * "Spatial disorientation in flight current problems : papers presented at the Aerospace medical Panel specialists' meeting;Bodø, 20-23 May 1980" 
			 * "Toxic hazards in aviation papers pres.at the Aerospace medical Panel specialists' meeting; Toronto, 15-19 September 1980"
			 */
			
			$PATTERN_TITLE="/(.*)papers\W(pres\.|presented\W)at/";
			
			if (($match = uni_preg_match($PATTERN_TITLE,$text)))
			{
				$title=trim($match[1]);
				//print "<br>cleanup_dates matched place($place) and editdate($editdate)";
				$resttext=trim(str_replace($match[0],'',$text));
				if ($DEBUG)
				{
					print "<br><b>title</b>: (($title))";
					print "<br>resttext: (($resttext))";
				}			
				$PATTERN_CONGRESS="/^(.*);(\W*)/";
				$matched=false;
				if (($match = uni_preg_match($PATTERN_CONGRESS,$resttext)))
				{
					$congress=trim($match[1]);
					$resttext=trim(str_replace($match[0],'',$resttext));
					
					if ($DEBUG)
					{
						print "<br><b>congress</b>: (($congress))";
						print "<br>resttext: (($resttext))";
					}
					$PATTERN_PLACE="/^([\p{L}]+),/";
					if (($match = uni_preg_match($PATTERN_PLACE,$resttext)))
					{
						$place=trim($match[1]);
						$resttext=trim(str_replace($match[0],'',$resttext));
						if ($DEBUG)
						{
							print "<br><b>place</b>: (($place))";
							print "<br>resttext: (($resttext))";
						}
						$PATTERN_EVENTDATE1="/^\W(.*)\W(\w{3})\W(\d4)/";
						$PATTERN_EVENTDATE="/^(.*)-(.*)/";
						if (($match = uni_preg_match($PATTERN_EVENTDATE,$resttext)))
						{
							$date1=trim($match[1]);
							$date2=trim($match[2]);
							$eventdaterange = "$date1-$date2";
							
							$resttext=trim(str_replace($match[0],'',$resttext));
							if ($DEBUG)
							{
								print "<br><b>eventdaterange</b>: (($eventdaterange))";
								print "<br>resttext: (($resttext))";
							}
						} // matched $PATTERN_EVENTDATE
						else fontprint( "<br>NOMATCH IV PATTERN_EVENTDATE $PATTERN_EVENTDATE in (($resttext))", 'red');
						
					} // matched $PATTERN_PLACE
					else fontprint(  "<br>NOMATCH III PATTERN_PLACE $PATTERN_PLACE in (($resttext))", 'red');
					
				} // matched $PATTERN_CONGRESS
				else fontprint(  "<br>NOMATCH II PATTERN_CONGRESS $PATTERN_CONGRESS in (($resttext))", 'red');
				
			} else 
			{
				//take the title as is...
				$title=$text;
				$congress=$place=$eventdaterange='';
			}
			$scanned_obj=array($title,$congress,$place,$eventdaterange);
		
		} // swissbib
							
		return $scanned_obj;
	} // cleanup_dates
	
		
		
  
	/**
	 * Taken from DBPedia engine:
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
	 * @param text $terms - a text containing terms
	 * @param array $remoteLODstores - vector of remote SPARQL stores to validate subject
	 */
	protected function get_validated_english_candidate_compounds($terms,$remoteLODstores) 
	{
		$SRCDEBUG=1;
		$VERBOSE=1;
    if ($SRCDEBUG) print "<br><br>get_validated_english_candidate_compounds";
		$tokenizationResults = $this->tokenizeCandidateCompounds(preg_replace('/\s+/', ' ', trim($terms)));
		$usercompounds = $this->flattenCompoundTerms($tokenizationResults['usercompounds']);
		$candidatecompounds = $this->flattenCompoundTerms($tokenizationResults['candidatecompounds']);
		$singletons = $this->flattenCompoundTerms($tokenizationResults['singletons']);

    if ($SRCDEBUG) 
    {
      print "\n<br>tokenizationResults: "; 	var_dump($tokenizationResults);
      print "\n<br>usercompounds: "; 				var_dump($usercompounds);
      print "\n<br>candidatecompounds: "; 	var_dump($candidatecompounds);
      print "\n<br>singletons: "; 					var_dump($singletons);
    }
    
    // Now check each compound against the ontology of the SRC engine.
		$validatedCompounds = array();
		
		foreach(array_merge($usercompounds, $candidatecompounds) as $compound) {
			foreach($remoteLODstores as $$remoteLODstore)
			{
				$termValidation = $this->validateTermInRemoteLODstore($compound,$remoteLODstore);
				if ($termValidation ) {
					$validatedCompounds[$termValidation[1]] = $termValidation[0];
				}
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
			foreach($remoteLODstores as $$remoteLODstore)
			{
				if ($termValidation = $this->validateTermInRemoteLODstore($validSingleton,$remoteLODstore)) {
					$validatedCompounds[$termValidation[1]] = $termValidation[0];
				}
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
			//$allTermUris .= $uri . RodinRDFResult::$TERM_SEPARATOR;
			$allTermLabels[] = cleanup_commas($this->code_binding(clean_puntuation($label))) ;
		}
		
		//$allTermLabels = cleanup_commas($allTermLabels);
		//$allTermUris = cleanup_commas($allTermUris);
		
		//return array($allTermLabels, $allTermUris);
		return $allTermLabels;
	} // get_validated_english_candidate_compounds
	
	
	
	/**
	 * Extracts the terms that are already compounds in the list and puts them on the
	 * compound object. Returns an assosiative array with keys 'candidatecompounds',
	 * 'usercompounds', 'singletons', which values are arrays of arrays of words. Arrays
	 * of words should correspond to the words composing a compound term, and in the case
	 * of 'singletons' they should have only one string.
	 */
	private function tokenizeCandidateCompounds($terms) {
		$result = array();
		$SRCDEBUG=1;
		
		if ($SRCDEBUG)
			print "<br>tokenizeCandidateCompounds($terms) ...";
		// if compounds were already delimited
		$chunks = explode(',', $terms);
		
		//If no comma => one element => then explode by space	
		
		if (count($chunks)==1 && count(explode(' ',trim($chunk)))>1)
			$chunks = explode(' ',$terms); // find each compound input (a b c d)
		
		
		//Extract the last chunk to the $restterms if they contain a blank (=2 or more words)
		if (preg_match('/ /',trim($chunks[count($chunks) - 1]))) {
			if ($SRCDEBUG) print "<br>pop last chunks because contains blank = (".$chunks[count($chunks) - 1].") words";
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
	
	
		/**
		 * Validates the term as is using a search inside each given LOD store
		 * @param string $term - text to be tested inside each LOD store
		 * @param vector $LODstoreInf - a vector of LOD store information (at the moment unused - only DBPEdia)
		 */
		protected function validateTermInRemoteLODstore($term, $LODstoreInf, $lang = 'en') 
		{
			$DEBUG=1;
			if($DEBUG) {
				print "<br>validateTermInRemoteLODstore($term,(only DBPedia),$lang='en') ...";
		}
		
		$foundTerm = $this->checkterm_in_dbpedia($term,'X',$lang);
		
    if($DEBUG) {
			print "\n<br>checkterm_in_dbpedia($term): ";var_dump($foundTerm);
		}
		
		if ($foundTerm != '') {
			return array(str_replace('_', ' ', $foundTerm), base64_encode('http://dbpedia.org/resource/Category:' . $foundTerm));
		} else {
			return false;
		}
		
	}
	
	
	private function checkterm_in_dbpedia($term, $SearchType='X', $lang) {
		$term = $this->suggest_dbpedia_term($term, $lang, 1);
		return $term; 
	}
	
	private function suggest_dbpedia_term($term, $lang, $maxterms = 1) 
	{
		$DEBUG=1;	
		$suggested_term = $this->scrap_wikipedia_suggestion($term);
		
    if($DEBUG) {
			print "<br>suggest_dbpedia_term($term) ...= ($suggested_term)";
			print "<br>suggest_dbpedia_term redirected_term=$redirected_term";
		}
    
		// Enough already? Disambiguate useless
		// $redirected_term = get_dbpedia_redirect($suggested_term,$lang);
		
		if (!$redirected_term) {
			$redirected_term = $suggested_term;
		}
		
    if($DEBUG) {
			print "\n<br>suggest_dbpedia_term redirected_term=$redirected_term";
		}
    
		// Disambiguate and keep the first $maxterms results only
		$disambiguated_terms = wikipedia_disambiguate($redirected_term, $lang);
    
    if($DEBUG) {
			print "\n<br>suggest_dbpedia_term disambiguated_terms: "; var_dump($disambiguated_terms);
		}
    
		$disambiguated_terms = array_slice($disambiguated_terms, 0, $maxterms);

    if($DEBUG) {
			print "\n<br>suggest_dbpedia_term disambiguated_terms: "; var_dump($disambiguated_terms);
		}
			
		$res_disambiguated_terms = implode(RodinRDFResult::$TERM_SEPARATOR, $disambiguated_terms);
    
    if($DEBUG) {
			print "\n<br>suggest_dbpedia_term res_disambiguated_terms: ($res_disambiguated_terms)";
		}

		$res_disambiguated_terms = cleanup_commas($res_disambiguated_terms);
    
    if($DEBUG) {
			print "\n<br>suggest_dbpedia_term cleaned up commas: res_disambiguated_terms: ($res_disambiguated_terms)";
		}
    
		
		if ($DEBUG) {
			print "<br><b>find_dbpedia_terms($term)</b>";
			print "<hr>$term -> suggested:<em>$suggested_term</em> -> redirected:<em>$redirected_term</em> ->disambiguated:<em>$res_disambiguated_terms</em><hr>"; 
			//print "<hr>Aus $term -> suggested:$suggested_term -> first disambiguated:$disambiguated_term<hr>"; 
			print "<br><b>All disambiguated terms:";
			if (count($disambiguated_terms))
				foreach($disambiguated_terms as $disambiguated_term)
					print "<br><a href='http://dbpedia.org/resource/$disambiguated_term' target=_blank>http://dbpedia.org/resource/$disambiguated_term</a>";
			print "<br><b>$maxterms choosen and returned terms:";
			if (count($partial_disambiguated_terms))
				foreach($partial_disambiguated_terms as $disambiguated_term)
					print "<br><a href='http://dbpedia.org/resource/$disambiguated_term' target=_blank>http://dbpedia.org/resource/$disambiguated_term</a>";
		}	
		
		return $res_disambiguated_terms;	
	}
	
	/**
	 * Checks in Wikipedia if the given $query corresponds to the name
	 * of an article. If not, then Wikipedia usually proposes a 'Did you mean?'
	 * when a search is made with the $query, the proposition is returned
	 * instead of the $query.
	 */
	private function scrap_wikipedia_suggestion($query) 
	{
	 	$SRCDEBUG=1;
		$WIKIPEDIASEARCH2=RodinRDFResult::$WIKIPEDIASEARCH2;
		$WIKIPEDIABASEURL=RodinRDFResult::$WIKIPEDIABASEURL;
		
		$treated_query=	$this->code_binding($query);

		if ($SRCDEBUG) {
			print "<br>scrap_wikipedia_suggestion in $WIKIPEDIABASEURL/$treated_query ...";
		}
		if (url_exists("$WIKIPEDIABASEURL/".$treated_query)) {
			
			if ($SRCDEBUG) print "<br>EXISTS: $WIKIPEDIABASEURL/$query";
			
			$suggestion = $query;
		} else {
			
			if ($SRCDEBUG) print "<br>CALLING: ". $WIKIPEDIASEARCH2 . "&search=$query";
			$WIKIPEDIARESPONSE = get_file_content($WIKIPEDIASEARCH2 . "&search=$query");
			$w = str_get_html($WIKIPEDIARESPONSE);

			//Was liegt an: Page found oder didyoumean?
			$a = $w->find('a[title="Special:Search"]');

			if (count($a) == 0)
			$suggestion = $query;
			else
			$suggestion = $a[0]->plaintext;
		}
			
		$suggestion = ucfirst($suggestion);
			
		if ($SRCDEBUG) {
			print "returning: $suggestion";
		}

		return $suggestion;
	}
	
	
	 /**
  * Formats $term as in DBpedia, first letter capital.
	*/
	protected function formatAsInThesaurus($term) {
		return ucfirst(strtolower($term));
	}

	
	
	private function code_binding($terms) 
	{
		$res = str_replace(' ', '_', $terms);
		
		return trim($res);
	}
	
	
	
	
	
	/**
	 * For each active thesaurus: gather skos information on the subjects
	 * Returns $skos_subject_related = 
	 * 
	 * @param vector $subjects
	 * @param string $sid
	 * @param integet $USER_ID
	 * @param vector $NAMESPACES
	 * @param string $lang
	 */
	public function expand_related_subjects_using_thesauri(&$subjects,$sid,$USER_ID,&$NAMESPACES,$lang)
	{
		if ($DEBUG) {
			print "<br>expand_related_subjects_using_thesauri on the following subjects:";
			foreach($subjects as $s) if ($s) print "<br>&nbsp;$s";
		}
		
		$subject_count=count($subjects);
		Logger::logAction(27, array('from'=>'expand_related_subjects_using_thesauri','msg'=>"Started with $subject_count subjects"));
		
		global $VERBOSE;
		global $SRCDEBUG;
		global $RDFLOG;
		$MAXSUBJECTSRELATED_RESULTS=5;
		//$VERBOSE=0; $SRCDEBUG=0;
		$MAXRESULTS=5;
		global $DOCROOT,$WEBROOT,$RODINSEGMENT,$RODINROOT;
		global $SRC_SEARCH_MAX;
		global $TERM_SEPARATOR; 
		if (!$TERM_SEPARATOR) $TERM_SEPARATOR=',';
		
		$INITIALISED_SRCs = $this->get_THESAURI_RECORDS($USER_ID,$lang);

		if ($DEBUG) $RDFLOG.= "<br>".count($INITIALISED_SRCs)." USED SRCs for related subjects !";

		if (count($INITIALISED_SRCs))
		foreach($INITIALISED_SRCs as $INITIALISED_SRC)
		{
			
			//list($src_name,$CLASS,$pathSuperClass,$pathClass,$path_sroot,$path_SRCengineInterface,$path_SRCengine) = $INITIALISED_SRC;
			
			list(	$src_name,
						$IS_SPARQL_ENDPOINT,
						$sds_sparql_endpoint,
						$sds_sparql_endpoint_params,
						$CLASS,
						$pathSuperClass,
						$pathClass,
						$path_sroot,
						$path_SRCengineInterface,
						$path_SRCengine,
						$AuthUser,
						$AuthPasswd,
						$ID,
						$Protocol,
						$Server,
						$Port,
						$Path_Start,
						$Path_Refine,
						$Servlet_Start,
						$Servlet_Refine,
						$src_parameters ) = $INITIALISED_SRC;
			
			
			//unset($SRCINSTANCE->refine_skosxl_solr);
			//unset($SRCINSTANCE->refine_skos_solr);
			
			###################################
			#
			# VALIDATION $src_parameters from SRC_management record:
			#
			eval($src_parameters); // $max_triples=11; $max_docs=5; $max_subjects=5;
			$valid_src_parameters=true;
			if (!$max_subjects)
			{
				global $USER_ID,$SRCLINKBASE;
				$SRCLINK="$SRCLINKBASE/select_src.php?nl=1&u=$USER_ID&showuser=$USER_ID&filter={$src_name}";
				$SRCHREF="<a href=$SRCLINK target='_blank' title='Click to open SRC_Management on the concerning record'>SRC_management</a>";
				$ERRORTXT="<br>Error in src_parameters for '{$src_name}' concerning '\$max_subjects' - zero value provided."
									."<br>Please provide a value like '\$max_subjects=n;' (n>0) in $SRCHREF in field 'src_parameters'";
				fontprint($ERRORTXT,'red');
				$valid_src_parameters=false;
				Logger::logAction(27, array('from'=>'expand_related_subjects_using_thesauri','msg'=>$ERRORTXT));
				$RDFLOG.="<br>$ERRORTXT";
			}
			#
			###################################
			
			
			if ($valid_src_parameters)
			{
				Logger::logAction(27, array('from'=>'expand_related_subjects_using_thesauri','msg'=>"Starting $src_name on $subject_count subjects (".implode('+',$subjects).") extracting further (SKOS) $max_subjects subjects"));
				$RDFLOG.="<br>Compute max $max_subjects subjects from Thesaurus $src_name for $subject_count subjects (".implode('+',$subjects).")";
				
				###########################
				if ($IS_SPARQL_ENDPOINT)
				###########################
				{
					global $SRC_MAXRESULTS;
					$processed_subjects=array();
					foreach ($subjects as $s)
					{
						$s = trim(strtolower($s));
						if ($s<>'')
						{
								
							list($still_to_process,$subsuming_subject,$numdocs) = $this->subject_is_still_to_process($s,$processed_subjects);
							if ($still_to_process)
							{
								$expanded_subjects =
										array($src_name,
													$broader	=array(),
													$narrower	=array(),
													$related	=get_related_subjects_from_sparql_endpoint($s,$src_name,$sds_sparql_endpoint,$sds_sparql_endpoint_params,$NAMESPACES,$lang,$max_subjects));
							
								add_to_assocvector($skos_subject_related,$s,$expanded_subjects);					
								$processed_subjects{$s}=count($broader)+count($narrower)+count($related); 
							}
							else $RDFLOG.=htmlprint("<br>SUPPRESS (sparql) Subject '{$s}' because subsumed by '{$subsuming_subject}' having already $numdocs results",'red');
						}
					}
				}
				else {
									$WEBSERVICE_q=$WEBROOT."$RODINROOT/$RODINSEGMENT/app/s/refine/index.php"
											.'?'.'sid='.$sid
											.'&'.'cid=0'
											.'&'.'cid=0'
											.'&'.'action=preall'
											.'&'.'v=' // must be empty = use q without preprocessing
											.'&'.'sortrank=standard'
											.'&'.'w=0'
											.'&'.'m='.$max_subjects
											.'&'.'l='.$lang
											.'&'.'c=c'
											.'&'.'service_id='.$ID
											.'&'.'user='.$USER_ID
											.'&'.'q='
											;
					
					//Call the refine method inside $CLASS
					if ($DEBUG) $RDFLOG.= "<hr>";
					
					$broder_arr=$narrower_arr=$related_arr=array();
					foreach ($subjects as $s)
					{
						if (trim($s))
						{
							list($still_to_process,$subsuming_subject,$numdocs) = $this->subject_is_still_to_process($s,$processed_subjects);
							if ($still_to_process)
							{
							//In case the refine method gets some results, 
							//do not ask the same service again for a subject 
							//which is contained in the previously sserved call if successful!
								$s64 = base64_encode($s);
								$WEBSERVICE = $WEBSERVICE_q.$s64;
								//print "<br>Calling internal webservice for subject ($s) and class <b>$CLASS</b>:<br>".htmlentities($WEBSERVICE);
								
								$CONTENT=get_file_content($WEBSERVICE);
								//print "<br>CONTENT: ".htmlentities($CONTENT);
								$expanded_subjects = 
								list($src_name,$broader,$narrower,$related) = 
											$this->scan_src_results($CONTENT,$TERM_SEPARATOR,$src_name,$s,$WEBSERVICE_q);
								
								add_to_assocvector($skos_subject_related,$s,$expanded_subjects)	;					
								//$skos_subject_related{$s} = $expanded_subjects;
								$processed_subjects{$s}=$max_bnr=count($broader)+count($narrower)+count($related); 
													
							}
							else $RDFLOG.=htmlprint("<br>SUPPRESS (src) Subject '{$s}' because subsumed by '{$subsuming_subject}' having already $numdocs results",'red');	
						}
					}
				}
	
				Logger::logAction(27, array('from'=>'expand_related_subjects_using_thesauri','msg'=>"Exit $src_name with $max_subjects subjects"));
			} // foreach($INITIALISED_SRCs as $INITIALISED_SRC)
		} // valid_src_parameters
		Logger::logAction(27, array('from'=>'expand_related_subjects_using_thesauri','msg'=>'Exit'));
		
		return $skos_subject_related;
		
	} // get_subject_related_to_from_thesauri
	
	
	
	/**
	 * list($broder_arr,$narrower_arr,$related_arr) = scan_src_results($CONTENT,$TERM_SEPARATOR)
	 */
	public function scan_src_results($CONTENT,$TERM_SEPARATOR,$src_name,$subject,$urlcall)
	{
		$DEBUG=0;
		
		$broder_arr=$related_arr=$narrower_arr=array();
		//In the following statement the flag LIBXML_NOCDATA is mandatory for reading and processing CDATA sections:
		$valid_xml=true;
		if (datasource_error($CONTENT,$src_name))	
		{
			fontprint("<hr>$src_name error"
								."<br>CACHE checked ?"
								."<br>On subject ($subject) "
								."<br>Used url: <b>".htmlentities($urlcall)."</b>"
								,'red');
			$valid_xml=false;
		} 
		
		if ($valid_xml)
		{
			
			$sxmldom = simplexml_load_string($CONTENT,'SimpleXMLElement', LIBXML_NOCDATA);
			if ($sxmldom)
			{
				//Extract $RESULTS_B, $RESULTS_N, $RESULTS_R
				$broader64_ = $sxmldom->xpath("/refine/srv/broader"); //find the (CDATA) doc list results
				$broader64 = trim($broader64_[0]);
				$narrower64_ = $sxmldom->xpath("/refine/srv/narrower"); //find the (CDATA) doc list results
				$narrower64 = trim($narrower64_[0]);
				$related64_ = $sxmldom->xpath("/refine/srv/related"); //find the doc (CDATA) list results
				$related64 = trim($related64_[0]);
				
				
				if($broader64)
				{
					$broader= (base64_decode(($broader64)));
					$broder_arr=explode($TERM_SEPARATOR,$broader);
					if ($DEBUG) 
					{
						print "<br><b>Broader:</b>";
						foreach($broder_arr as $b) print "<br>$b";
					}
				}
		
				if($narrower64)
				{
					$narrower= (base64_decode(($narrower64)));
					$narrower_arr=explode($TERM_SEPARATOR,$narrower);
					if ($DEBUG) 
					{
						print "<br><b>Narrower:</b>";
						foreach($narrower_arr as $b) print "<br>$b";
					}
				}
		
				if($related64)
				{
					$related= (base64_decode(($related64)));
					$related_arr=explode($TERM_SEPARATOR,$related);
					if ($DEBUG) 
					{
						print "<br><b>Related:</b>";
						foreach($related_arr as $b) print "<br>$b";
					}
				}
			}
		} // valid_xml
		return array($src_name,$broder_arr,$narrower_arr,$related_arr);
	} // scan_src_results
	
	
	
	
	
	/**
	 * Using the attached LOD sources
	 * Using the related-to subject won by rdfize
	 * Calculate the documents related to these subjects 
	 * LOD-Link these documents inside the local result rdf store
	 * Store these documents in the local store for 
	 * Suggestions
	 * 
	 * CACHE CALLS
	 * 
	 * For each present subject $sx in ($this->getDCSubjects())
	 * 
	 * if $sx is not contained in $processed_subject or
	 *    $sx is contained in $processed_subject and $processed_subject{$sx}=0 (no docs fetched)
	 * then
	 * Fetch documents records for this subject $sx, 
	 * register the subject into $processed_subject{$sx}=Number of docs fetched.
	 * 
	 * Only labels in the same language as $searchterm are considered.
	 */
	public function rdfLODfetchDocumentsOnSubjects($sid,$datasource,$searchterm,$USER_ID)
	{
		global $RDFLOG;
		$entry = microtime_float();
		$CLASS=get_class($this);
		if (!$CLASS::$searchtermlang)
			$CLASS::$searchtermlang = detectLanguage($searchterm);
		$lang=$CLASS::$searchtermlang;
		
		$processed_subjects = array();
 		Logger::logAction(27, array('from'=>'rdfLODfetchDocumentsOnSubjects','msg'=>'Started'));
		
		$subj_start = microtime_float();
		$subjects_labels=$this->getDCSubjects();
		$subj_end = microtime_float();
		$ubj_elapsed+= ($subj_end - $subj_start);
		
		$sl_count=count($subjects_labels);
		
		$RDFLOG.="<ht>FETCHING LOD Documents using $sl_count subjects ...";
		
		if (is_array($subjects_labels) && count($subjects_labels))
		{
			foreach($subjects_labels as $l) $RDFLOG."<br>subj $l";
			
			$lods_start = microtime_float();
			$LOD_SOURCES=get_active_LOD_expansion_sources($USER_ID);
			$lods_end = microtime_float();
			$lods_elapsed+= ($lods_end - $lods_start);
			
			$LOD_SOURCES_RECORDS= $LOD_SOURCES['records'];
			
			if (is_array($LOD_SOURCES_RECORDS) && count($LOD_SOURCES_RECORDS))
			{
				foreach($LOD_SOURCES_RECORDS as $LOD_SOURCES_RECORD)
				{
					$sds_name=$LOD_SOURCES_RECORD['Name'];
					$sds_sparql_endpoint=$LOD_SOURCES_RECORD['sparql_endpoint'];
					$sds_sparql_endpoint_params=$LOD_SOURCES_RECORD['sparql_endpoint_params'];
					$sds_parameters= $LOD_SOURCES_RECORD['src_parameters'];
					eval($sds_parameters); // expecting: $max_triples=10; $max_docs=5; $max_subjects=5;
					$sds_url_base="$sds_sparql_endpoint?$sds_sparql_endpoint_params";
					
					$RDFLOG.= "<br> Expanding RDF subject using LOD source <b>$sds_name</b> ($sds_url_base) $max_triples max_triples";
					
					$cached_time=$open_time=0;
					$timeelapsed_microsec=0;
					$cache_used = $open_used=0;
					$import_elapsed=0;
					foreach($subjects_labels as $subject)
					{
						$toproc_start = microtime_float();
						list($still_to_process,$subsuming_subject,$numdocs) = $this->subject_is_still_to_process($subject,$processed_subjects);
						$toproc_end = microtime_float();
						$toproc_elapsed+= ($toproc_end - $toproc_start);
						
						
						if ($still_to_process)
						{
							$RDFLOG.= "<br>Fetching document triples for subject '$subject'";
							$mtime1 = microtime_float();
							list($triples,$used_cache)
											 = get_triples_on_subject_from_sparql_endpoint(	$subject,
																																			$sds_name,
																																			$sds_sparql_endpoint,
																																			$sds_sparql_endpoint_params,
																																			RodinRDFResult::$NAMESPACES,
																																			$max_triples);
							$timeelapsed_microsec= microtime_float() - $mtime1;
																									
							$RDFLOG.="<br>$timeelapsed_microsec - LOD call $sds_name on subject '$subject'";
							if ($used_cache) 
							{
								$cached_time+=$timeelapsed_microsec;
								$cache_used++;
							}
							else {
								$open_used++;
								$open_time+=$timeelapsed_microsec;
							}
								
							$otriplescount=count($triples);	
																					
																																		
							$homog_start = microtime_float();
							list($homogenized_triples,$htriplescount,$hdocscount)
												= $this->homogenize_foreign_triples($triples,$sds_name,$lang);
							$homog_end = microtime_float();
							$homog_elapsed+=($homog_end - $homog_start);
							$processed_subjects{$subject}=$hdocscount; 
							
							$RDFLOG.= "--> $hdocscount docs in ($otriplescount) $htriplescount homogenized triples imported";
							
							$import_start = microtime_float();
							$this->import_triples($homogenized_triples);
							$import_end = microtime_float();
							$import_elapsed+=$import_end - $import_start;
					  } // $processed_subjects
						else {
						if ($DEBUG || 1)
							$RDFLOG.="<br>SUPPRESS LOD document fetch on subject ($subject), since there was a more complicated one ($subsuming_subject) having $numdocs document(s).";
						}
					} // foreach($subjects_labels 
					$datasource_LOD_fetch_statistics{$sds_name}=array($sl_count,$cache_used,$cached_time,$open_used,$open_time,$import_elapsed);
					
				} // foreach($LOD_SOURCES_RECORDS
			}
			else
				$RDFLOG.= "<br> NO LOD sources (yet) used to expand result rdf information";

		}
		$exit = microtime_float();
		$elapsed=$exit-$entry;
		//Print statistics:
		$RDFLOG.= "<hr>rdfLODfetchDocumentsOnSubjects STAT<br>applied at (".($cache_used+$open_used)."/".$sl_count.") subjects";
		$RDFLOG.= "<br> computation in $elapsed secs";
		
		$homog_elapsed_pro = round($homog_elapsed/$elapsed*100,1);
		$lods_elapsed_pro = round($lods_elapsed/$elapsed*100,1);
		$ubj_elapsed_pro = round($ubj_elapsed/$elapsed*100,1);
		
		$RDFLOG.= "<br> - inside subjects get took $ubj_elapsed secs = $ubj_elapsed%";
		$RDFLOG.= "<br> - inside 'lods_elapsed' took $lods_elapsed secs = $lods_elapsed_pro";
		$RDFLOG.= "<br> - inside homogenization took $homog_elapsed secs = $homog_elapsed_pro%";
				
			
		
		foreach($datasource_LOD_fetch_statistics as $src_name=>$STAT)
		{
			list($subjects_labels,$cache_used,$cached_time,$open_used,$open_time,$import_elapsed) = $STAT;
			$RDFLOG.= "<br><br>STAT $src_name: ";

			$cached_time_pro = round($cached_time / $elapsed*100,1);
			if ($cache_used) $RDFLOG.= "<br> $cache_used times cache_used ($cached_time sec = $cached_time_pro%)";

			$open_time_pro = round($open_time / $elapsed*100,1);
			if ($open_used) $RDFLOG.=htmlprint( "<br> $open_used LOD remote calls ($open_time sec = $open_time_pro%)",'red');

			$import_elapsed_pro= round($import_elapsed / $elapsed*100,1);
			$RDFLOG.= " + import time elapsed ($import_elapsed sec = $import_elapsed_pro%)";
		} // stat

 		Logger::logAction(27, array('from'=>'rdfLODfetchDocumentsOnSubjects','msg'=>'Exit'));
	} // rdfLODfetchDocumentsOnSubjects
	
	
	
	
	
	/**
	 * if $sx is not contained in $processed_subject or
	 *    $sx is contained in $processed_subject and $processed_subject{$sx}=0 (no docs fetched)
	 * then true
	 * @param $subject
	 * @param $processed_subjects
	 */
	public function subject_is_still_to_process($subject,&$processed_subjects)
	{
		$subsumed=false;
		$subsuming='';
		$fetched_docs=0;
		if (count($processed_subjects))
		foreach($processed_subjects as $s=>$docs)
		{
			if (strstr($s,$subject) && $docs > 0)
			//A more complicated subject delivered documents.
			//do not process
			{
				$subsumed=true;
				$subsuming=$s;
				$fetched_docs=$docs;
				break;
			}
		}
		
		//If subject was not subsumed, 
		//it must be processed:
		return array(!$subsumed,$subsuming,$fetched_docs);
	} // subject_is_still_to_process
	
	
	
	
	
	/**
	 * Returns an array with dcsubjects from the current local store for this result
	 */
	public function getDCSubjects()
	{
		$subjects_labels=array();
		if (RodinRDFResult::$store)
		{
			$dce_ns_url=RodinRDFResult::$NAMESPACES{'dce'};
			$rdf_ns_url=RodinRDFResult::$NAMESPACES{'rdf'};
			$rodin_ns_url=RodinRDFResult::$NAMESPACES{'rodin'};
			
			$sparql_query=<<<EOS
			PREFIX dce:  	<$dce_ns_url>
			PREFIX rdf: 	<$rdf_ns_url>
			PREFIX rodin: <$rodin_ns_url>
						select ?l 
			{
				?s rdf:type dce:subject .
				?s rodin:label ?l .
			} 
EOS;
			//print "<br>querying: ".htmlentities($sparql_query);
			if (($rows = RodinRDFResult::$store->query($sparql_query, 'rows')))
			{
				foreach($rows as $row)
				{
					$subjects_labels[] = $row['l'];
				} // foreach
			}	// results
		} //store
		return $subjects_labels;
	} // getDCSubjects
	
	
	
	/**
	 * Returns the triples passed with some 
	 * specific needed transformation
	 * in order to be used inside the RODIN SPACE
	 * accept titles, descriptions and subject literal 
	 * only if in the same language as the searchterm
	 * 
	 * @param triples array $triples - the triples
	 * @param string $src_name - The name of the refining component
	 */
	public function homogenize_foreign_triples(&$triples,$src_name,$lang)
	{
		global $RDFLOG;
		$hdocscount=0;
		$htriplescount=0;
		$CLASS=get_class($this);
		if (!$CLASS::$searchtermlang)
			$CLASS::$searchtermlang = detectLanguage($searchterm);
		$lang=$CLASS::$searchtermlang;
		
		//print "<br>homogenize_foreign_triples for ($src_name)";
		switch (strtolower($src_name))
		{
			#########################################
			case 'europeana';
			#########################################
			if (is_array($triples) && count($triples))
			{
				foreach($triples as $triple)
				{
					list($s,$p,$o) = $triple;
					$add_triple=true;
					//print "<br>HOMOG triple ($s)($p)($o)";
					
					$new_p=$p; $new_o=$o;
					if (strstr($s,'epp:'))
						$new_s='rodin_e:'.str_replace('epp:','',$s);
					
					if ($p=='rdf:type' && $o=='ore:terms/Proxy')
					{
						//change this triple: We need the "real" type. not a proxy trick like in europeana	
						$homogenized_triples[]=array($new_s,$new_p,'dce:BibliographicResource');
						$hdocscount++;
					}
					else if ($p=='dce:title' 
								|| $p=='dce:description' )
					{
						//Only the SAME language!
						$langt='';
						if (($langt=detectLanguage($new_o))==$lang)
						{
							if($DEBUG || 1)  
										$RDFLOG.= "<br>TAKE($langt==$lang) OBJ($new_o):";
						}
						else 
						{
							$add_triple=false;
							if($DEBUG || 1)  
										$RDFLOG.= htmlprint("<br>SUPPRESS($langt<>$lang) OBJ($new_o):",'red');
						}					
											
					}				
					else if ($p=='dce:subject')
					{
							//TODO: LINK work with subject 
							//is such a subject=$o in rodin?
							//print "<br>PROC dce:subject: $o";
							$o=l_inverse($o); //eliminate douple quote from literal
							if (!($subject_uid=get_triple_subject('rodin:label',$o,$this)))
							{
								//$o should not contain double quote...
								//Construct a rodin subject
								$subject_uid='rodin:'.RodinRDFResult::adapt_name_for_uid($o);
								//Generate a rodin:label $o 
								$homogenized_triples[]=array($subject_uid,	'rodin:label', 	l($o)); 
								$homogenized_triples[]=array($subject_uid,	'rdf:type', 	'dce:subject'); 
							}
							$homogenized_triples[]=array($new_s,	$new_p, 	$subject_uid); 
							$add_triple=false;
					} //dce:subject 
					
					//TODO: check doubles work ...	
					//TODO: Link Work with subject
					//TODO: Link Subject (if possible or create)
					//Is the subject literal linked with a rodin-literal (subject)?
					//if yes: change the literal to a rodin: link to this element
					//if not: create a rodin:subject to this literal (to be related with that subject)
	
					if ($add_triple)
								$homogenized_triples[]=array($new_s,$new_p,$new_o);
				}	// foreach $triples		
			}
			break; //europeana
			#########################################
			default: $homogenized_triples=$triples;
		}
				
		return array($homogenized_triples,count($homogenized_triples),$hdocscount);
	} // homogenize_foreign_triples
	
	
	
	
	
	
	/**
	 * $INITIALISED_SRCs = initialize_SRC($USER_ID)
	 * @param $USER_ID - the user id for which some SRC's are activated
	 */
	public function get_THESAURI_RECORDS($USER_ID,$lang)
	{
		$DEBUG=0;
		global $DOCROOT;
		$initialised_src=0;
		$INIT_SRC_OBJ = get_active_THESAURI_expansion_sources( $USER_ID );
		
		$SRCs = $INIT_SRC_OBJ['records'];
		$NoOfUsableSRC=count($SRCs);
		
		$i=-1;
		if(count($SRCs))
		foreach($SRCs as $SRC)
		{
			$i++;
			$src_name=$SRC['Name'];
			$src_path=trim($SRC['Path_Refine']); // = /rodin/eng/fsrc/app/engine/SOZengine/SOZengineSOLR
			$sds_sparql_endpoint=trim($SRC['sparql_endpoint']); // = /rodin/eng/fsrc/app/engine/SOZengine/SOZengineSOLR
			$src_sparql_endpoint_params=trim($SRC['sparql_endpoint_params']); // = /rodin/eng/fsrc/app/engine/SOZengine/SOZengineSOLR
						
			//Is this a classical SRC or nonSKOS a sparql endpoint ?
			$IS_SPARQL_ENDPOINT=($src_path=='' && $sds_sparql_endpoint<>'');
			
			$CLASS=basename($src_path);
			$SUPERCLASS=basename(dirname($src_path));
			$DISKenginePATH=$DOCROOT.str_replace("$SUPERCLASS/$CLASS",'',$src_path);
			$DISKfsrcPATH=dirname($DISKenginePATH);
			
			// Include class paths for SRC ENGINES
			if ($i==0)
			{
				$path_sroot=$DISKfsrcPATH."/sroot.php";
				$path_SRCengine=$DISKenginePATH.'SRCengine.php';
				$path_SRCengineInterface=$DISKenginePATH.'SRCengineInterface.php';
				//if (!chdir($DISKenginePATH)) fontprint("Problem chdir $path_SRCengine" , 'red');
				//require_once($path_sroot);
				//require_once($path_SRCengineInterface);
				//require_once($path_SRCengine);
			}
			
			
			$pathSuperClass=$DISKenginePATH.$SUPERCLASS."/$SUPERCLASS.php";
			//include_once($pathSuperClass);
			
			$pathClass=$DISKenginePATH.$SUPERCLASS."/$CLASS/$CLASS.php";
			//include_once($pathClass);
			
			$AuthUser				=$SRC['AuthUser'];
			$AuthPasswd			=$SRC['AuthPasswd'];
			$ID							=$SRC['ID'];
			$Protocol				=$SRC['Protocol'];
			$Server					=$SRC['Server'];
			$Port						=$SRC['Port'];
			$Path_Start			=$SRC['Path_Start'];
			$Path_Refine		=$SRC['Path_Refine'];
			$Path_Test			=$SRC['Path_Test'];
			$Servlet_Start	=$SRC['Servlet_Start'];
			$Servlet_Refine	=$SRC['Servlet_Refine'];
			
			$src_parameters	=$SRC['src_parameters'];
			
			
			$initialised_src++;
			if ($DEBUG)
			{
				print "<hr> Name:".$src_name;
				print "<br> CLASS: ".$CLASS;
				print "<br> SUPERCLASS: ".$SUPERCLASS;
				print "<br> PATH: ".$src_path;
				print "<br> DISKfsrcPATH: ".$DISKfsrcPATH;
				print "<br> DISKenginePATH: ".$DISKenginePATH;
			}
			
			$INITIALISED_SRCs[]= array(	$src_name,
																	$IS_SPARQL_ENDPOINT,
																	$sds_sparql_endpoint,
																	$sds_sparql_endpoint_params,
																	$CLASS,
																	$pathSuperClass,
																	$pathClass,
																	$path_sroot,
																	$path_SRCengineInterface,
																	$path_SRCengine,
																	$AuthUser,
																	$AuthPasswd,
																	$ID,
																	$Protocol,
																	$Server,
																	$Port,
																	$Path_Start,
																	$Path_Refine,
																	$Servlet_Start,
																	$Servlet_Refine,
																	$src_parameters
																	);
			
		} // foreach $SRC
		return $INITIALISED_SRCs;
	} // init_SRC
	
	
	
} // RodinRDFResult
