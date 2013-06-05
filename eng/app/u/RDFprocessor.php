<?php

/**
 * RDF PROCESSOR
 * 
 * Creation and maintenance of RDF content for RODIN
 * @date Mai 2013
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
$filename="gen/u/arc/ARC2.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
			

$filename="app/u/LanguageDetection.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}


$filename="/u/RodinResult/RodinResultManager.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}




class RDFprocessor {
	
	public  $sid= null;
	public  $USER_ID= null;
	public  $searchterm				= null; 
	public  $searchtermlang		= null; 
	public  $searchuid				= null;
	public  $storename 				= null; // Name of store where to insert triples
	public  $store 						= null; // ARC localstore Config obj
	public  $rodin_search_subjects = null;
	public  $rodin_result_subjects = null;
	public  $rodin_flattened_expanded_result_subjects = null;
	public  $rodin_ranked_expanded_result_subjects = null;
	public  $rodin_reference_subjects_text = null;
	public 	$rodin_results 	= null;
	public  $LOCALARCCONFIG	= null;
	public  $one_work 			= null; // root node for triples page
	public  $stopwords			= null;
	public  $languages 			= null;
	public 	$requesting_host= null;
	
	public  static $importGraph			  = null;
	public  static $submlt_collection = 'subject_ranking'; //SOLR collection used for ranking
	public  static $querysubjects_servicename ='queryexp';
	public  static $relatedsubjects_servicename ='subexp';
	public  static $lodfetch_servicename ='lodfetch';
	public  static $static_store			= null;
	public  static $ownnamespacename 	='rodin';
	public  static $NAMESPACES 				= null; // namespaces
	public	static $NAMESPACES_PREFIX = null;
	private static $PUBBLICATION_URL 	= null; // to see/navigate/access triples
	public  static $TOBECODED64 			= null; // to be used inside a SPARQL query
	public  static $rdfp_TOLERATED_MIN_SUBJ_LEN = 3; // Discard subjects which are smaller than 3 chars length
	public  static $rdfp_EXTRACT_SUBJECTS_FROM_TITLE = true; // Extract s. from titles if no subjects were provided by the source
	public  static $rdfp_MAX_SRC_SUBJECT_EXPANSION_PRO_SRC = 3; // Allow each SRC a maximum of calls on subject
	public  static $rdfp_MAX_SUBJECT_TOKENIZE = 2; // 2 - discard a subject if it has more than n words
	public  static $rdfp_MAX_SRC_SUBJECT_EXPANSION = 4; // 4 Limit SRC subjects expansion (expand at most n subjects)
	public  static $rdfp_MAX_LOD_SUBJECT_DOCFETCH  = 4; // 3 Limit DOC FETCH expansion to (n) subjects
	public  static $rdfp_MAX_LOD_DOC_ADD  					= 15; // 2 Limit SUGGESTED DOC ADDING to (n) subjects
	public  static $rdfp_THRESHOLD_DATASOURCE_MIN_SUBJECTS = 4; // 4 if less then n subjects, try to read subjetcs from RODIN result title
	public  static $rdfp_TOLERATED_SRC_SOLR_DATA_AGE_SEC =604800; // 1 week RDF STORAGE tolerated data age before removing/recalling/refreshing data from remote source
	public  static $rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC =604800; // 1 week = 604800 secs RDF STORAGE tolerated data age before removing/recalling/refreshing data from remote source
	public  static $rdfp_USE_ARC_SOLR_BRIDGE = 'no'; // ARC triples are also indexed in solr
	
	private static $TERM_SEPARATOR    =',';
	private static $DBPEDIA_BASE="http://dbpedia.org";
	private static $WIKIPEDIABASE="http://en.wikipedia.org";
	private static $DBPEDIA_SPARQL_ENDPOINT = "http://dbpedia.org/sparql";
	private static $WIKIPEDIABASEURL="http://en.wikipedia.org/wiki";
	private static $WIKIPEDIASEARCH= "http://en.wikipedia.org/w/api.php?action=opensearch&format=xml";
	private static $WIKIPEDIASEARCH2="http://en.wikipedia.org/w/index.php?";


	/**
	 * CONSTRUCTOR
	 */
	public function RDFprocessor($sid,$USER_ID,$requesting_host='localhost') 
	{
		$DEBUG=0;
		if (!$USER_ID) { fontprint("<br>Instantiation of RDFprocessor() without USER_ID",'red'); return null; }
		
		//init namespaces & co once for all 
		global $HOST, $RODINSEGMENT;
		global $WANT_RDF_STORE_INITIALIZED_AT_EVERY_SEARCH;
		global $ARCCONFIG;
		global $PROT; //USE SAME RODIN PROTOCOL (http or https)
		global $RODINROOT;
		 		
		if ($sid)
		{
			$this->searchterm = collect_queries_tag($RODINSEGMENT, $USER_ID, $sid);
			//print "<br>searchterm found with ($RODINSEGMENT,$USER_ID, $sid): ".$this->searchterm;
			$this->languages    = array('en','fr','de','it','es');
			$this->searchtermlang	=detectLanguageAndLog($this->searchterm,'RDFprocessor',$sid);
			if (in_array($this->searchtermlang, $this->languages))
				$this->stopwords =get_stopwords_from_db($this->searchtermlang);
			else 
				$this->stopwords =get_stopwords_from_db();
		}
		
		$this->sid 				=	$sid;
		$this->USER_ID		= $USER_ID;
		$this->requesting_host = $requesting_host;
		
		
		$RODINBASE="$PROT://$requesting_host$RODINROOT/$RODINSEGMENT";
		
		if (!RDFprocessor::$importGraph)
				RDFprocessor::$importGraph			="$RODINBASE/lod/";
		if (!RDFprocessor::$PUBBLICATION_URL)
				RDFprocessor::$PUBBLICATION_URL	="$RODINBASE/app/lod";
		
		//print "<br>INITIALIZING RodinRDFResult ... HOST=$HOST RODINSEGMENT=$RODINSEGMENT, PUBBLICATION_URL=".$this->PUBBLICATION_URL;
		
		if (!RDFprocessor::$NAMESPACES)
			RDFprocessor::$NAMESPACES = array(
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
			    'rodin_a'	=> RDFprocessor::$PUBBLICATION_URL.'/resource/a/',//annotation namespace
			    'rodin_e'	=> RDFprocessor::$PUBBLICATION_URL.'/resource/e/',//pubblication external imported e=europeana
			   	'rodin'		=> RDFprocessor::$PUBBLICATION_URL.'/resource/',	//pubblication internal resources
			    			
					// Europeana:
					// see also http://pro.europeana.eu/documents/866205/13001/EDM_v5.2.2.pdf
					'ore' 	=>'http://www.openarchives.org/ore/',
					'epp' =>'http://data.europeana.eu/proxy/provider/',
		     	'eedm' =>'http://www.europeana.eu/schemas/edm/',
					'e'	=> 'http://data.europeana.eu/',
					);

	 //FIX what should be stored base64 encoded (to avoid ARC store special effects ...)
	 if (!is_array(RDFprocessor::$TOBECODED64)) 
	 		RDFprocessor::$TOBECODED64 
	 				= array(
	 						'dce:title'=>true,
	 						'dce:description'=>true,
	 						'foaf:name'=>true,
	 						'rodin:name'=>true,
	 						'dce:creator'=>true,
	 						'rodin_a:cache_id'=>true
	 						);

		// Build NAMESPACES_PREFIX
		if (! RDFprocessor::$NAMESPACES_PREFIX)
		{
			if (is_array(RDFprocessor::$NAMESPACES) && count (RDFprocessor::$NAMESPACES))
				foreach(RDFprocessor::$NAMESPACES as $ns=>$nsurl)
					RDFprocessor::$NAMESPACES_PREFIX.="PREFIX $ns: <$nsurl>\n";	
		}
		
		
		if($this->requesting_host)
		{
			global $PROT, $SOLR_RODIN_CONFIG;
			//Make LOD SPACE dependent from accessing parameters PROTOCOL, HOST
			$RODINHOST4LOD=$PROT.'_'.str_replace('.','_',$this->requesting_host);
			$RODINRDFSTORENAME='lod_'.$RODINSEGMENT.'_'.$RODINHOST4LOD;
			$this->storename = $RODINRDFSTORENAME;
			//print "<br>INITIALIZING STORE ".RDFprocessor::$storename;
			$this->LOCALARCCONFIG=$ARCCONFIG;
	    $this->LOCALARCCONFIG{'store_name'}= $this->storename;
	    $this->LOCALARCCONFIG{'ns'}=RDFprocessor::$NAMESPACES;
	    $this->LOCALARCCONFIG{'segment'}=$RODINSEGMENT;
	    $this->LOCALARCCONFIG{'use_arc_solr'}=(strtolower(RDFprocessor::$rdfp_USE_ARC_SOLR_BRIDGE)=='yes');
	    $this->LOCALARCCONFIG{'solr_collection'}='arc_'.$this->storename;
	    $this->LOCALARCCONFIG{'solr_host'}=$SOLR_RODIN_CONFIG['arc']['adapteroptions']['host'];
	    $this->LOCALARCCONFIG{'solr_port'}=$SOLR_RODIN_CONFIG['arc']['adapteroptions']['port'];
	    $this->LOCALARCCONFIG{'solr_path'}='/solr/'.$this->LOCALARCCONFIG{'solr_collection'}.'/';
	    $this->LOCALARCCONFIG{'solr_core'}=$SOLR_RODIN_CONFIG['arc']['adapteroptions']['core'];
	    $this->LOCALARCCONFIG{'solr_timeout'}=$SOLR_RODIN_CONFIG['arc']['adapteroptions']['timeout'];
	    $this->store = ARC2::getStore($this->LOCALARCCONFIG);
	    if (!$this->store->isSetUp()) {
	       $this->store->setUp();
	    } else
				{
					//Only iff the constructor is called with a result: 
					//and only the first time when not yet initialized: 
					//reset triples in store!
					//10.4.2013 FRI: we DO NOT CLEAR the store any more, we keep every triple inside
					//Annotate imported triple with a timestamp (?and a sid)
					//Annotate every widget result document with the sid / search term / timestamp
					//Renew portions of the RDF Graph following age of Triples (triples which are "too old" are renewed.)
					//In case sid is null an instance of this class was created without the purpose of changing data
					//in the latter case - do not reset the store
					
				}
				
				//DISPLAY W3PAGE NEEDS IT ALSO STATICALLY
				if (!RDFprocessor::$static_store)
			  RDFprocessor::$static_store=$this->store;
			} // $this->requesting_host
			
		} // RDFprocessor (constructor)
	
	
		public function reinit_store()
		{
			Logger::logAction(27, array('from'=>'reinit_store','msg'=>"RDF STORE REINITIALISED"),$this->sid);
			
			$this->store->reset(); // clear store from Triples!
			$this->store->setUp();
		} // reinit_store
	
	
	
	
	/**
	 * Extract subjects out of result information
	 * If no keywords or Subjects slot are provided
	 * by the results, try to compute subjects out of the title
	 * 
	 * 
	 * rdfize each rodin result (and the own subjects)
	 *  works and authors in one
	 * 
	 * return a list of globally computed subjects
	 */
	public function rdfize_extract_subjects($COUNT_TRIPLES=0)
	{
		$DEBUG=0;
		$showsubjects=1;
		global $RDFLOG;
		global $WANT_RDF_ANNOTATION;
		//$WANT_RDF_ANNOTATION=0; // DEBUG
		$added_triples=0;
		$triple= array();

		//For each result corresponding to sid: extract subjects
		$this->rodin_results= RodinResultManager::getRodinResultsForASearch($this->sid,$datasource='',true,false);
		$resultCount = count($this->rodin_results);	
		
		if ($DEBUG) 
		{
			$RDFLOG.="<br>$resultCount results read from db to sid ".$this->sid;
			$RDFLOG.="<br>Using language ".$this->searchtermlang." recognised in (".$this->searchterm.") to extract subjects";
		}
		//$result_id=$this->my_result->getId();
			
		Logger::logAction(27, array('from'=>'rdfize_extract_subjects','msg'=>'Started with sid:'.$this->sid),$this->sid);
		if ($COUNT_TRIPLES) $count_triples_before=count_ARC_triples($this->store);
		#####################################################################
		#
		# Annotation section
		#
		# Since new data should here be annotated, old data should be firstly removed
		#
		
		if ($WANT_RDF_ANNOTATION)
				list($searchuid,$timestamp,$triple) = $this->rdf_reannotate_search($this->sid,$this->searchterm,$triple);

		$this->searchuid = $searchuid;

		$searchterm_subjects = $this->extract_subjects_from_term($this->searchterm,$this->searchtermlang);
		//Construct global subject UIDs for later use!
		foreach($searchterm_subjects as $slabel)
			$this->rodin_search_subjects{$slabel}=RDFprocessor::$ownnamespacename.':'.RDFprocessor::adapt_name_for_uid($slabel);
		
		##########################
		#
		# segmentize query, associate segment to search
		#	
		
		$triple =
				$this->rdfize_search_subjects(	$searchuid,
																				$this->rodin_search_subjects, 
																				$triple
																				);
			
			
		##########################
		$i=0;
		while ($i < $resultCount) 
		##########################
		{
			$result = $this->rodin_results[$i];
			
			list($datasource,$authors,$publisher,$title,$isbn,$date,$urlPage) = 
					$this->openup_rodinResult_data($result);
			
			$subjects_swl_cleaned = $this->extract_cleaned_datasource_subjects($result,$title);
						
			Logger::logAction(27, array('from'=>'rdfize','countsubjects_x_result'=>$result->getId()."=".count($subjects_swl_cleaned)),$this->sid);
			
			//Construct global subject UIDs for later use!
			foreach($subjects_swl_cleaned as $slabel)
				$this->rodin_result_subjects{$slabel}=RDFprocessor::$ownnamespacename.':'.RDFprocessor::adapt_name_for_uid($slabel);
			
			
			##############################################
			#
			# BEGIN RDFIZATION (authors, works, subjects)
			#
			##############################################
			#
			# Add RDFizinf triples to obj $triple for current result
			#
			
			$triple=
				$this->rdfize_result_data(	$searchuid,
																		$this->get_result_uid($result), 
																		$authors, 
																		$publisher,
																		$title,
																		$isbn,
																		$date,
																		$urlPage,
																		$subjects_swl_cleaned,
																		$triple
																		);
			
			$i++;
		} // while results	
		################################################################		
				
				
		if ($WANT_RDF_ANNOTATION)
			$triple = $this->rdf_reannotate_exectime_for_search($searchuid,$timestamp,$triple);
		
		$T=count($triple);
		
		// IMPORT TRIPLES INTO LOCAL STORE:
		$this->import_triples($triple);
		
		if ($COUNT_TRIPLES) $count_triples_after=count_ARC_triples($this->store);
		
		Logger::logAction(27, array('from'=>'rdfize_extract_subjects','msg'=>'Exit'),$this->sid);


		# Sort subjects longest label first
		if (is_array($this->rodin_result_subjects) && count($this->rodin_result_subjects))
			uksort($this->rodin_result_subjects,'sort_by_item_length');		
		
		if ($COUNT_TRIPLES) $added_triples=($count_triples_after - $count_triples_before);
		
		return array(	$this->rodin_search_subjects,
									$this->rodin_result_subjects, 
									$searchuid,
									$added_triples ); 
		
	} // rdfize_extract_subjects
	

	
	
	/**
	 * Creates relation links between subjects and 
	 * register these creation links as metainformation starting from srcuid
	 * like (srcuid rodin_a:delivered ($s, $p, $o))
	 * This will allow a fix removal algorithm for all elements produced by srcuse
	 */
	public function rdfize_subexp(&$subjects,&$skos_result_subjects_expansions,$servicename,$COUNTTRIPLES)
	{
		global $RDFLOG;
		global $WANT_RDF_ANNOTATION;
		$DEBUG=0;
		
		if ($DEBUG)
		{
			$RDFLOG.= "<hr><b>rdfize_subexp using subjects ($servicename):</b> "; //print "<br>rdfize_subexp: $servicename ";var_dump($subjects);
			foreach($subjects as $label=>$subject_uid)
				$RDFLOG.= "<br> $label=>$subject_uid";
		}
		if (is_array($subjects) && count($subjects))
		foreach($subjects as $subject_label=>$subject_uid)
		{
			if (count(($SKOSEXPANSIONS=$skos_result_subjects_expansions{$subject_label})))
			{				
				foreach($SKOSEXPANSIONS as $SKOS)
				{
					list($src_name,$src_uid,$src_fresh_data,$broaders,$narrowers,$related) = $SKOS;

					if ($RDF_PROBLEM=(!$src_uid))
					{
						fontprint("<br>Error internally processing subject expansions on '<b>$subject</b>' coming from '$src_name': <b>NO UID supplied</b>!",'red');
					}

					if (is_array($broaders) && count($broaders))
					foreach($broaders as $bs_label)
					{
						if ($bs_label<>'' && strtolower($bs_label)<>$subject_label)
						{
							$triple = $this->register_subject_links_and_srcuse_annotation($servicename,$relationname='broader',$bs_label,$subject_uid,$src_uid,$triple,$RDF_PROBLEM);
						}
					}

					if (is_array($narrowers) && count($narrowers))
					foreach($narrowers as $ns_label)
					{
						if ($ns_label<>'' && strtolower($ns_label)<>$subject_label) 
						{
							$triple = $this->register_subject_links_and_srcuse_annotation($servicename,$relationname='narrower',$ns_label,$subject_uid,$src_uid,$triple,$RDF_PROBLEM);
						}	
					}

					if (is_array($related) && count($related))
					foreach($related as $rs_label)
					{
						if ($rs_label<>'' && strtolower($rs_label)<>$subject_label) 
						{
							$triple = $this->register_subject_links_and_srcuse_annotation($servicename,$relationname='related',$rs_label,$subject_uid,$src_uid,$triple,$RDF_PROBLEM);
						}
					}
				} // add related subjects from thesauri to s
			} // SKOS		
		} // foreach $subjects
		
		
		$count_triples_before=count_ARC_triples($this->store);
		
		// IMPORT TRIPLES INTO LOCAL STORE:
		$this->import_triples($triple);
		
		$count_triples_after=count_ARC_triples($this->store);
		
		return ($count_triples_after - $count_triples_before);
	} // rdfize_subexp
	
	
	
	/**
	 * Constructs triples to 
	 * 1. describe relationships to $subject_uid
	 * 2. describe the owership of the src (annotate the herein created triples under $src_uid rodin_a:delivered)
	 * @param $servicename - used to identify the use of this method
	 * @param $relationname - broader/narrower/related
	 * @param $label_new_subject - label
	 * @param $src_uid - uid of the src use to annotate triples
	 * @param $subject_uid - uid of the main subject (RODIN RESULT subject)
	 * @param $triple - array with previous triples
	 */
	public function register_subject_links_and_srcuse_annotation(	$servicename,
																																$relationname='broader',
																																$label_new_subject,
																																$subject_uid,
																																$src_uid,
																																&$triple,
																																$RDF_PROBLEM	)
	{
		$DEBUG=0;
		global $RDFLOG;
		global $WANT_RDF_ANNOTATION;
		
		if ($DEBUG) $RDFLOG.="<hr><b>register_subject_links_and_srcuse_annotation</b>($servicename,$relationname,$label_new_subject,$subject_uid,$src_uid)";
		
	  $s_uid=RDFprocessor::$ownnamespacename.':'.RDFprocessor::adapt_name_for_uid($label_new_subject);
		if ($DEBUG) $RDFLOG.= "<br>asserting ($subject_uid rodin:$relationname/rodin:subject_related $bs_uid ($bs_label))";
		$triple[]= $triple_relationname 		= array($subject_uid,	'rodin:'.$relationname, 	$s_uid); 
		$triple[]= $triple_subject_related 	= array($subject_uid,	'rodin:subject_related', 	$s_uid); 
		$triple[]= $triple_label 						= array($s_uid,			'rodin:label', 	 						l($label_new_subject)); 
		$triple[]= $triple_type							= array($s_uid,			'rdf:type', 	 							'dce:subject'); 
		if ($WANT_RDF_ANNOTATION && !$RDF_PROBLEM)
		{
			//Add link to an src uid 'rodin_a:subexp_broader' if $src_uid is set
			if ($src_uid)
			{
				if ($DEBUG) $RDFLOG.="<br>ANNOTATE TRIPLES";
				//The following is just a convenience link=triple, it allows the browsing from the srcuse to its delivered items (subjects)
				$triple[]= $triple_link_relation = array($src_uid, 'rodin_a:'.RDFprocessor::$relatedsubjects_servicename.'_broader',$s_uid); 
				
				// Register triples under src_uid:
				$triple[]=array($src_uid,			'rodin_a:delivered',	freeze_triple_as_literal_metaobject($triple_link_relation)); 
				$triple[]=array($src_uid,			'rodin_a:delivered',	freeze_triple_as_literal_metaobject($triple_relationname)); 
				$triple[]=array($src_uid,			'rodin_a:delivered',	freeze_triple_as_literal_metaobject($triple_subject_related)); 
				$triple[]=array($src_uid,			'rodin_a:delivered',	freeze_triple_as_literal_metaobject($triple_label)); 
				$triple[]=array($src_uid,			'rodin_a:delivered',	freeze_triple_as_literal_metaobject($triple_type)); 
			}
			else 
				{
					if ($DEBUG) $RDFLOG.="<br>COULD NOT ANNOTATE TRIPLES";
				}
		}
		
		
		if ($DEBUG)
		{
			$RDFLOG.="<br>TRIPLES ADDED: ";
			foreach($triple as $T)
			{
				list($s,$p,$o)=$T;
					$RDFLOG.="<br> ($s)($p)($o)";
			}
		}	//add here produced triples:
		
		return $triple;
	} // register_subject_links_and_srcuse_annotation
	
	
	
	
	/**
	 * 
	 */
	public function rdfize_search_subjects(	$searchuid,
																					&$search_subjects, 
																					&$triple
																					)
	{
		global $WANT_RDF_ANNOTATION;
		if (is_array($search_subjects) && count($search_subjects))
		foreach($search_subjects as $subject=>$subject_uid)
		{
			if ($WANT_RDF_ANNOTATION)
			{
				################################################################################
				#
				# Annotation section - link annotation to this result and result to widget, ...
				#
				$triple[]=$T=array('rodin_a:'.$searchuid,	'rodin_a:search_subj',  $subject_uid);
				$triple[]=$T=array($subject_uid,	'rdf:type', 'dce:subject' );
				$triple[]=array($subject_uid,	'rodin:label', 	l($subject)); //l64($
				#
				################################################################################
			}
			
		} // foreach $search_related_subjects
		
		return $triple;
	} // rdfize_search_subjects
	
	
	
	
	
	/**
	 * RDFIZE (generating triple objs - not registering them into triple store)
	 *
	 * work
	 * date
	 * urlPage
	 * author(s)
	 * subjects
	 * 
	 * returns enhanced triple obj 
	 */
	public function rdfize_result_data(	$searchuid, 
																			$work_uid, 
																			&$authors, 
																			$publisher,
																			$title,
																			$isbn,
																			$date,
																			$urlPage,
																			&$subjects_swl_cleaned,
																			&$triple
																			)
	{
		global $WANT_RDF_ANNOTATION;
		//Are there one or more authors?
		if (is_array($authors) && count($authors))
		foreach($authors as $author)
		{
			$authors_uid{RDFprocessor::$ownnamespacename.':'.RDFprocessor::adapt_name_for_uid($author)} = $author;
		} // authors
		
		//is there a publisher?
		if ($publisher)
		{
			 $publishers_uid{RDFprocessor::$ownnamespacename.':'.RDFprocessor::adapt_name_for_uid($publisher)} = $publisher;
		}
		
		
		//RDFize the work:
		if ($work_uid)
		{
			$triple[]=array($work_uid,		'rdf:type', 		'dce:BibliographicResource'); 
			
			if ($WANT_RDF_ANNOTATION)
			{
				################################################################################
				#
				# Annotation section - link annotation to this result and result to widget, ...
				#
				$triple[]=$T=array('rodin_a:'.$searchuid,	'rodin_a:resultdoc',  $work_uid);		
				#
				################################################################################
			}
			
			$triple[]=array($work_uid,		'dce:title', 		l64($title,'dce:title',RDFprocessor::$TOBECODED64)); 
			if ($isbn)
				$triple[]=array($work_uid,	'bibo:isbn', 		l($isbn)); 
			if($date)
				$triple[]=array($work_uid,	'dce:date', 		l($date)); 
			if($urlPage) {
				$triple[]=array($work_uid,	'dce:source', 	l($urlPage)); 
			}
			
			//Add subjects and assert datasource+timestamp
			//DO NOT ADD SUBJECTS HERE AND NOW - THIS IS DONE LATER
			if (is_array($subjects_swl_cleaned) && count($subjects_swl_cleaned))
			{
				
				foreach($subjects_swl_cleaned as $subject)
				{
					if($subject)
					{
						$subject_uids[]=$subject_uid=RDFprocessor::$ownnamespacename.':'.RDFprocessor::adapt_name_for_uid($subject);
						//print "<br>Adding subject: ($subject)";
						//print "<br>subject=($subject) subject_uid=($subject_uid) asserting ($work_uid,	'dce:subject', 	$subject_uid)";
						$triple[]=array($work_uid,		'dce:subject', 	$subject_uid); 
						$triple[]=array($subject_uid,	'rdf:type', 		'dce:subject'); 
						$triple[]=array($subject_uid,	'rodin:label', 	l($subject)); //l64($subject,'rodin:label',RDFprocessor::$TOBECODED64)); 
						
						// $subject=strtolower($subject);
						// //add related subjects from thesauri to s - NOT HERE - LATER ON
						// if (count(($SKOSEXPANSIONS=$skos_result_subjects_expansions{$subject})))
						// {
						// 								
						// } // SKOS
					} // nonzero sobject
				} // for subjects
			} // subjects
		} // $work_uid
		
		// add/link author information:
		if (is_array($authors_uid) && count($authors_uid))
		foreach($authors_uid as $author_uid=>$authortxt)
		{
			$triple[]=array($author_uid, 	'rdf:type', 			'foaf:Person'); 
			$triple[]=array($author_uid, 	'foaf:name', 			l64($authortxt,'foaf:name',RDFprocessor::$TOBECODED64)); 
			$triple[]=array($author_uid, 	'rodin:name', 		l64($authortxt,'foaf:name',RDFprocessor::$TOBECODED64)); 
			//$triple[]=array($author_uid, 	'dce:creator', 	 $work_uid);
			if ($work_uid)
				$triple[]=array($work_uid ,	'rodin:author', $author_uid);
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
			$triple[]=array($publisher_uid, 'rdf:type', 	 'foaf:Person'); 
			$triple[]=array($publisher_uid,	'foaf:name', 	 l64($publisher_txt,'foaf:name',RDFprocessor::$TOBECODED64)); 
			$triple[]=array($publisher_uid,	'rodin:name',  l64($publisher_txt,'rodin:name',RDFprocessor::$TOBECODED64)); 
			$triple[]=array($work_uid ,	'dce:publisher', 	 $publisher_uid	); 
			$triple[]=array($work_uid,	'rodin:publisher', $publisher_uid	); 
			
			//Link author writes about subjects
			if (is_array($subject_uids) && count($subject_uids))
			{
				foreach($subject_uids as $subject_uid)
					$triple[]=array($publisher_uid, 'rodin:writes_about', $subject_uid); 
			} // some subjects
				
		} // publishers
		return $triple;
	} // rdfize_result_data
	
	
	
	
	
	
	
	
	/**
	 * @param $search_subjects - assoc defined with $subjectlabel=>$subject_uid
	 * @param $search_uid - the uid of the global search
	 * @param $result_subjects - assoc defined with $subjectlabel=>$subject_uid
	 * @param $COUNTTRIPLES - Flag
	 */
	public function expand_rdfize_subjects(&$search_subjects,$searchuid,&$result_subjects,$COUNTTRIPLES)
	{
		
		$DEBUG=0;
		$showsubjects=1;
		global $WANT_RDF_ANNOTATION;
		$count_added_triples=0;
		
		list($skos_search_subjects_expansions,$count_added_triples1) 
							= $this->get_subjects_expansions_using_thesauri(	$search_subjects,
																																$this->sid,
																																RDFprocessor::$querysubjects_servicename,
																																$this->USER_ID,
																																RDFprocessor::$NAMESPACES,
																																$this->searchtermlang,
																																$searchuid,
																																$COUNTTRIPLES  );
				
		list($skos_result_subjects_expansions,$count_added_triples2) 
							= $this->get_subjects_expansions_using_thesauri(	$result_subjects,
																																$this->sid,
																																RDFprocessor::$relatedsubjects_servicename,
																																$this->USER_ID,
																																RDFprocessor::$NAMESPACES,
																																$this->searchtermlang,
																																null,
																																$COUNTTRIPLES  );
				
				
																														
		if ($showsubjects) tell_skos_subjects($skos_search_subjects_expansions, 'QUERY expand_rdfize_subjects SKOS subjects');
		if ($showsubjects) tell_skos_subjects($skos_result_subjects_expansions, 'RESULTS expand_rdfize_subjects SKOS subjects');
		
		#############################
		#
		# RDFize expanded subjects
		#
		
		$count_added_triples3 = $this->rdfize_subexp($search_subjects,	$skos_search_subjects_expansions, RDFprocessor::$querysubjects_servicename, 	$COUNTTRIPLES);
		$count_added_triples4 = $this->rdfize_subexp($result_subjects,	$skos_result_subjects_expansions,	RDFprocessor::$relatedsubjects_servicename,	$COUNTTRIPLES);
		
		if ($COUNTTRIPLES) $count_added_triples = $count_added_triples1 + $count_added_triples2 + $count_added_triples3 + $count_added_triples4;
		return array($skos_search_subjects_expansions,$skos_result_subjects_expansions,$count_added_triples);
	} //expand_rdfize_subjects
	
		
	
	
	
	/**
	 * Import documents and to these documents related subjects into RDF store
	 * 
	 * returns 
	 * 
	 * @param $flattened_skos_result_subject_list - ordered flattened list
	 * @param $result_subjects - list of currently known under SID produced result subjects
	 */
	public function lod_subJ_doc_fetch(&$flattened_skos_result_subject_list,&$result_subjects,$COUNTTRIPLES)
	{
		$DEBUG=0;
		global $RDFLOG;
		global $EPSILON;
		global $USER_ID;
		global $WANT_RDF_ANNOTATION;
		$REMOVE_EFFECTIVITY_TESTING=	0	;
		$C=get_class($this);
		$lang=$this->searchtermlang;
		$servicename='lodfetch';
		$ann_servicename=$servicename;
		
		$expandeddocs = 
		$further_expandeddocs =
		$processed_subjects = 
		$expanded_new_subjects = 
		$expanded_old_subjects = 
		$further_expanded_new_subjects = 
		$further_expanded_old_subjects = 
		array();
 		Logger::logAction(27, array('from'=>'lod_subJ_doc_fetch','msg'=>'Started'),$this->sid);
		
		$sl_count = count($flattened_skos_result_subject_list);
		
		if ($DEBUG || 1)
		{
			$RDFLOG.="<hr>Carefully FETCHING LOD Documents using $sl_count subject labels...";
			if(count($flattened_skos_result_subject_list))
			{
				$RDFLOG.="<hr><b>lod_subJ_doc_fetch searching for triples (docs and subjects) on the following subjects</b>:";
				foreach($flattened_skos_result_subject_list as $subject_label=>$R)
				{
					list($rank,$k)=$R;
					$RDFLOG.="<br>rank $rank label ($subject_label)";
				}
			}
		}
		
		if (is_array($flattened_skos_result_subject_list) && count($flattened_skos_result_subject_list))
		{
			$LOD_SOURCES=get_active_LOD_expansion_sources($USER_ID);
			$LOD_SOURCES_RECORDS= $LOD_SOURCES['records'];
			if (is_array($LOD_SOURCES_RECORDS) && count($LOD_SOURCES_RECORDS))
			{
				foreach($LOD_SOURCES_RECORDS as $LOD_SOURCES_RECORD)
				{
					$src_name=$LOD_SOURCES_RECORD['Name'];
					$src_id=$LOD_SOURCES_RECORD['ID'];
					$sds_sparql_endpoint=$LOD_SOURCES_RECORD['sparql_endpoint'];
					$sds_sparql_endpoint_params=$LOD_SOURCES_RECORD['sparql_endpoint_params'];
					$sds_parameters= $LOD_SOURCES_RECORD['src_parameters'];
					eval($sds_parameters); // expecting: $max_triples=10; $max_docs=5; $max_subjects=5;
					
					//check src_parameters
					$valid_src_parameters=true;
					if (!$max_docs)
					{
						global $USER_ID,$SRCLINKBASE;
						$SRCLINK="$SRCLINKBASE/select_src.php?nl=1&u=$USER_ID&showuser=$USER_ID&filter={$src_name}";
						$SRCHREF="<a href=$SRCLINK target='_blank' title='Click to open SRC_Management on the concerning record'>SRC_management</a>";
						$ERRORTXT="<br>Error in src_parameters for '{$src_name}' concerning '\$max_docs' - zero value provided."
											."<br>Please provide a value like '\$max_docs=n;' (n>0) in $SRCHREF in field 'src_parameters'";
						fontprint($ERRORTXT,'red');
						$valid_src_parameters=false;
						Logger::logAction(27, array('from'=>'lod_subJ_doc_fetch','error'=>$ERRORTXT),$this->sid);
						if ($DEBUG) $RDFLOG.="<br>$ERRORTXT";
					}
					else if (!$max_triples)
					{
						global $USER_ID,$SRCLINKBASE;
						$SRCLINK="$SRCLINKBASE/select_src.php?nl=1&u=$USER_ID&showuser=$USER_ID&filter={$src_name}";
						$SRCHREF="<a href=$SRCLINK target='_blank' title='Click to open SRC_Management on the concerning record'>SRC_management</a>";
						$ERRORTXT="<br>Error in src_parameters for '{$src_name}' concerning '\$max_triples' - zero value provided."
											."<br>Please provide a value like '\$max_triples=n;' (n>0) in $SRCHREF in field 'src_parameters'";
						fontprint($ERRORTXT,'red');
						$valid_src_parameters=false;
						Logger::logAction(27, array('from'=>'lod_subJ_doc_fetch','error'=>$ERRORTXT),$this->sid);
						if ($DEBUG) $RDFLOG.="<br>$ERRORTXT";
					}
					#
					###################################
					
					if ($valid_src_parameters)
					{
						$sds_url_base="$sds_sparql_endpoint?$sds_sparql_endpoint_params";
						
						if ($DEBUG) $RDFLOG.= "<br> Expanding RDF subject using LOD source <b>$src_name</b> ($sds_url_base) $max_triples max_triples";
						
						Logger::logAction(27, array('from'=>'lod_subJ_doc_fetch','src_parameters'=>"$src_name,$servicename,$sds_parameters"),$this->sid);
					
						$loopcount=0;
						foreach($flattened_skos_result_subject_list as $s=>$R)
						{
							$loopcount++;
							list($subjectrank,$k) = $R;
							$subject_uids=$subject_uid=RDFprocessor::$ownnamespacename.':'.RDFprocessor::adapt_name_for_uid($s);
							Logger::logAction(27, array('from'=>'lod_subJ_doc_fetch','msg'=>$msg="CALLING $servicename SRC $src_name on (ranked) subject $s ($subjectrank) with max $max_subjects subjects"),$this->sid);
							
							// Check and break here if limit $C::$rdfp_MAX_LOD_SUBJECT_DOCFETCH reached
							$count_processed_subjects=count($processed_subjects);
							if ($count_processed_subjects > $C::$rdfp_MAX_LOD_SUBJECT_DOCFETCH)		
							{
								if ($DEBUG || 1)
									$RDFLOG.=htmlprint("<br>lod_subJ_doc_fetch reached limit number of subjects to be used to seek/expand documents ({$C::$rdfp_MAX_LOD_SUBJECT_DOCFETCH}) - breaking loop",'red');
						
								break;
							} // $count_processed_subjects > $C::$rdfp_MAX_SRC_SUBJECT_EXPANSION
							
							else // continue process subjects LOD seeking for documents
							{
								list($still_to_process,$subsuming_subject,$numdocs) = $this->subject_is_still_to_process($s,$processed_subjects);							
								if ($still_to_process)
								{
									$cache_id="$servicename.$src_name.$s.$lang.$max_docs.$max_triples";
									
									################################################################
									#
									# Is there an src triple annotation for this service
									# which has data which is fresh enaugh for avoiding a call?
									# using $rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC
									#
									
									list(	$age_of_last_src_use, 
												$src_use, 
												$src_uid, 
												$further_expandeddocs,
												$further_expanded_new_subjects )= $this->check_rdf_annotated_last_src_lodfetch_use($src_name,$src_id,$cache_id,$s);
									if ($DEBUG) $RDFLOG.=htmlprint("<br>ANALIZING LOD SRC USE DATE: src_use=$src_use, age_of_last_src_use=$age_of_last_src_use, tolerated ({$C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC})",'blue');
									if ($src_use && $age_of_last_src_use <= $C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC)
										//Data is still fresh in triple store - no need of calling src
										{
											$count_further_expanded_docs=count($further_expandeddocs);
											$count_further_expanded_new_subjects=count($further_expanded_new_subjects);
											Logger::logAction(27, array('from'=>'lod_subJ_doc_fetch','msg'=>$msg="USING PERSISTED DATA ($count_further_expanded_docs) FROM RDF SPARQL SRC $src_name on subject $s with max $max_subjects subjects"),$this->sid);
											if ($DEBUG) $RDFLOG.=htmlprint("<br>$msg",'green');
										}
										else // src data too old or non existent in this way
										//CALL SRC
										{
											Logger::logAction(27, array('from'=>'lod_subJ_doc_fetch','msg'=>$msg="REQUESTING NEW DATA FROM (CACHED) RDF SPARQL LOD FETCH $src_name (age=$age_of_last_src_use > {$C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC} limit) on subject $s with max $max_subjects subjects"),$this->sid);
											if ($DEBUG) $RDFLOG.=htmlprint("<br>$msg",'red');
											
											if ($WANT_RDF_ANNOTATION)
											{
												# provide / update ann information
												# if $REMOVE_EFFECTIVITY_TESTING is 1, a slower but verbose display is performed to check 
												# whether all the triples were eliminated - this is accompanied by the prevention (see below)
												# from reconstructing the triples, in order to see the evvect of the removal
												if(0) // dangerous - still subject of study
												$count_removed_triples_remove_srcuse=
												$this->remove_srcuse(	$src_name,
																							$ID,
																							$cache_id,
																							$ann_servicename,
																							$s,
																							$COUNTTRIPLES,
																							$REMOVE_EFFECTIVITY_TESTING );
												list($srcuse_uid,$count_triple_added_rdfannotate_add_src_use)=
														$this->rdfannotate_add_src_use(	$src_name,
																														$ann_servicename,
																														$src_id,
																														$cache_id,
																														$sds_sparql_endpoint,
																														$sds_sparql_endpoint_params,
																														$USER_ID,
																														$COUNTTRIPLES,
																														$s );
											}
											if ($DEBUG) $RDFLOG.= "<br>Fetching document triples for subject '$s'";
	
											IF (!$REMOVE_EFFECTIVITY_TESTING) // Debug control - set to 0 to test remove without (r)construction
											list($triples,$count_results,$used_cache)
															 = get_cached_triples_on_subject_from_sparql_endpoint(	$s,
																																											$src_name,
																																											RDFprocessor::$rdfp_TOLERATED_SRC_SOLR_DATA_AGE_SEC,
																																											$sds_sparql_endpoint,
																																											$sds_sparql_endpoint_params,
																																											RDFprocessor::$NAMESPACES,
																																											$this->searchtermlang,
																																											$this->sid,
																																											$max_docs );
																							
										Logger::logAction(27, array('from'=>'lod_subJ_doc_fetch','msg'=>$msg="EXIT NEW DATA ($count_results) FROM (CACHED) RDF SPARQL SRC $src_name (age=$age_of_last_src_use > {$C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC} limit) on subject $s with max $max_subjects subjects"),$this->sid);
										$RDFentities = $this->regroup_triple_to_entities($triples);
										if (($otriplescount=count($triples)))
										{
											$homog_start = microtime_float();
											//Homogenize is used with method lod_subJ_doc_fetch() hence:
											Logger::logAction(27, array('from'=>'lod_subJ_doc_fetch','msg'=>$msg="START HOMOGENIZE $otriplescount $src_name triples on subject $subject_uid"),$this->sid);
											
											list(	$further_homogenized_triples,
														$further_expandeddocs,
														$further_expanded_new_subjects,
														$further_expanded_old_subjects	)
																= $this->homogenize_foreign_RDFentities($RDFentities,$result_subjects,$subject_uid,$src_name,$srcuse_uid,$lang);
											
											$count_further_homogenized_triples=count($further_homogenized_triples);
											$count_further_expanded_docs=count($further_expandeddocs);
											$count_further_expanded_new_subjects=count($further_expanded_new_subjects);
											$count_further_expanded_old_subjects=count($further_expanded_old_subjects);
																					
											
											if ($DEBUG)
											{
												$RDFLOG.= "<br>--> $count_further_expanded_docs docs in $count_further_homogenized_triples homogenized triples imported";
												$RDFLOG.= "<br>--> $count_further_expanded_new_subjects new subjects imported";
												$RDFLOG.= "<br>--> $count_further_expanded_old_subjects already existing subjects found";
											}
											
											if ($COUNTTRIPLES) $count_triples_before= count_ARC_triples($this->store);
											$this->import_triples($further_homogenized_triples,$LODIMPORTDEBUG=false);
											if ($COUNTTRIPLES) $count_triples_after= count_ARC_triples($this->store);
											if ($COUNTTRIPLES) $count_triples_imported= $count_triples_after - $count_triples_before;
											
											Logger::logAction(27, array('from'=>'lod_subJ_doc_fetch','msg'=>$msg="END HOMOGENIZE $count_further_expanded_docs $count_further_homogenized_triples $src_name triples on subject $subject_uid"),$this->sid);
										}									
									} // CALL SRC
									
									if ($count_further_expanded_docs)
									{
										$processed_subjects{$s}=$count_further_expanded_docs; 
									}
									
							  } // $processed_subjects
								else {
									if ($DEBUG)
										$RDFLOG.=htmlprint("<br>PREVENT further LOD document fetch on subject ($s), since there was a more specialised one ($subsuming_subject) having $numexpandeddocs document(s).",'red');
								}
								
								$expandeddocs = array_merge($expandeddocs,$further_expandeddocs);
								$numexpandeddocs+=$count_further_expanded_docs;
								
								$expanded_new_subjects = array_merge($expanded_new_subjects,$further_expanded_new_subjects);
								$count_expanded_new_subjects+=$count_further_expanded_new_subjects;
								
								$expanded_old_subjects = array_merge($expanded_old_subjects,$further_expanded_old_subjects);
								$count_expanded_old_subjects+=$count_further_expanded_old_subjects;
							} // continue process subjects LOD seeking for documents
							
							if ($COUNTTRIPLES)
							$count_triples_added+=	$count_removed_triples_remove_srcuse 
																		+ $count_triple_added_rdfannotate_add_src_use 
																		+	$count_triples_imported;
						} // foreach($subjects_labels)
						
						
						
					} // foreach($LOD_SOURCES_RECORDS
				} // valid_src_parameters
			}
			else
				$RDFLOG.= "<br> NO LOD sources (yet) used to expand result rdf information";
		}

		Logger::logAction(27, array('from'=>'lod_subJ_doc_fetch','msg'=>"Exit $servicename"),$this->sid);
		
		return array($expandeddocs,$expanded_new_subjects,$expanded_old_subjects,$count_triples_added);
	} // lod_subJ_doc_fetch
	
	
	
	
	
	/**
	 * ranks every subject in $result_subjects and in $skos_subjects_expansions
	 */
	public function rerank_subjects(	$search_subjects, 
																		$flattened_expanded_search_subject_list,
																		$result_subjects,
																		$flattened_expanded_result_subject_list,
																		$expanded_new_result_subjects		)
	{
		$DEBUG=0;
		global $RODINSEGMENT;
		global $RDFLOG;
  
		if ($DEBUG) $RDFLOG.= "<hr><b>rerank_subjects()</b>";

		if (!is_array($flattened_expanded_search_subject_list))
			$flattened_expanded_search_subject_list=array();

		if (!is_array($search_subjects))
			$search_subjects=array();

		if ($DEBUG) foreach($flattened_expanded_search_subject_list as $f) $RDFLOG.="<br>flattened_expanded_search_subject_list ($f)";

		$AU=array_unique(
				$AM=	array_merge( 	array_keys($search_subjects),
														$flattened_expanded_search_subject_list) );

		//Implode intelligently subsumtion free into test:
		$subsumptionfree_search_text='';
		foreach($AU as $l) {
			if (!strstr($subsumptionfree_search_text,$l))
			{
				$subsumptionfree_search_text.=$subsumptionfree_search_text?' ':'';
				$subsumptionfree_search_text.=$l;
			}
		}
		$this->rodin_reference_subjects_text = $subsumptionfree_search_text;

		if ($DEBUG) $RDFLOG.="<br>subsumptionfree_search_text: ($subsumptionfree_search_text)";

		//add the other ones into solr and 
		//retrieve MLT ranked subjects using $merged_search_subject_text
		//SYNCHRONISE SEND/RECEIVE?
			
		$this->rodin_flattened_expanded_result_subjects =
		$result_subject_list= array_unique(
															array_merge(	array_keys($result_subjects), 
																						array_keys($expanded_new_result_subjects), 
																						array_values($flattened_expanded_result_subject_list) ) );
		if ($DEBUG)
		{
			
		$RDFLOG.= "<br><br> search_subjects to be taken:";
		foreach(array_keys($search_subjects) as $k=>$s)
					$RDFLOG.= "<br>$k=>$s";
		$RDFLOG.= "<br><br> flattened_expanded_search_subject_list to be taken:";
		foreach($flattened_expanded_search_subject_list as $k=>$s)
					$RDFLOG.= "<br>$k=>$s";
		$RDFLOG.= "<hr><b>rerank_subjects - reference search text:</b> <br>"
					.$subsumptionfree_search_text;
		
		$RDFLOG.= "<br><br> Result subjects to be taken:";
		foreach($result_subjects as $k=>$s)
					$RDFLOG.= "<br>$k=>$s";
		$RDFLOG.= "<br><br> flattened_expanded_result_subject_list to be taken:";
		foreach($flattened_expanded_result_subject_list as $k=>$s)
					$RDFLOG.= "<br>$k=>$s";
		$RDFLOG.= "<br><br> expanded_new_result_subjects to be taken:";
		foreach($expanded_new_result_subjects as $k=>$s)
					$RDFLOG.= "<br>$k=>$s";
		$RDFLOG.= "<br><br><b> result_subject_list to be ranked</b>:";
		foreach($result_subject_list as $k=>$s)
					$RDFLOG.= "<br>$k=>$s";
		}

		$sorted_ranked_subjects = rank_vectors_vsm(	$subsumptionfree_search_text,
																								$result_subject_list, 
																								$this->sid, 
																								$this->USER_ID,
																								$miminumrank=	1);
		
		if ($DEBUG)
		foreach($sorted_ranked_subjects as $subject_label=>$REC)
		{
			list($rank,$k)=$REC;
			$RDFLOG.= "<br>$rank: $k: $subject_label";
		} // foreach

		return $sorted_ranked_subjects;
	} // rerank_subjects
	

	
	
	
	/**
	 * Ranks all rodin result document and the exp_docs in the current search
	 * Returns a list of all ranked docs .....
	 */
	public function rerankadd_docs($sid, &$docs_ext, &$ranked_result_subjects)
	{
		//Rank documents using ranked subjects.
		$DEBUG;
		global $RDFLOG;
				
		
		if ($DEBUG) $RDFLOG.="<hr><b>rerankadd_docs</b>";
		$C = get_class($this);
		$searchuid = 'rodin_a:'.$this->searchuid;
		
				//Get result documents direct from search (in $o)
		list($_,$docs) = get_triple_objects($searchuid,'rodin_a:resultdoc',$this->store,$C::$NAMESPACES);		
		//Compute the number of subj each document
		
		Logger::logAction(27, array('from'=>'rdfLODfetchDocumentsOnSubjects','msg'=>$msg="START RERANK"),$this->sid);
		
		$ranked_docs		= rank_docs_with_its_subjects($docs, 		$this->store, $C::$NAMESPACES, $ranked_result_subjects);
		$ranked_docs_ext= rank_docs_with_its_subjects($docs_ext,$this->store, $C::$NAMESPACES, $ranked_result_subjects);

		Logger::logAction(27, array('from'=>'rdfLODfetchDocumentsOnSubjects','msg'=>$msg="END RERANK"),$this->sid);
		
		if ($DEBUG)
		{
			$RDFLOG.="<br>rerankadd_rdf_documents_related_to_search:";
			$c=count($ranked_docs);
			$RDFLOG.="<br>$c Internal docs:"; //print "<br>$c Internal docs:<br>"; var_dump($ranked_docs);
			foreach($ranked_docs as $docuid=>$rank)
			{
				$RDFLOG.="<br> $docuid=>$rank";
			}
			
			$c=count($ranked_docs_ext);
			$RDFLOG.="<br>$c External docs:"; //print "<br>$c External docs:<br>"; var_dump($ranked_docs_ext);
			foreach($ranked_docs_ext as $docuid=>$rank)
			{
				$RDFLOG.="<br> $docuid=>$rank";
			}
		}
		//Adjust the rank annotation of each doc
		Logger::logAction(27, array('from'=>'rdfLODfetchDocumentsOnSubjects','msg'=>$msg="START WRES ASSEMBLING"),$this->sid);
		
		
		foreach($this->rodin_results as $result)
		//foreach($ranked_docs as $docuid=>$rank)
		{
			$C=get_class($this);
			
			$rdoc_uid = $this->get_result_uid($result);
			$rank = $ranked_docs{$rdoc_uid};
			// rank the result corresponding to this doc with $rank
			$result->setRank($rank);
			$ranked_docs_list{$rdoc_uid}=$rank;
		} // foreach $ranked_docs
		RodinResultManager::saveRodinResults($this->rodin_results, $this->sid, $datasource='', $timestamp='');
				
		#######################################################################
		#
		# ADD DOC TO RODIN RESULTS
		#
		#######################################################################
		foreach($ranked_docs_ext as $docuid=>$rank)
		{
			$rodin_result_type='';
			//Gather data to build result info
			//from RDF store
			$p_o = get_entity_infos2(	$C::$NAMESPACES,
																$C::$NAMESPACES_PREFIX,
																$this->store,
																$docuid,
																$token='',
																true );
			if (is_array($p_o) && count($p_o))
			{
				if ($DEBUG) $RDFLOG.="<br><br>rerankadd_rdf_documents_related_to_search()<br>Assembling info for external doc $docuid:";
				foreach($p_o as $p=>$ooo)
				{
					if ($DEBUG) $RDFLOG.="<br>($p) => ";
					
					if (is_array($ooo))
					{
						if ($DEBUG) 
						{
							$RDFLOG.= "[";
							foreach($ooo as $ovalue) $RDFLOG.= "(".$ovalue[0].")";
							$RDFLOG.= "]";
						}
					}
					switch ($p)
					{
						//Note:
						//ASSUMING (*) the LOD SOURCE has meticolous data
						//HERE a discrimination on dce:type
						//can lead to an exact mapping to a RODIN result
						//UNFORTUNATELY this (*) is not the case
						//since dce:type comes in several languages and forms
						//therefore we renounce to discriminate and
						//ASSUME HERE ALWAYS a type 'article'
						
						case 'dce:type': 
									if ($ooo[0][0]=='pdf' 
									|| $ooo[0][0]=='Thesis'
									|| $ooo[0][0]=='Article'
									|| $ooo[0][0]=='Text'
									|| $ooo[0][0]=='czasopismo'
									|| 1
									) 
										 $rodin_result_type = 'article';
									if (!$rodin_result_type)
										fontprint("<br>rerankadd_rdf_documents_related_to_search() Error mapping LOD document types to rodin result types (".$ooo[0][0]." ?)",'red');
									break;
						case 'dce:date': 
									$date_created = $ooo[0][0];
									break;
						case 'dce:title':
									$title=decode_64_literal($p,$ooo[0][0],$C::$TOBECODED64);
									break;
						case 'dce:description':
									$description=decode_64_literal($p,$ooo[0][0],$C::$TOBECODED64);
									break;
						case 'dce:identifier':
										if (count($ooo)>=1)
										$source_url = $ooo[0][0];
									if (count($ooo)>1)
										$identifier_url = $ooo[1][0];
									if ($DEBUG)
									{
										print "<hr>dce:identifier "; var_dump($ooo);
										print "<br>Take source_url = ($source_url)"; 
										print "<br>Take identifier_url = ($identifier_url)"; 
									}
									break;
						case 'dce:creator':
									foreach($ooo as $ovalue)
										$creators[]=decode_64_literal($p,$ovalue[0],$C::$TOBECODED64);
									$authors=array('creator'=>$creators);
									break;
						case 'dce:subject':
									foreach($ooo as $ovalue)
										$subjects[]=read_rodin_label($ovalue[0], $this->store, $C::$NAMESPACES);
									break;
											
					} // switch
				} // foreach pair
				//add once -> yes but at the moment SOLR stores it once ... even on multiple calls
				//Do not forget: we might still get some uncomplete information (through triple limit)
				
				if ($rodin_result_type)
				{
					if ($title==$oldtitle)
					{
						 $RDFLOG.=htmlprint("<br>rerankadd_rdf_documents_related_to_search():<br>prevent adding doc because title ($title) already added",'red');
					}
					else // do add unique
					{
						// if (this external doc is unique)
						
						$R = RodinResultManager::create_rodinResult_for_lod(  $rodin_result_type,
																																	$rank,
																																	$title,
																																	$description,
																																	$date_created,
																																	$source_url,
																																	$identifier_url,
																																	$authors,  // $authorFieldNames = array('creator'=>, 'person'=>, 'contributor'=>);
																																	$subjects  );
																													
						RodinResultManager::saveRodinResults($allResults=array($R), $this->sid, $datasource='extern', $timestamp='');
						$oldtitle=$title;
						$added_documents++;
						$ranked_docs_list{$docuid}=$rank;
						
						if ($added_documents >= $C::$rdfp_MAX_LOD_DOC_ADD)
						{
							$RDFLOG.=htmlprint("<br>rerankadd_rdf_documents_related_to_search():<br>breaking adding loop to $added_documents because of limit of (".$C::$rdfp_MAX_LOD_DOC_ADD.") max docs to add",'red');
							break;
						}
					}
				}
			}
			else 
				$RDFLOG.=htmlprint("<br>Error adding external document ($docuid)",'red');
		} // $ranked_docs_ext
		Logger::logAction(27, array('from'=>'rdfLODfetchDocumentsOnSubjects','msg'=>$msg="END WRES ASSEMBLING"),$this->sid);
		return $ranked_docs_list;		

	} // rerankadd_docs
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * 
	 */
	public function openup_rodinResult_data(&$result)
	{
		//print "<hr>Result: ";var_dump($result);
		if ($DEBUG)
			$RDFLOG.="<br><hr>PROCESSING RESULT ".$result->getId();
		$datasource = $result->getProperty('datasource');
		//Do we have one or more authors?
		$authors= $result->getAuthors() 
							?explode(',',$result->getAuthors())
							:null;
		$isbn= $this->extract_isbn($result->getProperty('isbn')); 
		$title= trim($result->getTitle()); 
		$date = $result->getDate();
		$urlPage = $result->getUrlPage();
		$publisher = $result->getProperty('publisher');
		
		return array($datasource,$authors,$publisher,$title,$isbn,$date,$urlPage);
	} // openup_result_data


	/**
	 * CLEANING SUBJECTS
	 * Filter out too small words or stopwords from subject list
	 * Filter out too complicated subjects
	 */
	public function clean_subject_labels(&$subjectlabels)
	{
		$DEBUG=0;
		global $RDFLOG;
		$C=get_class($this);
		$showsubjects=0;
		$subjects_swl_cleaned = array();
		if (is_array($subjectlabels) && count($subjectlabels))
		{
			if ($DEBUG){
				$RDFLOG.="<hr><b>clean_subject_labels</b>:";
				foreach($subjectlabels as $l) $RDFLOG.="<br>dirty subject: $l";
			}
			
			$subjects_sw_cleaned = array_unique(cleanup_stopwords($subjectlabels, $this->stopwords));
			if ($showsubjects) tell_subjects($subjects_sw_cleaned,"globally considered stopword cleaned subjects:");
			
			#
			# Clean lenght and enclosing double or single quotes
			#
			foreach($subjects_sw_cleaned as $slabel)
			{
				if (strlen($slabel) >= RDFprocessor::$rdfp_TOLERATED_MIN_SUBJ_LEN)
				{
					####
					#
					# Check/repair enclosing (double)quotes
					#
					if (preg_match("/\"(.+)\"/",$slabel,$match))
						$slabel=$match[1];
					else 
					if (preg_match("/'(.+)'/",$slabel,$match))
						$slabel=$match[1];
					
					#
					# Check for too many wirds in subject
					#
					$slabel = trim(str_replace('  ',' ',$slabel)); // no doubles
					$count_words=substr_count($slabel,' ')+1;
					if ($DEBUG) $RDFLOG.="<br>substr_count($slabel,' ')=$count_words";
					if ($count_words > $C::$rdfp_MAX_SUBJECT_TOKENIZE)
					{
						if ($DEBUG)
							$RDFLOG.=htmlprint("<br>DISCARD subject ($slabel) because it has too many words (more than ".$C::$rdfp_MAX_SUBJECT_TOKENIZE.")",'red'); 
					}
					else // insert it!
					$subjects_swl_cleaned[]=$slabel;
				}
			} // foreach (filter)
			if ($showsubjects || 1) tell_subjects($subjects_swl_cleaned,"clean_subject_labels(): globally considered stopword cleaned and length tested subjects for RODIN result:");
		}
		else
		{
			if ($DEBUG)
				$RDFLOG.="<br>clean_subject_labels: ZERO subjects got";	
		}
		return $subjects_swl_cleaned;
	} // clean_subject_labels				
	
	
	
	
	
	
	/**
	 * Try to read subjects seeking for
	 * - subjects
	 * - keywords
	 * - tags
	 * inside the current result
	 * If none found or too few found,
	 * guess subjects from title
	 */
	public function extract_cleaned_datasource_subjects(&$result,$title)
	{
		$DEBUG=0;
		global $RDFLOG;
		
		$showsubjects=1;
			
		$datasource_subjects = $this->compute_datasource_subjects(trim(strtolower($result->getProperty('subjects'))),$this->searchtermlang);
		if (!$datasource_subjects)
		$datasource_subjects = $this->compute_datasource_subjects(trim(strtolower($sss= $result->getProperty('keywords'))),$this->searchtermlang);
		if (!$datasource_subjects)
		$datasource_subjects = $this->compute_datasource_subjects(trim(strtolower($sss= $result->getProperty('tags'))),$this->searchtermlang);
		
		if ($DEBUG || 1) 
			$RDFLOG.="<br>USED (DETECTED) LANGUAGE for searchterm ({$this->searchterm}): ".$this->searchtermlang;
		
		if ($showsubjects) tell_subjects($datasource_subjects,"considered datasource subjects:");
		
		//print "<br>Datasource >Subjects (".$this->my_result->getProperty('subjects').")";
		
		#####################################################################
		#
		# Prepare title category etc ... if possibile
		#
		$subjects = $datasource_subjects;
		if (($c=count($datasource_subjects)) < RDFprocessor::$rdfp_THRESHOLD_DATASOURCE_MIN_SUBJECTS)
		{
			if (RDFprocessor::$rdfp_EXTRACT_SUBJECTS_FROM_TITLE)
			{
				if ($DEBUG || 1)
					$RDFLOG.= htmlprint("<br>Compute subjects from title",'green').", since threshold ".RDFprocessor::$rdfp_THRESHOLD_DATASOURCE_MIN_SUBJECTS." not reached ($c subjects read)";
				
				//Justincase: quotes !!!
				$title=str_replace('"','',$title);
				$title=str_replace("'",'',$title);
				
				list($title,$category,$presentation_at,$date_event) = $this->scan_datasource_title($title,$result->wdatasource,$this->searchtermlang);
	
				$additional_subjects=$this->compute_title_subjects($title,$this->searchterm,$result->wdatasource,$this->searchtermlang);
				if ($showsubjects) tell_subjects($additional_subjects,"extracted additional title subjects:");
		
				//print "<br>ADDITIONAL Subjects (".implode('+',$additional_subjects).")";
				$msubjects=array_unique(array_merge($datasource_subjects,$additional_subjects));
				
				$uniquesubjects = $this->compute_unique_subjects($msubjects,$this->searchtermlang);
				if ($showsubjects) tell_subjects($uniquesubjects,"extracted unique subjects:");
				
				$subjects=array_unique(array_merge($msubjects,$uniquesubjects));
				//print "<br>FINAL Subjects (".implode('+',$subjects).")";
				
				if ($showsubjects) tell_subjects($subjects,"globally considered subjects:");
			} // $rdfp_THRESHOLD_DATASOURCE_MIN_SUBJECTS
			else {
				$RDFLOG.=htmlprint("<br>DO NOT EXTRACT subjects from title (param RDFprocessor::rdfp_EXTRACT_SUBJECTS_FROM_TITLE)",'red');
			}
		} // RDFprocessor::$rdfp_EXTRACT_SUBJECTS_FROM_TITLE
		


		if ($DEBUG)
		{
			$count_r = count($subjects);
			$RDFLOG.="<br>generated $count_r dirty subjets";
			foreach($subjects as $s) $RDFLOG.="<br>s $s";
		}


		#
		#####################################################################
		# CLEANING SUBJECTS
		# Filter out too small words or stopwords from subject list
		#
		$subjects_swl_cleaned = $this->clean_subject_labels($subjects);
		
		if ($DEBUG)
		{
			$count_r = count($subjects_swl_cleaned);
			$RDFLOG.="<br>returning $count_r cleaned subjets";
		}
		
		return $subjects_swl_cleaned;
	} // extract_cleaned_datasource_subjects
	
	
	
	
	
	
	
	
	/**
	 * Imports triples into store
	 * @param $triples A vector of triples (arrays(s,p,o))
	 * returns: Statistic object reflecting import process
	 * 
	 * Important: the object of a triple must have <> or "" to denote literal
	 */
	function import_triples(&$triples,$DEBUG_SOURCE='')
	{
		$DEBUG=0;
		$statistics=null;
		$GRAPH=RDFprocessor::$importGraph;
		$NAMESPACES_PREFIX = RDFprocessor::$NAMESPACES_PREFIX;
		$TRIPLETEXT="
		$NAMESPACES_PREFIX
    INSERT INTO <$GRAPH> 
    {";
 	
		$i=0;
		
		$T=count($triples);
		Logger::logAction(27, array('from'=>'import_triples','msg'=>"START IMPORT TRIPLES $T"),$this->sid);
		
		//print "<br>import_triples called with:<br>"; var_dump($triples);
		if (is_array($triples) && count($triples)>0)
		{
			foreach($triples as $triple)
			{
				$i++;
				
				if ($DEBUG_SOURCE)
				{
					if ($i > 560) 
					{
						fontprint( "<br><b>BREAKED CONSTRUCTION LOOP for '$DEBUG_SOURCE' at STEP ".($i - 1)." </b>",'red');
						break;
					}
				}
				
				$s=$triple[0];
				$p=$triple[1];
				$o=cleanup4literal($triple[2]); // literals might contain ' '' ... / \ etc ... ?
				
				// Cleanness check
				$clean= ( $s<>''
							&& $p <>''
							&& $o <>'' );
							
				if (!$clean)
				{
					fontprint ("<br>import_triples(): Error on triple ($s)($p)($o) ",'red');
				}
				else	
				//Insert Triple
				{
			  	$TRIPLETEXT.="\n $s $p $o .";
				}	 
				
				//if($i>19) break;
			}
			
			$TRIPLETEXT.="\n}";
			
			
			if($DEBUG) print "<br><hr>DEBUG import_triples(): ARC CONSTRUCTING: <hr>".str_replace("\n","<br>",htmlentities($TRIPLETEXT));	
			if($DEBUG_SOURCE) print "<br><hr>DEBUG <b>$DEBUG_SOURCE</b> import_triples(): ARC CONSTRUCTING ".($i - 1)." triples: <hr>".str_replace("\n","<br>",htmlentities($TRIPLETEXT));	
			
			$num_triples_before=count_ARC_triples($this->store);
	    $num_triples_before_formatted=number_format($num_triples_before, 0, '.', "'");
	
	    //We need on the server at HEG to enhance php execution time limit, 
	    //since this server is slowlee and need more time than the local power macs
	    set_time_limit ( 1000000 ); // 250h -> Feature in 5.3.0 deprecated, in 5.4.0 deleted - but useful right now
	    $rs=NULL;
	    $repetitions=0;
	    $added_triples=0;
			
	    $rs= $this->store->query($TRIPLETEXT);
	    $added_triples = intval($rs['result']['t_count']);
	    $repetitions++;
	    if (($DEBUG || $DEBUG_SOURCE) && ($errs = $this->store->getErrors())) {
	
	      foreach($errs as $err)
	      fontprint("<br>ARC ERROR: $err",'red');
				fontprint("<hr>ARC CONSTRUCTING USING: <br>".str_replace("\n","<br>",htmlentities($TRIPLETEXT)),'red');	
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
	      $num_triples_after=count_ARC_triples($this->store);
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

		Logger::logAction(27, array('from'=>'import_triples','msg'=>"END IMPORT TRIPLES $T"),$this->sid);

    return $added_triples;
	} // import_triples
	
	
	
	/**
		 * Construct an id for SPQRQL USE
		 * using name, born and place of birth
		 * returns uid (string)
		 */
		public function getWork_uid($title,$date,$namespace_short='rodin')
		{
			$title=trim($title);
			$date=trim($date);
			$EVT_TITLE= $title ? RDFprocessor::adapt_name_for_uid($title): '';
			$EVT_DATE= $date ? RDFprocessor::adapt_name_for_uid($date): '';
			if ($EVT_TITLE && $EVT_DATE)
				$wuid_short=
						$namespace_short.':'
							.$EVT_TITLE.'_'
							.$EVT_DATE;
							
			else if ($EVT_TITLE)
				$wuid_short=
						$namespace_short.':'
						.$EVT_TITLE;
			
			else if ($EVT_DATE)
				$wuid_short=
						$namespace_short.':'
						.$EVT_DATE;
			
			else 
				$wuid_short=
						$namespace_short.':work_'
						.uniqid();
			
	  	return $wuid_short;
		} // getWork_uid
	
	
	
	
	public function get_result_uid(&$result)
	{
		$isbn= trim($this->extract_isbn($x=$result->getProperty('isbn'))); 
		if ($isbn)
				$work_uid=RDFprocessor::$ownnamespacename.':'.'isbn_'.$isbn;
			else // in case no isbn be provided:
			{
				$title= trim($t=$result->getTitle()); 
				if ($title) // a title HAS ALWAYS to be there
					$work_uid = $this->getWork_uid($title, $date);
			}
		return $work_uid;
	}
	
	
	
	
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
							.RDFprocessor::adapt_name_for_uid($this->masic_author_name).'_'
							.RDFprocessor::adapt_name_for_uid($this->masic_author_no).'_'
							.RDFprocessor::adapt_name_for_uid($this->masic_author_fromplace).'_'
							.RDFprocessor::adapt_name_for_uid($this->masic_author_life_range);
				$this->author_uid_long=$uid_long;
			} 
					
			//retrieve/generate uid short (to be used with a redirect):
			if ($this->author_uid_short)
				$uid_short = $this->author_uid_short;
			else {
				$uid_short= 
							$namespace_short.':'
							.RDFprocessor::adapt_name_for_uid($this->masic_author_no).'__'
							.RDFprocessor::adapt_name_for_uid($this->masic_author_life_range);
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
	
	
	
	
		public static function metasearch_uid($sid)
		{
			$sid_uid=RDFprocessor::sid_uid($sid);
			$searchuid="search_".$sid_uid;
			return $searchuid;
		}
		
	
		public static function sid_uid($sid)
		{
			$sid_uid=RDFprocessor::adapt_name_for_uid($sid,'.');
			return $sid_uid;
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
		
		if ($DEBUG)
		{
			$RDFLOG.= "<br>compute_datasource_subjects($ds_subjects,$lang):";
			foreach($subjects_cand as $s) $RDFLOG.="<br>$s";
		}
		
		
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
	 * 
	 */
	public function extract_subjects_from_term($term,$lang)
	{
		
		$DEBUG=0;
		global $RDFLOG;
		if ($DEBUG) 
			$RDFLOG.="<br>extract_subjects_from_term($term,$lang):";
		
		return $this->compute_title_subjects($term,$term,$datasource='',$lang);
	}
		
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
	public function compute_title_subjects($title,$search,$datasource,$lang)
	{
		$DEBUG=0;
		global $RDFLOG;
	  $C=get_class($this);
	  
	  
	  if (!$title) $RDFLOG.= htmlprint("<br>compute_title_subjects(): Error compute_subjects called with empty title !!!");
	
		if (strstr($datasource,'swissbib'))
		{
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
		} // Swissbib
		else // if (strstr($datasource,'alexandria'))
		{
			
		} // alexandria
		
	
		$title_cleaned_arr=array_unique(cleanup_stopwords(explode(' ',strtolower(clean_spechalchars($title))),$this->stopwords));
		$title_cleaned=implode(' ',$title_cleaned_arr); //separate into chunks
	  
		if($DEBUG)
		{
			$RDFLOG.= "<br><b>compute_title_subjects</b>((($search)),(($title))):";
			$RDFLOG.= "<br>title_cleaned: (($title_cleaned))";
		}
		$subjects=array();	
		//Add as subject the whole title - without colons...
		if (($langt=detectLanguageAndLog($title_cleaned,'compute_title_subjects',$this->sid))==$lang)
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
		
		$segments=preg_split("/[,:;\+\s*]+/",$title_cleaned);
		
		$tolerated_lang = 'en';
		if (count($segments))
		{
			foreach($segments as $segment)
			{
				if ($DEBUG) $RDFLOG.="<br>Consider subject segment $segment";
				$segment=put_to_singular($segment);
				insert_filtered_once($segment,$subjects,$lang,$tolerated_lang);
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
	public function scan_datasource_title($text,$datasource,$lang)
	{
		$DEBUG=0;
		$C = get_class($this);
		
		//tokenize and date_parse
		//print "<br>scan_datasource_title($text,$datasource,$lang) ...";
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
		
		} // swissbib$
		
		###########################################
		else if (strstr($datasource,'alexandria'))
		###########################################
		{
			$scanned_obj=array($text,null,null,null);
		} // alexandria
		else {
			$scanned_obj=array($text,null,null,null);
		}
							
							
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
		$SRCDEBUG=0;
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
			//$allTermUris .= $uri . RDFprocessor::$TERM_SEPARATOR;
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
		$SRCDEBUG=0;
		
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
			$DEBUG=0;
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
		$DEBUG=0;	
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
			
		$res_disambiguated_terms = implode(RDFprocessor::$TERM_SEPARATOR, $disambiguated_terms);
    
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
	 	$SRCDEBUG=0;
		$WIKIPEDIASEARCH2=RDFprocessor::$WIKIPEDIASEARCH2;
		$WIKIPEDIABASEURL=RDFprocessor::$WIKIPEDIABASEURL;
		
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
	 * Logs the following params:
	 * 
	 * Returns assoc with values
	 */
	public function log_rdf_parameters()
	{
		$C=get_class($this);
		$LOGPARAMS['rdfp_TOLERATED_MIN_SUBJ_LEN']							=$C::$rdfp_TOLERATED_MIN_SUBJ_LEN;
		$LOGPARAMS['rdfp_EXTRACT_SUBJECTS_FROM_TITLE']				=$C::$rdfp_EXTRACT_SUBJECTS_FROM_TITLE;
		$LOGPARAMS['rdfp_MAX_SUBJECT_TOKENIZE']								=$C::$rdfp_MAX_SUBJECT_TOKENIZE;
		$LOGPARAMS['rdfp_TOLERATED_SRC_SOLR_DATA_AGE_SEC']		=$C::$rdfp_TOLERATED_SRC_SOLR_DATA_AGE_SEC;
		$LOGPARAMS['rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC']			=$C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC;
		$LOGPARAMS['rdfp_THRESHOLD_DATASOURCE_MIN_SUBJECTS'] 	=$C::$rdfp_THRESHOLD_DATASOURCE_MIN_SUBJECTS;
		$LOGPARAMS['rdfp_MAX_SRC_SUBJECT_EXPANSION_PRO_SRC']	=$C::$rdfp_MAX_SRC_SUBJECT_EXPANSION_PRO_SRC;
		$LOGPARAMS['rdfp_MAX_SRC_SUBJECT_EXPANSION']					=$C::$rdfp_MAX_SRC_SUBJECT_EXPANSION;
		$LOGPARAMS['rdfp_MAX_LOD_SUBJECT_DOCFETCH']						=$C::$rdfp_MAX_LOD_SUBJECT_DOCFETCH;
		$LOGPARAMS['rdfp_MAX_LOD_DOC_ADD']										=$C::$rdfp_MAX_LOD_DOC_ADD;
		$LOGPARAMS['rdfp_USE_ARC_SOLR_BRIDGE']								=$C::$rdfp_USE_ARC_SOLR_BRIDGE;
		
		foreach($LOGPARAMS as $key=>$value)
					$LOGPARAMS_DB.=($LOGPARAMS_DB?';':'')."\$LOGPARAMS['$key']=$value";
		Logger::logAction(27, array('from'=>'log_rdf_parameters','rdf_exec_params'=>$LOGPARAMS_DB),$this->sid);
	
		return $LOGPARAMS;

	} // log_rdf_parameters
	
	
	
	public function rdf_reannotate_exectime_for_search($searchuid, $timestamp, &$triple)
	{
		#####################################################################
		#
		# Annotation
		# SUPPLY SECTION (part 2)
		# 
		list($timstampnow,$_)=timestamp_for_rdf_annotation();
		$msec_delta=floatval(str_replace('_','.',$timstampnow)) - floatval(str_replace('_','.',$timestamp));
		$msec_delta=number_format($msec_delta,6,'_','');
		$triple[]=array('rodin_a:'.$searchuid,	'rodin_a:exec_time', 		l($msec_delta));
		
		return $triple;
	}
	
	
	
	
	/**
	 * 
	 */
	public function rdf_reannotate_search($sid,$searchterm,&$triple)
	{			
			list($timestamp,$timestamp4humans) = timestamp_for_rdf_annotation();
			
			$this->remove_unused_result_triples_on_search($searchterm, $this->store, RDFprocessor::$NAMESPACES, $this->USER_ID);
			#
			# Annotate the main search params (older are superseeded)
			#
			# ORDER SECTION (is repeated for each Result ... but no impact since once stored -> OPTIMIZE)
			#
			$sid_uid=	RDFprocessor::sid_uid($sid);
			$searchuid=	RDFprocessor::metasearch_uid($sid);
			$triple[]=array('rodin_a:'.$searchuid,	'rodin_a:sid', 					l($sid_uid));		
			$triple[]=array('rodin_a:'.$searchuid,	'rodin_a:userid', 			l($this->USER_ID));		
			$triple[]=array('rodin_a:'.$searchuid,	'rdf:type', 						'rodin_a:rodin_search');		
		  $triple[]=array('rodin_a:'.$searchuid,	'rodin_a:timestamp', 		l($timestamp));
			
			foreach(explode(',', $searchterm) as $compound)
				$triple[]=array('rodin_a:'.$searchuid,	'rodin_a:search_term', 	l($compound));		
			#
			#####################################################################
			#
			# SUPPLY SECTION				
			#
			$triple[]=array('rodin_a:'.$searchuid,	'rodin_a:supplied', 		l($timestamp));
			$triple[]=array('rodin_a:'.$searchuid,	'rodin_a:supplied4h', 	l($timestamp4humans));
			#
			# rodin_a:exec_time see at bottom
			#
			# LINK RESULT to ANNOTATION - see below
			#
			#####################################################################
			return array($searchuid,$timestamp,$triple);
		} // rdf_reannotate_search
	
	
	
	
	
	/**
	 * For each active thesaurus: gather skos information on the subjects
	 * Returns $skos_subject_expansion = (OBJ)
	 * 
	 * Note: In case there are in the local store calls to previously done src uses
	 * the data coming from these src uses is gathered instead of re-calling the src again
	 * 
	 * Note: In case the src is marked to be local, no http call but a direct call is issued
	 * 
	 * Note. In case $servicename='queryexp' and $searchuid is set, result relations 
	 * will be made from the search uid onwards insdead from the src_uid (calculated locally)
	 * 
	 * 
	 * @param vector $subjects
	 * @param string $sid
	 * @param $servicename = 'subjexp'
	 * @param integet $USER_ID
	 * @param vector $NAMESPACES
	 * @param vector $subjects - assoc $subject{$label}=$uid
	 * @param string $lang
	 * @param $searchuid - the main search uid (maybe null)
	 * @param $COUNTTRIPLES - if set, several ARC count triple calls are made
	 */
	public function get_subjects_expansions_using_thesauri(&$subjects,$sid,$servicename,$USER_ID,&$NAMESPACES,$lang,$searchuid,$COUNTTRIPLES)
	{
		global $RDFLOG;
		global $VERBOSE;
		global $SRCDEBUG;
		
		$DEBUG=0;
		$MONITORCALLS=0;
		$REMOVE_EFFECTIVITY_TESTING =    0   ;
		$FETCHDATAFROMSOURCE = 1;
		$skos_subject_expansion = array();
		$count_all_added_triples=1;
		if (is_array($subjects) && count($subjects))
		{
			$C = get_class($this);
			if ($DEBUG) {
				$RDFLOG.= "<hr><b>get_subjects_expansions_using_thesauri on the following subjects:</b>";
				foreach($subjects as $s=>$uid_notused) if ($s) $RDFLOG.= "<br>&nbsp;$s ($uid_notused)";
			}
			$ann_servicename=$servicename;
			$subject_count=count($subjects);
			Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>"Started with $subject_count subjects"),$this->sid);
			
			
			$C=get_class($this);
			//$VERBOSE=0; $SRCDEBUG=0;
			$MAXRESULTS=5;
			global $DOCROOT,$WEBROOT,$RODINSEGMENT,$RODINROOT;
			global $SRC_SEARCH_MAX;
			global $TERM_SEPARATOR; 
			if (!$TERM_SEPARATOR) $TERM_SEPARATOR=',';
			
			$processed_subjects=array();
			$processed_subjects_pro_src=array();
			//print "<hr>get_SRC_THESAURI_RECORDS(null,$USER_ID,$lang)...";
			$INITIALISED_SRCs = get_SRC_THESAURI_RECORDS($SRCS=null,$USER_ID,$lang);
	
			if ($DEBUG) 
			{
				$RDFLOG.= $xxx = htmlprint("<br>".count($INITIALISED_SRCs)." USED SRCs for related subjects !",'gren');
			}
			if (count($INITIALISED_SRCs))
			foreach($INITIALISED_SRCs as $INITIALISED_SRC)
			{
				if ($enaugh_processed_subjects)
				{
					break; // loop here
				}
				//list($src_name,$CLASS,$pathSuperClass,$pathClass,$path_sroot,$path_SRCengineInterface,$path_SRCengine) = $INITIALISED_SRC;
				if ($COUNTTRIPLES) $count_removed_triples=$count_added_triples=0;
				
				list(	$src_name,
							$IS_REMOTE_SPARQL_ENDPOINT,
							$sds_sparql_endpoint,
							$sds_sparql_endpoint_params,
							$LOCAL_SRC,
							$DISKenginePATH,
							$basic_path_sroot,
							$basic_path_SRCengineInterface,
							$basic_path_SRCengine,
							$CLASS,
							$pathClass,
							$pathSuperClass,
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
							$src_parameters,
							$autocomplete_uri ) = $INITIALISED_SRC;
				
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
					Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','error'=>$ERRORTXT),$this->sid);
					if ($DEBUG) $RDFLOG.="<br>$ERRORTXT";
				}
				#
				###################################
				
				if ($valid_src_parameters)
				{
					Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>"Starting $src_name on $subject_count subjects (".implode('+',is_array($subjects)?$subjects:array()).") extracting further (SKOS) $max_subjects subjects"),$this->sid);
					if ($DEBUG) $RDFLOG.="<br>Compute max $max_subjects subjects from Thesaurus $src_name for $subject_count subjects (".implode('+',$subjects).")";
					
					###########################
					if ($IS_REMOTE_SPARQL_ENDPOINT)
					###########################
					{
						$loopcount=0;
						if (is_array($subjects) && count($subjects))
						foreach ($subjects as $s=>$_)
						{
							$loopcount++;
							Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>$msg="CALLING $servicename SRC $src_name on subject $s with max $max_subjects subjects"),$this->sid);
							$s = trim(strtolower($s));
							if ($s<>'')
							{
								###############################################################################
								// Break here also if $rdfp_MAX_SRC_SUBJECT_EXPANSION reached
								$count_processed_subjects=count($processed_subjects);
								if ($processed_subjects_pro_src{$ID} + 1 > $C::$rdfp_MAX_SRC_SUBJECT_EXPANSION_PRO_SRC)		
								{
									if ($DEBUG)
									{
										$RDFLOG.=$xxx=htmlprint("<br>get_subjects_expansions_using_thesauri reached statical limit number of subjects to seek/expand pro SRC ($src_name) ({$C::$rdfp_MAX_SRC_SUBJECT_EXPANSION_PRO_SRC}) - breaking loop",'red');
										//print $xxx;
									}
									break;
								} // $count_processed_subjects > $C::$rdfp_MAX_SRC_SUBJECT_EXPANSION
								else if ($count_processed_subjects+1 > $C::$rdfp_MAX_SRC_SUBJECT_EXPANSION) 
								{
									if ($DEBUG)
									{
										$RDFLOG.=$xxx=htmlprint("<br>get_subjects_expansions_using_thesauri reached statical limit number of subjects to seek/expand ({$C::$rdfp_MAX_SRC_SUBJECT_EXPANSION}) - breaking loop",'red');
										//print $xxx;
										$enaugh_processed_subjects=true;
									}
									break;
								}
								###############################################################################
								else // continue process subjects
								{
									if ($MONITORCALLS) print "<br>monitor ".($monitorcall_stp++)." call stp? remote endpoint $src_name on $s ($count_processed_subjects succesfully processed subjects)";
	
									list($still_to_process,$subsuming_subject,$numdocs) = $this->subject_is_still_to_process($s,$processed_subjects);
									if ($still_to_process)
									{
										##########################################
										#
										# RDF annotated src use check
										#
										$cache_id="$servicename.$src_name.$s.$lang.$max_subjects";
										if ($MONITORCALLS) print "<br>monitor ".($monitorcall++)." call remote endpoint $src_name on $s";
										
										list($age_of_last_src_use, $src_use, $srcuse_uid, $SKOSOBJ) = $this->check_rdf_annotated_last_src_subexp_use($src_name,$ID,$cache_id,$s);
										
										if ($DEBUG) $RDFLOG.=htmlprint("<br>ANALIZING SRC USE DATE: src_use=$src_use, age_of_last_src_use=$age_of_last_src_use, tolerated ({$C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC})",'blue');
										if ($src_use && $age_of_last_src_use <= $C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC)
										//Data is still fresh in triple store - no need of calling src
										{
											list($broader,$narrower,$related)=$SKOSOBJ;			
											$count_results=count($broader)+count($narrower)+count($related); 
											Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>$msg="USING PERSISTED DATA ($count_results) FROM RDF SPARQL SRC $src_name on subject $s with max $max_subjects subjects"),$this->sid);
											if ($DEBUG) $RDFLOG.=htmlprint("<br>$msg",'green');
											
											//Register results only if some data supplied
											if ($count_results) 
											{
												$expanded_subjects =
														array($src_name,
																	$srcuse_uid,
																	$src_fresh_data=false,
																	$broader	= $SKOSOBJ[0], 
																	$narrower	= $SKOSOBJ[1], 
																	$related	= $SKOSOBJ[2]);
											}
										}
										else // src data too old or non existent in this way
										//CALL SRC
										{
											Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>$msg="REQUESTING NEW DATA FROM (CACHED) RDF SPARQL SRC $src_name (age=$age_of_last_src_use > {$C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC} limit) on subject $s with max $max_subjects subjects"),$this->sid);
											if ($DEBUG) $RDFLOG.=htmlprint("<br>$msg",'red');
											# provide / update ann information

											if ($DEBUG) $RDFLOG.="<br>1 ABOUT TO CALL SRC $src_name on $s";
																						
											if (0) // dangerous ... 
											{$count_removed_triples =
												$this->remove_srcuse($src_name,$ID,$cache_id,$ann_servicename,$s,$COUNTTRIPLES,$REMOVE_EFFECTIVITY_TESTING);}
											
											if ($DEBUG) $RDFLOG.="<br>1 ABOUT TO CALL SRC $src_name on $s";
											
												
											list($srcuse_uid,$count_added_triples) =
															$this->rdfannotate_add_src_use(	$src_name,
																															$ann_servicename,
																															$ID,
																															$cache_id,
																															$sds_sparql_endpoint,
																															$sds_sparql_endpoint,
																															$USER_ID,
																															$s,
																															$COUNTTRIPLES );
																															
											if ($DEBUG) $RDFLOG.="<br>2 ABOUT TO CALL SRC $src_name on $s";
																															
											if($FETCHDATAFROMSOURCE) // debug control
											$expanded_subjects =
													array($src_name,
																$srcuse_uid,
																$src_fresh_data=true,
																$broader	=array(),
																$narrower	=array(),
																$related	=get_cached_related_subjects_from_sparql_endpoint(	$s,
																																															$src_name,
																																															$sds_sparql_endpoint,
																																															$sds_sparql_endpoint_params,
																																															$cache_id,$NAMESPACES,
																																															$this->searchtermlang,
																																															$this->sid,
																																															$max_subjects
																																														));
											
											$count_results=count($related);
											Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>$msg="EXIT NEW DATA ($count_results) FROM (CACHED) RDF SPARQL SRC $src_name (age=$age_of_last_src_use > {$C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC} limit) on subject $s with max $max_subjects subjects"),$this->sid);
											if ($DEBUG) $RDFLOG.=htmlprint("<br>$msg",'red');
										} // CALL SRC
										//Add-register ONLY if some results effectively came
										if ($count_results)
										{
											add_to_assocvector($skos_subject_expansion,$s,$expanded_subjects);					
											$processed_subjects{$s}=$count_results;
											$processed_subjects_pro_src{$ID}+=$count_results;
										}
									}
									else 
									{
										if ($DEBUG) $RDFLOG.=htmlprint("<br>SUPPRESS (sparql) Subject '{$s}' because subsumed by '{$subsuming_subject}' having already $numdocs results",'red');
									}
								} // continue process subjects
							} // $s<>''
							if ($COUNTTRIPLES) $count_all_added_endpoint_triples+=$count_removed_triples+$count_added_triples;
						} // foreach ($subjects as $s=>$_)
					} // $IS_REMOTE_SPARQL_ENDPOINT
					####################################
					else // !$IS_REMOTE_SPARQL_ENDPOINT
					####################################
					{
						//Call the refine method inside $CLASS
						if ($DEBUG) $RDFLOG.= "<hr>";
						
						$broder_arr=$narrower_arr=$related_arr=array();
						foreach ($subjects as $s=>$uid_notused)
						{
							$counter++;
							Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>$msg="CALLING $servicename SRC $src_name on subject $s with max $max_subjects subjects"),$this->sid);
							
							if (trim($s))
							{
								###############################################################################
								$count_processed_subjects=count($processed_subjects);
								if ($processed_subjects_pro_src{$ID} + 1 > $C::$rdfp_MAX_SRC_SUBJECT_EXPANSION_PRO_SRC)		
								{
									if ($DEBUG)
									{
										$RDFLOG.=$xxx=htmlprint("<br>get_subjects_expansions_using_thesauri reached statical limit number of subjects to seek/expand pro SRC ($src_name) ({$C::$rdfp_MAX_SRC_SUBJECT_EXPANSION_PRO_SRC}) - breaking loop",'red');
										//print $xxx;
									}
									break;
								} // $count_processed_subjects > $C::$rdfp_MAX_SRC_SUBJECT_EXPANSION
								else if ($count_processed_subjects + 1 > $C::$rdfp_MAX_SRC_SUBJECT_EXPANSION) 
								{
									if ($DEBUG || 1)
									{
										$RDFLOG.=$xxx=htmlprint("<br>get_subjects_expansions_using_thesauri reached statical limit number of subjects to seek/expand ({$C::$rdfp_MAX_SRC_SUBJECT_EXPANSION}) - breaking loop",'red');
										//print $xxx;
										$enaugh_processed_subjects=true;
									}
									break;
								}
								###############################################################################
								if ($DEBUG) $RDFLOG.= "<br>CHECK $src_name on $s";
								//print "<br>using subject for SRC subexp: ($s)";
								//Incase some SRC had already answered for a subsuming subject ... break it there.
								if ($MONITORCALLS) print "<br>monitor ".($monitorcall_stp++)." call (LOCAL_SRC=$LOCAL_SRC) stp? SRC $src_name on $s ($count_processed_subjects succesfully processed subjects)";
								
								list($still_to_process,$subsuming_subject,$numdocs) = $this->subject_is_still_to_process($s,$processed_subjects);
								if ($still_to_process)
								{
									##########################################
									#
									# RDF annotated src use check
									#
									if ($MONITORCALLS) print "<br>monitor ".($monitorcall++)." call SRC $src_name on $s";
									
									$cache_id="$servicename.$src_name.$s.$lang.$max_subjects";
									list($age_of_last_src_use, $src_use, $srcuse_uid, $SKOSOBJ) = $this->check_rdf_annotated_last_src_subexp_use($src_name,$ID,$cache_id,$s);
									if ($DEBUG) $RDFLOG.=htmlprint("<br>ANALIZING SRC USE DATE: src_use=$src_use, age_of_last_src_use=$age_of_last_src_use, tolerated ({$C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC})",'blue');
									if ($src_use && $age_of_last_src_use <= $C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC)
									//Data is still fresh in triple store - no need of calling src
									{
										list($broader,$narrower,$related)=$SKOSOBJ;				
										$count_results=count($broader)+count($narrower)+count($related); 	
										Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>$msg="USING PERSISTED DATA ($count_results) FROM RDF SRC $src_name on subject $s with $max_subjects subjects"),$this->sid);
										if ($DEBUG) $RDFLOG.=htmlprint("<br>$msg",'green');
																			
										//Register results only if some data supplied
										if ($count_results) 
										{
											$expanded_subjects = 
												array(	$src_name,
																$srcuse_uid,
																$src_fresh_data=false,
																$broader,
																$narrower,
																$related	);
										}
									}
									else // src data too old or non existent in this way
									//CALL SRC
									{
										# provide / update ann information
										if (0) // dangerous ...
										$count_removed_triples= 
												$this->remove_srcuse($src_name,$ID,$cache_id,$ann_servicename,$s,$COUNTTRIPLES,$REMOVE_EFFECTIVITY_TESTING);
										
										list($srcuse_uid,$count_added_triples)= 
												$this->rdfannotate_add_src_use(	$src_name,
																												$ann_servicename,
																												$ID,
																												$cache_id,
																												$sds_sparql_endpoint,
																												$sds_sparql_endpoint,
																												$USER_ID,
																												$s,
																												$COUNTTRIPLES );
										
										//We have later to link triples results with $srcuse_uid
										//in order to make links between the annotation and the results
										//In case the refine method gets some results, 
										//do not ask the same service again for a subject 
										//which is contained in the previously sserved call if successful!
										
										Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>$msg="REQUESTING NEW DATA FROM RDF SRC $src_name (age=$age_of_last_src_use > {$C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC} limit) on subject $s with $max_subjects subjects"),$this->sid);
										if ($DEBUG) $RDFLOG.=htmlprint("<br>$msg",'red');
										
										if($FETCHDATAFROMSOURCE)
										{
											if($LOCAL_SRC)
											{
												if ($DEBUG) $RDFLOG.="<br>CALL SRC $src_name locally";
 												$CONTENT = get_from_src_directly( 
																													$sid,
																													$max_subjects,
																													$lang,
																													$servicename,
																													$s, // query
											
																													$src_name,
																													$mode='direct',
																													$DISKenginePATH,
																													$basic_path_sroot,
																													$basic_path_SRCengineInterface,
																													$basic_path_SRCengine,
																													$CLASS,
																													$pathClass,
																													$pathSuperClass,
																													$AuthUser,
																													$AuthPasswd  );
											}
											else
											{
												if ($DEBUG) $RDFLOG.="<br>CALL SRC $src_name remotely";
												$WEBSERVICE=$WEBROOT."$RODINROOT/$RODINSEGMENT/app/s/refine/index.php"
												.'?'.'sid='.$sid
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
												.'&'.'q='.base64_encode($s)
												;
						 				    $CONTENT=get_file_content($WEBSERVICE);
											}
										
											if ($DEBUG) $RDFLOG.="<br>CONTENT: ".print_r($CONTENT);
											$expanded_subjects = 
											list($src_name,$srcuse_uid,$src_fresh_data,$broader,$narrower,$related) = 
														$this->scan_src_results($CONTENT,$TERM_SEPARATOR,$src_name,$srcuse_uid,$s,$src_fresh_data=true,$WEBSERVICE);
											
											$count_results=count($broader) + count($narrower) + count($related);
											if ($DEBUG)
											{
												$RDFLOG.="<br>SCANNED RESULTS: ";
												if ((is_array($broader) && count($broader)))
												{
													$RDFLOG.="<br>".count($broader)." BROADERs: ";
													foreach($broader as $x) $RDFLOG.="$x, ";
												}
												else if (!is_array($broader))
												$RDFLOG.=htmlprint("<br>ERROR: broader ($broader) should be array",'red');
		
												if ((is_array($narrower) && count($narrower)))
												{
													$RDFLOG.="<br>".count($narrower)." NARROWERs: ";
													foreach($narrower as $x) $RDFLOG.="$x, ";
												}
												else if (!is_array($narrower))
												$RDFLOG.=htmlprint("<br>ERROR: narrower ($narrower) should be array",'red');
		
												if ((is_array($related) && count($related)))
												{
													$RDFLOG.="<br>".count($related)." RELATEDs: ";
													foreach($related as $x) $RDFLOG.="$x, ";
												}
												else if (!is_array($related))
												$RDFLOG.=htmlprint("<br>ERROR: related ($related) should be array",'red');
		
											} // DEBUG
										} // $GENERATE_TRIPLES
										if (!$count_results) $count_results=0; // we need a 0 not nix in the variable for Logger
										Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>$msg="EXIT NEW DATA ($count_results) FROM RDF SRC $src_name (age=$age_of_last_src_use > {$C::$rdfp_TOLERATED_SRC_LOD_DATA_AGE_SEC} limit) on subject $s with $max_subjects subjects"),$this->sid);
										if ($DEBUG) $RDFLOG.=htmlprint("<br>$msg",'red');
									} // CALL SRC
									
									//Add-register ONLY if some results effectively came:
									if ($count_results)
									{
										add_to_assocvector($skos_subject_expansion,$s,$expanded_subjects)	;					
										//$skos_subject_expansion{$s} = $expanded_subjects;
										$processed_subjects{$s}=$count_results; 
										$processed_subjects_pro_src{$ID}+=$count_results;
									}
								} // $still_to_process
								else {
									if ($DEBUG) print "<br> NOT TO PROCESS: $src_name on $s";
								}
							} // trim ($s)
							if ($COUNTTRIPLES) $count_all_added_localsrc_triples+=$count_removed_triples+$count_added_triples;
						} // foreach subjects
					} // !$IS_SPARQL_ENDPOINT
					if ($COUNTTRIPLES) $count_all_added_triples+=$count_all_added_endpoint_triples+$count_all_added_localsrc_triples;
					Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>"Exit $servicename SRC $src_name with $max_subjects subjects"),$this->sid);
				} // foreach($INITIALISED_SRCs as $INITIALISED_SRC)
			} // valid_src_parameters
			Logger::logAction(27, array('from'=>'get_subjects_expansions_using_thesauri','msg'=>'Exit'),$this->sid);
		} // $subjects
		return array($skos_subject_expansion,$count_all_added_triples);
		
	} // get_subject_related_to_from_thesauri
	
	
	
	/**
	 * list($broder_arr,$narrower_arr,$related_arr) = scan_src_results($CONTENT,$TERM_SEPARATOR)
	 */
	public function scan_src_results($CONTENT,$TERM_SEPARATOR,$src_name,$srcuse_uid,$subject,$src_fresh_data,$urlcall)
	{
		$DEBUG=0;
		global $RDFLOG;
		if ($CONTENT)
		{
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
				if ($DEBUG)
				fontprint("<hr>$src_name trying to scan"
										."<br>CACHE checked ?"
										."<br>On subject ($subject) "
										."<br>Used url: <b>".htmlentities($urlcall)."</b>"
									,'#aa4455');
				
				$IS_SRCEngineSKOSResult = (get_class($CONTENT)=='SRCEngineSKOSResult');
				
				if ($DEBUG)
				{
					print "<br>CONTENT (IS_SRCEngineSKOSResult=$IS_SRCEngineSKOSResult); "; var_dump($CONTENT);
				}
				
				if ($IS_SRCEngineSKOSResult)
				//Result is as object (no linearization)
				{
					if ($DEBUG)
					{
						$RDFLOG.="<br>scan_src_results exploding ";
						$RDFLOG.="<br>broader (".$CONTENT->broader->results.")";
						$RDFLOG.="<br>narrower (".$CONTENT->narrower->results.")";
						$RDFLOG.="<br>related (".$CONTENT->related->results.")";
					}
					
					$broder_arr		=	$CONTENT->broader->results? explode(',',$CONTENT->broader->results): array();
					$narrower_arr	=	$CONTENT->narrower->results? explode(',',$CONTENT->narrower->results): array();
					$related_arr	=	$CONTENT->related->results? explode(',',$CONTENT->related->results): array();
				} // $IS_SRCEngineSKOSResult
				else //scan linearized xml text into vectors
				{
					if ($DEBUG)
					{
						$RDFLOG.="<br>scan_src_results xml scanning ";
					}
					$sxmldom = simplexml_load_string($CONTENT,'SimpleXMLElement', LIBXML_NOCDATA);
					if(!$sxmldom)
					{
						fontprint("<br>Exception scanning content of <b>$urlcall</b>",'red');
						fontprint("<br>CONTENT:<br>(".htmlentities($CONTENT).")<br>",'#aa2222');
					}
					
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
			} // scan xml
		}
else {
	if ($DEBUG)
		print "<br>scan_src_results(): no CONTENT";
}
		return array($src_name,$srcuse_uid,$src_fresh_data,$broder_arr,$narrower_arr,$related_arr);
	} // scan_src_results
	
	
	
	
	
	
	
	/**
	 * Returns an assoc where
	 * s = the entity subject
	 * value= list of triples (redundant) related to s
	 */
	public function regroup_triple_to_entities(&$triples)
	{
		$entities = array();
		
		if (count($triples))
		foreach($triples as $T)
		{
			list($s,$p,$o) = $T;
			add_to_assocvector($entities,$s,$T);
		}
	
		return $entities;
	} // regroup_triple_to_entities
	
	
	
	
	
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
			//A more complicated subject delivered already documents.
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
		if (RDFprocessor::$store)
		{
			$dce_ns_url=RDFprocessor::$NAMESPACES{'dce'};
			$rdf_ns_url=RDFprocessor::$NAMESPACES{'rdf'};
			$rodin_ns_url=RDFprocessor::$NAMESPACES{'rodin'};
			
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
			if (($rows = RDFprocessor::$store->query($sparql_query, 'rows')))
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
	 * Adds meta information on imported docs and subjects
	 * 
	 * NEW: TODO: Foreach document, a single records must be reassembled out of the gathered triples
	 * 
	 * @param triples array $triples - the triples
	 * @param $orig_subject_uid - the original subject for which the current LOD fetch was done
	 * @param string $src_name - The name of the refining component
	 */
	public function homogenize_foreign_RDFentities(&$RDFentities,&$rodin_subjects,$orig_subject_uid,$src_name,$src_uid,$lang)
	{
		$DEBUG=0;
		global $RDFLOG;
		global $WANT_RDF_ANNOTATION;
		$imported_docs=
		$imported_subjects=
		$imported_new_subjects=
		$imported_but_existing_subjects=
														array();
		$C=get_class($this);
				
		$lang=$this->searchtermlang;
		
		if($DEBUG) $RDFLOG.= "<hr><b>homogenize_foreign_triples for ($src_name):<br>";
		
		switch (strtolower($src_name))
		{
			#########################################
			case 'europeana';
			#########################################
			if (count($RDFentities))
			{
				foreach($RDFentities as $RDFentityID=>$triplelist)
				{
					if (count($triplelist))
					{
						foreach($triplelist as $triple)
						{
							list($s,$p,$o) = $triple;
							$add_triple=true;
							$i++;
							if ($DEBUG) print "<br>HOMOG $i. triple ($s)($p)($o)";
							
							$new_p=$p; $new_o=$o;
							if (strstr($s,'epp:'))
							{
								$new_s='rodin_e:'.str_replace('epp:','',$s);
								if ($DEBUG) $RDFLOG.="<br>DOC: $new_s";
							}
							##################################
							if ($p=='rdf:type' && $o=='ore:terms/Proxy')
							##################################
							{
								//change this triple: We need the "real" type. not a proxy trick like in europeana	
								$homogenized_triples[]= $T = array($new_s,$new_p,'dce:BibliographicResource');
							  if ($WANT_RDF_ANNOTATION) $homogenized_triples[]=array($src_uid,'rodin_a:delivered',freeze_triple_as_literal_metaobject($T));
								
								if ($WANT_RDF_ANNOTATION)
								{
									##########################################################################
									#
									# ANNOTATION SECTION - SUPPLY
									#
									//Annotation - when loaded
									//Annotate new doc as rodin_a:expdoc
									//And annotate the same triple representation as literal
									if ($src_uid)
									{
										  $homogenized_triples[]= $T = array($src_uid,'rodin_a:expdoc',$new_s);
										  if ($WANT_RDF_ANNOTATION) $homogenized_triples[]=array($src_uid,'rodin_a:delivered',freeze_triple_as_literal_metaobject($T));
									}
								}
								$imported_docs[]=$new_s;
								
								if($DEBUG)
								{
									$RDFLOG.= "<br>HOMOG EXPANDED DOC: $new_s";
								}
								
								$hdocscount++;
							}
							##################################
							else if ($p=='dce:title' 
										|| $p=='dce:description' )
							##################################
							{
								// HERE WE TAKE TITLES AND DESCRIPTIONS
								$new_o = l64($o,$p,RDFprocessor::$TOBECODED64);
								if (0) 	// DEACTIVATE IT, LANGUAGE DETECTOR IS BAD, 
												// KEEP TITLES / DESCRIPTIONS IN ALL LANGUAGES!
								{
									if($DEBUG) 
									{
										$RDFLOG.="<br>$p: ".$o;
										$RDFLOG.="<br>$p: ".$new_o;
									}
									//Only the SAME language!
									//Extra-Language Detection takes 5% of the whole computation time
									$langt='';
									if (($langt=detectLanguageAndLog($o,'homogenize_foreign_RDFentities titles and/or descriptions',$this->sid))==$lang)
									{
										if($DEBUG || 1)  
											 $RDFLOG.= "<br>TAKE($langt==$lang) OBJ($p = $o):";
									}
									else 
									{
										$add_triple=false;
										if($DEBUG || 1)  
													$RDFLOG.= htmlprint("<br>SUPPRESS($langt<>$lang) OBJ($p = $o):",'red');
									}					
								}			
							}				
							##################################
							else if ($p=='dce:creator')
							##################################
							{
								$new_o=l64($o,$p,RDFprocessor::$TOBECODED64); 
							}
							##################################
							else if ($p=='dce:subject')
							##################################
							//
							// New external subjects are coming here ... to RODIN local store
							// They must be traceable to be removed on src call (when data too old)
							{
								//print "<br>PROC external dce:subject: $o";
								$o=l_inverse($o); //eliminate douple quote from literal for reuse of the same
								
								if(!($subject_uid=$rodin_subjects{$o})) // subject is already present (from another same homog LOD or from RODIN result?)
								{
									//$o should not contain double quote...
									//Construct a rodin subject
									if ($DEBUG) $RDFLOG.="<br>new expanded subject ($o)";
									$subject_uid='rodin:'.RDFprocessor::adapt_name_for_uid($o);
									//Generate a rodin:label $o 
									
									$o = l64($o,$p,RDFprocessor::$TOBECODED64); 
									
									//TODO: Bind the following two $subject_uid to the own src_use ... 
									//The following entries could be already present inside RODIN RDF local store
									//They should be carefully deleted on remove, considering if
									//there is a trace ("inferred") from another meta object to these subjects
									$homogenized_triples[]=$T=array($subject_uid,	'rodin:label', 	l($o)); 
									if ($WANT_RDF_ANNOTATION) $homogenized_triples[]=array($src_uid,'rodin_a:delivered',freeze_triple_as_literal_metaobject($T));
									
									$homogenized_triples[]=$T=array($subject_uid,	'rdf:type', 	'dce:subject'); 
									if ($WANT_RDF_ANNOTATION) $homogenized_triples[]=array($src_uid,'rodin_a:delivered',freeze_triple_as_literal_metaobject($T));

									$homogenized_triples[]=$T=array($orig_subject_uid,	'rodin:subject_related', 	$subject_uid); 
									if ($WANT_RDF_ANNOTATION) $homogenized_triples[]=array($src_uid,'rodin_a:delivered',freeze_triple_as_literal_metaobject($T));
									//Mark imported subject (only on really new subjects)
									$imported_new_subjects{l_inverse($o)}=$subject_uid;
									
									
									if ($WANT_RDF_ANNOTATION)
									{
										##########################################################################
										#
										# ANNOTATION SECTION - SUPPLY "materials"
										#
										//Annotation - when loaded
										//Annotate new subject as rodin_a:lodexp_related
										//Annotate triple as delivered
										if ($src_uid)
										{
											$homogenized_triples[]=$T=array($src_uid,'rodin_a:lodexp_related',$subject_uid);
											$homogenized_triples[]=array($src_uid,'rodin_a:delivered',freeze_triple_as_literal_metaobject($T));
										}
									}
									
									
									
								} else {
									$imported_but_existing_subjects{$o}=$subject_uid;
								}
								
								//Assert dce:subject in rodin_space
								$homogenized_triples[]=$T=array($new_s,	$new_p, 								$subject_uid); 
								if ($WANT_RDF_ANNOTATION) $homogenized_triples[]=array($src_uid,'rodin_a:delivered',freeze_triple_as_literal_metaobject($T));
								
								$homogenized_triples[]=$T=array($new_s,	'rodin:writes_about', 	$subject_uid); 
								if ($WANT_RDF_ANNOTATION) $homogenized_triples[]=array($src_uid,'rodin_a:delivered',freeze_triple_as_literal_metaobject($T));
								
								
								//DO NOT ADD ANY further triple
								$add_triple=false;
							} //dce:subject 
							##################################
							else if ($p=='dce:type' || $p=='edm:type')
							##################################
							{
								$new_p=$p;
								$new_o=$o;
								$add_triple=true;
							}
							//TODO: check double works ...	
							//TODO: Link Work with subject
							//TODO: Link Subject (if possible or create)
							//Is the subject literal linked with a rodin-literal (subject)?
							//if yes: change the literal to a rodin: link to this element
							//if not: create a rodin:subject to this literal (to be related with that subject)
			
							
							if ($add_triple)
							{
								$homogenized_triples[]=$T=array($new_s,$new_p,$new_o);
								if ($WANT_RDF_ANNOTATION) $homogenized_triples[]=array($src_uid,'rodin_a:delivered',freeze_triple_as_literal_metaobject($T));
								if ($DEBUG) 
									$RDFLOG.="<br>HOMOGENIZE: ADD ($new_s)($new_p)($new_o)";
							}
						}	// foreach $triples		
					} // check count triples
				} // foreach RDFentitiy
			} // count RDFentities
			break; //europeana
			#########################################
			default: $homogenized_triples=$triples;
		}
				
		//foreach($hdocs as $hdocsid) $RDFLOG.="<br>HOMG END EXPANDED DOC $hdocsid";		
				
		return array($homogenized_triples,$imported_docs,$imported_new_subjects,$imported_but_existing_subjects);
	} // homogenize_foreign_RDFentities
	
	
	
	
	
	
	
	
	
	
	/**
	 * removes all triples corresponding to the current search values
	 */
	
	public function remove_unused_result_triples_on_search($searchterm, &$store, &$NAMESPACES, $USER_ID)
	{
		global $RDFLOG;
		global $WANT_RDF_ANNOTATION;
		$DEBUG=0;
		
		if ($DEBUG)
	  	$RDFLOG.="<br>remove_unused_result_triples_on_search() on search='$searchterm' user: ".$USER_ID;
			Logger::logAction(27, array('from'=>'rdfMetaControl','msg'=>$msg="START remove_unused_result_triples_on_search"),$this->sid);
		
		$rodin_a_ns_url =$NAMESPACES{'rodin_a'};
		
	/**
	 * A search is recorded in the RDF store with a SUBTREE of 3 node levels.
	 * ACCESSING all the subtree elements up to level 3 reaches all produced triples
	 */ 
$QUERY_DELETE=<<<EOQ
PREFIX rodin_a: <$rodin_a_ns_url>
delete {?s ?p ?o}
{
	{
	  ?s rodin_a:search_term "$searchterm" .
	  ?s ?p ?o .
	}
	UNION
	{
	  ?sm1 rodin_a:search_term "$searchterm" .
	  ?sm1 ?pm1 ?om1 .
	  FILTER (?om1 = ?s ) .
	  ?s ?p ?o .
	}
	UNION
	{
	  ?sm2 rodin_a:search_term "$searchterm" .
	  ?sm2 ?pm2 ?om2 .
	  FILTER (?om2 = ?sm1 ) .
	  ?sm1 ?pm1 ?om1 .
	  FILTER (?om1=?s) .
	  ?s ?p ?o .
	}
}
EOQ;
		
		//on 3 results this subtree is deleted/reconstructed each time?
		if ($store)
		{
			if ($DEBUG) $RDFLOG.= "<br>deleting: ".htmlentities($QUERY_DELETE);
			$store->query($QUERY_DELETE);
		}
		Logger::logAction(27, array('from'=>'rdfMetaControl','msg'=>$msg="END remove_unused_result_triples_on_search"),$this->sid);
		
	} // remove_unused_result_triples_on_search
	
	
	
	
	
	/**
	 * Construct an ser use in triples inside the local store
	 * returns $srcuse_uid
	 */
	public function rdfannotate_add_src_use(	$src_name,
																						$ann_servicename,
																						$src_id,
																						$cache_id,
																						$src_url,
																						$src_special_parameters,
																						$user_id,
																						$subject,
																						$COUNTTRIPLES )
	{
		//TODO
		global $RDFLOG;
		global $WANT_RDF_ANNOTATION;
		$count_added_triples=0;
		
		if($WANT_RDF_ANNOTATION)
		{		
			Logger::logAction(27, array('from'=>'rdfMetaControl','msg'=>$msg="START rdfannotate_add_src_use($src_name)"),$this->sid);
			if ($COUNTTRIPLES) $count_triples_before=count_ARC_triples($this->store);

			$C=get_class($this);	
			if ($DEBUG) $RDFLOG.="<br>rdfannotate_add_src_use() on src_name=$src_name, src_id=$src_id, ann_servicename=$ann_servicename, subject=$subject'";
			
			list($timestamp,$timestamp4h)=timestamp_for_rdf_annotation();
			$srcuse_uid="rodin_a:src_{$src_id}_{$ann_servicename}_{$timestamp}";
			
			$triples=array();
			$triples[]=array($srcuse_uid,'rdf:type','rodin_a:src_use');
			$triples[]=array($srcuse_uid,'rodin_a:src_id',l($src_id));
			$triples[]=array($srcuse_uid,'rodin_a:src_name',l($src_name));
			$triples[]=array($srcuse_uid,'rodin_a:supplied',l($timestamp));
			$triples[]=array($srcuse_uid,'rodin_a:supplied4h',l($timestamp4h));
			
			//We save time now and omit these info:
			if($deakt)
			{
				$triples[]=array($srcuse_uid,'rodin_a:src_url',l($src_url));
				$triples[]=array($srcuse_uid,'rodin_a:userid',l($user_id));
			}
			$triples[]=array($srcuse_uid,$p='rodin_a:cache_id',l64($cache_id,$p,$C::$TOBECODED64));
			$triples[]=array($srcuse_uid,$p='rodin_a:cache_id_txt',l($cache_id));
			
			//Add paremeter information (if needed??)
			if($deakt)
			{
				$triples[]=array($srcuse_uid,'rodin_a:parameter',$paramsubject='rodin_a:'."srcp_{$src_id}_service{$ann_servicename}_{$timestamp}_subject");
				$triples[]=array($paramsubject,$p='rodin_a:pvalue',l64('rodin_a:'.$subject,$p,$C::$TOBECODED64));
			}	
	
			$this->import_triples($triples);
			if ($COUNTTRIPLES) $count_triples_after=count_ARC_triples($this->store);
			
		}

		if ($COUNTTRIPLES) $count_added_triples = $count_triples_after - $count_triples_before;
		Logger::logAction(27, array('from'=>'rdfMetaControl','msg'=>$msg="END rdfannotate_add_src_use($src_name)"),$this->sid);
		return array($srcuse_uid,$count_added_triples);
	} // rdfannotate_add_src_use





	/**
	 * Retrieve the delivered triples and delete them
	 */
	public function remove_srcuse($src_name,$src_id,$cache_id,$ann_servicename,$subject,$COUNTTRIPLES,$REMOVE_EFFECTIVITY_TESTING=false)
	{
		global $RDFLOG;
		global $WANT_RDF_ANNOTATION;
		$DEBUG=0;
		$ok=true;
		$C = get_class($this);
		if ($DEBUG) $RDFLOG.="<hr><b>remove_srcuse()</b><br>on src_name=$src_name, src_id=$src_id, cache_id=$cache_id, ann_servicename=$ann_servicename, subject=$subject'";
		$rodin_a_ns_url =$C::$NAMESPACES{'rodin_a'};
		$cache_id=($C::$TOBECODED64{'rodin_a:cache_id'}?base64_encode($cache_id):$cache_id);
		if ($COUNTTRIPLES) $count_triples_before=count_ARC_triples($this->store);

		Logger::logAction(27, array('from'=>'rdfMetaControl','msg'=>$msg="START remove_srcuse($src_name)"),$this->sid);
		
		###################################
		###################################
		if ($ann_servicename=='lodfetch')
		###################################
		###################################
		{
			##########################################################
			# Look at homogenize_foreign_RDFentities() - section on dce:subjects
			# for detailed information on what to remove from RDF store
		
			$triples = rdf_get_delivered_triples($cache_id, $this->store, RDFprocessor::$NAMESPACES, $REMOVE_EFFECTIVITY_TESTING);
			if (is_array($triples) && count($triples))
			{
				rdf_delete_triples($triples,$this->store,$REMOVE_EFFECTIVITY_TESTING);
			}
		} // $ann_servicename=='lodfetch'
		###################################
		###################################
		else if ($ann_servicename=='subexp')
		###################################
		###################################
		{
			##########################################################
			# Look at rdfize_subexp() - section on dce:subjects
			# for detailed information on what to remove from RDF store
			
			$triples = rdf_get_delivered_triples($cache_id, $this->store, RDFprocessor::$NAMESPACES, $REMOVE_EFFECTIVITY_TESTING);
			if (is_array($triples) && count($triples))
			{
				rdf_delete_triples($triples,$this->store,$REMOVE_EFFECTIVITY_TESTING);
			}
		}
		
		//Delete the SRC USE COMPLETELY
		rdf_delete_entity_on_cacheid($cache_id, $this->store, RDFprocessor::$NAMESPACES);
		if ($COUNTTRIPLES) $count_triples_after=count_ARC_triples($this->store);
		if ($COUNTTRIPLES) $count_deleted_triples = $count_triples_after - $count_triples_before;
		
		Logger::logAction(27, array('from'=>'rdfMetaControl','msg'=>$msg="END remove_srcuse($src_name)"),$this->sid);
		
		return $count_deleted_triples;
	} // remove_srcuse
		
		
	
	
	
	
	/**
	 * Check the src use corresponding to cache_id
	 * Returns labels of generated subjects
	 * 
	 * @param $src_name
	 * @param $src_id
	 * @param $cache_id
	 * @param $subject
	 */
	public function check_rdf_annotated_last_src_subexp_use($src_name,$src_id,$cache_id,$subject)
	{
		$DEBUG=0;
		global $RDFLOG;
		global $WANT_RDF_ANNOTATION;
		$ok=true;
		$ann_servicename='subexp';
		if ($cache_id=='') fontprint("<br>check_rdf_annotated_last_src_subexp_use($src_name,$src_id,$cache_id,$subject) - EMPTY CACHE_ID passed!",'red');
		$C = get_class($this);
		if ($DEBUG) $RDFLOG.="<br>check_rdf_annotated_last_src_subexp_use() on src_name=<b>$src_name</b>, src_id=$src_id, cache_id=$cache_id, ann_servicename=$ann_servicename, subject=<b>$subject</b>)'";
		$rodin_ns_url =$C::$NAMESPACES{'rodin'};
		$rodin_a_ns_url =$C::$NAMESPACES{'rodin_a'};
		$cache_id=($C::$TOBECODED64{'rodin_a:cache_id'}?base64_encode($cache_id):$cache_id);
		$sparql_metaquery=<<<EOS
PREFIX rodin: <$rodin_ns_url>
PREFIX rodin_a: <$rodin_a_ns_url>
select  ?s ?p ?o ?l
{
  ?s rodin_a:cache_id "$cache_id" .
  ?s ?p ?o .
  OPTIONAL { ?o rodin:label ?l }
}
EOS;
//  FILTER( ?p != rodin_a:cache_id ) .
		if ($DEBUG) $cache_id_decoded = base64_decode($cache_id);
		$related_subjects=$broader_subjects=$narrower_subjects=array();
		if ($this->store)
		{
			if ($DEBUG) $RDFLOG.= "<br>querying for subject '<b>$subject</b>' and cache_id=$cache_id_decoded: <br><b>".str_replace("\n",'<br>',htmlentities($sparql_metaquery)).'</b>';
			if (($rows = $this->store->query($sparql_metaquery, 'rows')))
			{
				$cr=count($rows);
				if ($DEBUG) $RDFLOG.= "<br>$cr TRIPLES found!";
				foreach($rows as $row)
				{
					//$s_full = $row['s'];
					$p_full = $row['p'];
					$o_full = $row['o'];
					
					//$s = separate_namespace($C::$NAMESPACES,$s_full,':',false);
					$p = separate_namespace($C::$NAMESPACES,$p_full,':',false);
					$o = separate_namespace($C::$NAMESPACES,$o_full,':',false);
					
					if ($DEBUG) $RDFLOG.="<br>(p,o)=($p)($o)";
					//Take the supplied time:
					if (strstr($p,'supplied'))
					{
						$supplied=$o;
						$s_full = $row['s'];
						$s = separate_namespace($C::$NAMESPACES,$s_full,':',false);
						$src_use_uid=$s; // get once the src_use_id
					}
					else if (strstr($p,'subexp'))
					{
						$subjectfound++;
						//We need only the label, discard namespace abbr.
						$o_label = $row['l'];
						if (strstr($p,'related'))
							$related_subjects[]=$o_label;
						else if (strstr($p,'broader'))
							$broader_subjects[]=$o_label;
						else if (strstr($p,'narrower'))
							$narrower_subjects[]=$o_label;
					}
					else if (strstr($p,'cache_id'))
					{
						$cache_id_decoded=base64_decode($o);
					}
				} // foreach
			}	// results
			else {
				if ($DEBUG) $RDFLOG.= "<br>NO TRIPLES found!";
			}
			
			if($DEBUG && $subjectfound) $RDFLOG.=htmlprint("<br>$subjectfound SUBJECTS FOUND IN RDF STORE",'green');
		} //store
		
		
		
		#compute age from now
		#$supplied= timstamp with microseconds = "1365801457_721488"
		$age_sec = compute_rdf_age($supplied);
		
		$src_use = ($supplied<>null);
		if ($DEBUG)
		{
			$RDFLOG.="<br>supplied($cache_id_decoded)= $supplied => age= $age_sec secs";
			
			if (count($related_subjects))
			{
				if ($DEBUG) $RDFLOG.="<br>related subjects from triples: ";
				foreach($related_subjects as $s) $RDFLOG.="<br>$s";	
			}
			if (count($broader_subjects))
			{
				if ($DEBUG) $RDFLOG.="<br>broader subjects from triples: ";
				foreach($broader_subjects as $s) $RDFLOG.="<br>$s";	
			}
			if (count($narrower_subjects))
			{
				if ($DEBUG) $RDFLOG.="<br>narrower subjects from triples: ";
				foreach($narrower_subjects as $s) $RDFLOG.="<br>$s";	
			}
		} // debug
		
		$SKOSOBJ=array($broader_subjects,$narrower_subjects,$related_subjects);
		
		// DO NOT REMOVE A TRIPLE IF IT IS (transitively) REFERENCED BY ANOTHER SEARCH !!!!
		if ($DEBUG) $RDFLOG.="<br>RETURNING age_sec=$age_sec, src_use=$src_use,src_use_uid=$src_use_uid, $SKOSOBJ)";
		return array($age_sec,$src_use,$src_use_uid,$SKOSOBJ);
	} // check_rdf_annotated_last_src_subexp_use
	
	
	
	
	
	
	
	
	
	
	
	public function check_rdf_annotated_last_src_lodfetch_use($src_name,$src_id,$cache_id,$subject)
	{
		$DEBUG=0;
		global $RDFLOG;
		global $WANT_RDF_ANNOTATION;
		$ok=true;
		$further_expandeddocs =
		$further_expanded_new_subjects = array();
		
		if ($cache_id=='') fontprint("<br>check_rdf_annotated_last_src_lodfetch_use($src_name,$src_id,$cache_id,$subject) - EMPTY CACHE_ID passed!",'red');
		$related_subjects=$broader_subjects=$narrower_subjects=array();
		$ann_servicename='lodfetch';
		$C = get_class($this);
		if ($DEBUG) $RDFLOG.="<hr>check_rdf_annotated_last_src_lodfetch_use() on src_name=$src_name, src_id=$src_id, cache_id=$cache_id, ann_servicename=$ann_servicename, subject=$subject'";
		$rodin_a_ns_url =$C::$NAMESPACES{'rodin_a'};
		$cache_id=($C::$TOBECODED64{'rodin_a:cache_id'}?base64_encode($cache_id):$cache_id);
		$sparql_metaquery=<<<EOS
PREFIX rodin_a: <$rodin_a_ns_url>
select  ?s ?p ?o
{
  ?s rodin_a:cache_id "$cache_id" .
  ?s ?p ?o .
}
EOS;
//  FILTER( ?p != rodin_a:cache_id ) .
		
		if ($this->store)
		{
			if ($DEBUG) $RDFLOG.= "<hr><b>check_rdf_annotated_last_src_lodfetch_use querying: <br>".str_replace("\n",'<br>',htmlentities($sparql_metaquery)).'</b>';
			if (($rows = $this->store->query($sparql_metaquery, 'rows')))
			{
				$rc=count($rows);
				if ($DEBUG) $RDFLOG.="<br>$rc TRIPLES found";
				foreach($rows as $row)
				{
					//$s_full = $row['s'];
					$p_full = $row['p'];
					$o_full = $row['o'];
					
					//$s = separate_namespace($C::$NAMESPACES,$s_full,':',false);
					$p = separate_namespace($C::$NAMESPACES,$p_full,':',false);
					$o = separate_namespace($C::$NAMESPACES,$o_full,':',false);
					
					if ($DEBUG) $RDFLOG.="<br>(p,o)=($p)($o)";
					//Take the supplied time:
					if (strstr($p,'supplied'))
					{
						$supplied=$o;
						$s_full = $row['s'];
						$s = separate_namespace($C::$NAMESPACES,$s_full,':',false);
						$src_use_uid=$s; // get once the src_use_id
					}
					else if (strstr($p,'expdoc'))
					{
						$further_expandeddocs[]= ($o);
					}
					else if (strstr($p,'lodexp_related'))
					{
						$further_expanded_new_subjects[]= ($o);
					}
					else if (strstr($p,'cache_id'))
					{
						$cache_id_decoded=base64_decode($o);
					}
				} // foreach
			}	// results
			else {
				if ($DEBUG) $RDFLOG.="<br>NO TRIPLES found";
			}
		} //store
				
		#compute age from now
		#$supplied= timstamp with microseconds = "1365801457_721488"
		$age_sec = compute_rdf_age($supplied);
		if ($DEBUG) $RDFLOG.="<br>supplied($cache_id_decoded)= $supplied => age= $age_sec secs";
		$src_use = ($supplied<>null);
		
		// DO NOT REMOVE A TRIPLE IF IT IS (transitively) REFERENCED BY ANOTHER SEARCH !!!!
		if ($DEBUG) $RDFLOG.="<br>check_rdf_annotated_last_src_lodfetch_use RETURNING age_sec=$age_sec, src_use=$src_use,src_use_uid=$src_use_uid, $numexpandeddocs)";
		return array(	$age_sec,
									$src_use,
									$src_use_uid, 
									$further_expandeddocs,
									$further_expanded_new_subjects );
	} // check_rdf_annotated_last_src_lodfetch_use 
		
	
	
	
	/**
	 * fetches every document related to the current search (widget or lod document)
	 * assert a (newer) rank annotation bound with the corresponding supply stucture
	 * 
	 * r(d)= rank(document) = number of subjects to witch it is related.
	 * 
	 */
	 
	public function rerankadd_rdf_documents_related_to_search($sid,$datasource,$searchterm,$USER_ID)
	{
		global $RDFLOG;
		$DEBUG=0;
		$C = get_class($this);
		$rodin_ns_url =$C::$NAMESPACES{'rodin'};
		$dce_ns_url =$C::$NAMESPACES{'dce'};
		$rodin_a_ns_url =$C::$NAMESPACES{'rodin_a'};
		$rodin_e_ns_url =$C::$NAMESPACES{'rodin_e'};
 		$searchuid=	'rodin_a:'.RDFprocessor::metasearch_uid($sid);
		$PREFIX1="PREFIX rodin_a: <$rodin_a_ns_url> ";
		$PREFIX2="PREFIX rodin: <$rodin_ns_url> "
						."PREFIX rodin_a: <$rodin_a_ns_url> "
						."PREFIX dce: <$dce_ns_url> "
						;
		$PREFIX3="PREFIX rodin: <$rodin_ns_url> "
						."PREFIX dce: <$dce_ns_url> "
						;
		$PREFIX4="PREFIX rodin_e: <$rodin_e_ns_url> "
						."PREFIX dce: <$dce_ns_url> "
						;
		//Get result documents direct from search (in $o)
		list($_,$docs) = get_triple_objects(	$searchuid,
																					'rodin_a:resultdoc',
																					$this->store,
																					$C::$NAMESPACES_PREFIX,
																					$PREFIX1		);		
		
		//get external document related to $sid
		$docs_ext = get_external_rdf_docs($searchuid,$C,$PREFIX2);		
		
		//Compute the number of subj each document
		
		Logger::logAction(27, array('from'=>'rdfLODfetchDocumentsOnSubjects','msg'=>$msg="START RERANK"),$this->sid);
		
		$ranked_docs= rank_docs_with_its_subjects($docs,$C,$PREFIX3);
		$ranked_docs_ext= rank_docs_with_its_subjects($docs_ext,$C,$PREFIX4);

		Logger::logAction(27, array('from'=>'rdfLODfetchDocumentsOnSubjects','msg'=>$msg="END RERANK"),$this->sid);
		
		if ($DEBUG)
		{
			$RDFLOG.="<br>rerankadd_rdf_documents_related_to_search:";
			$c=count($ranked_docs);
			$RDFLOG.="<br>$c Internal docs:"; //print "<br>$c Internal docs:<br>"; var_dump($ranked_docs);
			foreach($ranked_docs as $docuid=>$rank)
			{
				$RDFLOG.="<br> $docuid=>$rank";
			}
			
			$c=count($ranked_docs_ext);
			$RDFLOG.="<br>$c External docs:"; //print "<br>$c External docs:<br>"; var_dump($ranked_docs_ext);
			foreach($ranked_docs_ext as $docuid=>$rank)
			{
				$RDFLOG.="<br> $docuid=>$rank";
			}
		}
		
		//Adjust the rank annotation of each doc
		Logger::logAction(27, array('from'=>'rdfLODfetchDocumentsOnSubjects','msg'=>$msg="START WRES ASSEMBLING"),$this->sid);
		
		//foreach($ranked_docs as $docuid=>$rank)
		{
			$C=get_class($this);
			$rank = rank_my_doc_with_its_subjects($result,$this,$PREFIX3);
			// rank the result corresponding to this doc with $rank
			$result=$this->my_result;
			$result->setRank($rank);
			RodinResultManager::saveRodinResults($allResults=array($result), $this->sid, $datasource=$result->getProperty('datasource'), $timestamp='');
		} // foreach $ranked_docs
		
		
		//$this->adjust_rank_annotation($ranked_docs);
		//$this->adjust_rank_annotation($ranked_docs_ext);
		foreach($ranked_docs_ext as $docuid=>$rank)
		{
			$rodin_result_type='';
			//Gather data to build result info
			//from RDF store
			$p_o = get_entity_infos($this,$docuid,true);
			if (is_array($p_o) && count($p_o))
			{
				$RDFLOG.="<br><br>rerankadd_rdf_documents_related_to_search()<br>Assembling info for external doc $docuid:";
				foreach($p_o as $p=>$ooo)
				{
					$RDFLOG.="<br>($p) => ";
					
					if (is_array($ooo))
					{
						$RDFLOG.= "[";
						foreach($ooo as $ovalue) $RDFLOG.= "(".$ovalue[0].")";
						$RDFLOG.= "]";
					}
					
					
					switch ($p)
					{
						case 'dce:type': 
									if ($ooo[0][0]=='pdf' 
									|| $ooo[0][0]=='Thesis'
									|| $ooo[0][0]=='Article'
									|| $ooo[0][0]=='Text'
									|| $ooo[0][0]=='czasopismo'
									|| 1
									) 
										 $rodin_result_type = 'article';
									if (!$rodin_result_type)
										fontprint("<br>rerankadd_rdf_documents_related_to_search() Error mapping LOD document types to rodin result types (".$ooo[0][0]." ?)",'red');
									break;
						case 'dce:date': 
									$date_created = $ooo[0][0];
									break;
						case 'dce:title':
									$title=decode_64_literal($p,$ooo[0][0],$C::$TOBECODED64);
									break;
						case 'dce:description':
									$description=decode_64_literal($p,$ooo[0][0],$C::$TOBECODED64);
									break;
						case 'dce:identifier':
									if (count($ooo)>=1)
										$source_url = $ooo[0][0];
									if (count($ooo)>1)
										$identifier_url = $ooo[1][0];
									break;
						case 'dce:creator':
									foreach($ooo as $ovalue)
										$creators[]=decode_64_literal($p,$ovalue[0],$C::$TOBECODED64);
									$authors=array('creator'=>$creators);
									break;
						case 'dce:subject':
									foreach($ooo as $ovalue)
										$subjects[]=read_rodin_label($ovalue[0],$this);
									break;
											
					} // switch
				} // foreach pair
				//add once -> yes but at the moment SOLR stores it once ... even on multiple calls
				//Do not forget: we might still get some uncomplete information (through triple limit)
				
				if ($rodin_result_type)
				{
					if ($title==$oldtitle)
					{
						 $RDFLOG.=htmlprint("<br>rerankadd_rdf_documents_related_to_search():<br>prevent adding doc because title ($title) already added",'red');
					}
					else // do add
					{
						$R = RodinResultManager::create_rodinResult_for_lod(  $rodin_result_type,
																																	$rank,
																																	$title,
																																	$description,
																																	$date_created,
																																	$source_url,
																																	$identifier_url,
																																	$authors,  // $authorFieldNames = array('creator'=>, 'person'=>, 'contributor'=>);
																																	$subjects  );
																													
						RodinResultManager::saveRodinResults($allResults=array($R), $this->sid, $datasource='extern', $timestamp='');
						$oldtitle=$title;
						$added_documents++;
						
						if ($added_documents >= $C::$rdfp_MAX_LOD_DOC_ADD)
						{
							$RDFLOG.=htmlprint("<br>rerankadd_rdf_documents_related_to_search():<br>breaking adding loop to $added_documents because of limit of (".$C::$rdfp_MAX_LOD_DOC_ADD.") max docs to add",'red');
							break;
						}
					}
				}
			}
			else 
				$RDFLOG.=htmlprint("<br>Error adding external document ($docuid)",'red');
		} // $ranked_docs_ext
		Logger::logAction(27, array('from'=>'rdfLODfetchDocumentsOnSubjects','msg'=>$msg="END WRES ASSEMBLING"),$this->sid);
		return $added_documents;
	}
	// rerankadd_rdf_documents_related_to_search
	
	
	
	
	/**
	 * Returns a jpeg with a triple representation
	 * TODO: In case we divide a store by user ... 
	 */
	public function get_jpeg_display_store_with_graphviz($searchterm='')
	{
		$C = get_class($this);
		
		$viz = ARC2::getComponent('TriplesVisualizerPlugin', $this->LOCALARCCONFIG);
		
		$triples= get_ARC_triples_for_viz($this->store,$searchterm,$C::$NAMESPACES);
		
		$tc=count($triples);
		
	  $png = $viz->draw($triples, 'png', 'base64');
		
		return $png;
	} //display_store_with_graphviz
	
	
	
	
	public function adjust_rank_annotation(&$ranked_docs)
	{
		//TODO
		print "<br>TODO: adjust_rank_annotation";
	}
	
} // RDFprocessor
