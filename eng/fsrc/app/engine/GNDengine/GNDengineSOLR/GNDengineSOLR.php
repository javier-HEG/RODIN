<?php

	# GND Engine SOLR 
	#
	# Jan 2013
	# Author: fabio.ricci@ggaweb.ch  
	# HEG 

$THISFILE=__FILE__;
$THISCLASSNAME = basename(dirname($THISFILE)); // 
$BASECLASSNAME = basename(dirname(dirname($THISFILE))); // 

#Automatically load upper class
$filename="$BASECLASSNAME.php"; $max=10;
#######################################
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}


#Automatically load upper class
$filename="app/u/SOLRinterface/solr_interface.php"; $max=10;
#######################################
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

	
class GNDengineSOLR extends GNDengine
{
	protected $maxROWSinSOLR_eachEntity;
  protected $GND_SKOS_FIELDS;
  protected $solr_collection;
	protected $namespaces;
  
	function __construct() 
	#########################
	{
		parent::__construct();
    
		$this->currentclassname='GNDengineSOLR';

    $this->solr_collection = 'dnb_gnd';
		$this->setWordbinding('GND');

    $this->maxROWSinSOLR_eachEntity = 20; // 10 rows each entity in GND SOLR
    $this->namespaces= get_namespaces_from_DB();

		$this->GND_SUGGESTION_FIELDS = array(
                                    'id',
                                    'score',
                                    'skos_prefLabel_fr',
                                    'skos_prefLabel_en',
                                    'skos_prefLabel_de',
                                    'skos_prefLabel_es',
                                    'skos_prefLabel_it',
                                	);


	} //GNDengineSOLR constructor 
	
	

	public function refine_gnd_solr_available()
  {
    return true; // at this level.
  }
	
  
  
  protected function refine_method($term,$action,$lang)
  {
    // obsolete ... is here for the sake of class completeness
  }   
  
  
  
  
  
  
	/**
	 * Refines all relevant token in text using $this->refineFunctionName
	 * ENTRY POINT for computation A la SKOS of GND nodes around "$text"
   * To be set on init by the class involved
	 * returns each token the related term
   * for each of action=broader,narrower,related
	 *
	 * returns an OBJ containing ranked broader/narrower/related results and the labels of the node(s) found
	 * Uses refine_gnd_solr_method() to compute SKOS relations
	 * 
	 * $param $text The term or search text
	 * $param $q The original user search text (used??)
	 * $param $m The maximal number of results each SKOS relation
	 * $param $lang The output language
	 * $param $sortrank (still fixed to 'standard') defines how results should be ranked
	 */
	protected function refine_gnd_solr($text, $q, $m, $lang, $sortrank='standard',$mode='web')
	{	
	 	global $TERM_SEPARATOR;
		
		if ($this->srcdebug) print "<br>GNDengineSOLR->refine_gnd_solr($text, $q, $m, $lang) ...";
		$ok=true;
		$TERMS_RAW=array();
		$words=explode($TERM_SEPARATOR,$text);
		if ($ok)
		{
      // call SLOR GND engine for each term in $text using the default solr dismax query handler
      if (trim($text))
      {
        $GND_SKOSResult = $this->refine_gnd_solr_method( trim($text),$m,$lang,$mode );
        list($broader_terms,  $broader_descriptors,   $B_ROOTPATHS)= $GND_SKOSResult->broader;
        list($narrower_terms, $narrower_descriptors,  $N_ROOTPATHS)= $GND_SKOSResult->narrower;
        list($related_terms,  $related_descriptors,   $R_ROOTPATHS)= $GND_SKOSResult->related;
      
        if ($this->srcdebug) {
          print "<hr>refine_gnd_solr(): refine_gnd_solr_method() returns: "
                .count($broader_terms)." broader terms"
                .count($narrower_terms)." narrower terms"
                .count($related_terms)." related terms"
                  ;

          if (count($broader_terms))
          foreach($broader_terms as $term=>$RANK)
            print "<br>Broader: ".$term." ($RANK)";
          print "<br>";
          if (count($narrower_terms))
          foreach($narrower_terms as $term=>$RANK)
            print "<br>Narrower: ".$term." ($RANK)";
          print "<br>";
          if (count($related_terms))
          foreach($related_terms as $term=>$RANK)
            print "<br>Related: ".$term." ($RANK)";
          print "<br>";
        }
        //process received terms
        //load to vectors singlewise! 
        
        list($broader_CANDIDATES, $broader_TERMS_RAW)   = $this->process_results($broader_terms,  $broader_descriptors,$text,$q);
        list($narrower_CANDIDATES,$narrower_TERMS_RAW)  = $this->process_results($narrower_terms, $narrower_descriptors,$text,$q);
        list($related_CANDIDATES, $related_TERMS_RAW)   = $this->process_results($related_terms,  $related_descriptors,$text,$q);
      } // text non empty

	
			if ($this->srcdebug) 
      {
        print "<br> ".count($broader_CANDIDATES)." broader_CANDIDATES (m=$m):";
        print "<br> ".count($narrower_CANDIDATES)." narrower_CANDIDATES (m=$m):";
        print "<br> ".count($related_CANDIDATES)." related_CANDIDATES (m=$m):";
			}
			
      list($broader_refined_terms,  $broader_refined_terms_raw)   = $this->scrumble_cut_and_process_raw_results($broader_CANDIDATES,$broader_descriptors,$m,$sortrank);
      list($narrower_refined_terms, $narrower_refined_terms_raw)  = $this->scrumble_cut_and_process_raw_results($narrower_CANDIDATES,$narrower_descriptors,$m,$sortrank);
      list($related_refined_terms,  $related_refined_terms_raw)   = $this->scrumble_cut_and_process_raw_results($related_CANDIDATES,$related_descriptors,$m,$sortrank);

		} // ok
    
    
		return array( $GND_SKOSResult->suggested,
									new SRCEngineResult(trim($broader_refined_terms), trim($broader_refined_terms_raw),  $B_ROOTPATHS),
                  new SRCEngineResult(trim($narrower_refined_terms),trim($narrower_refined_terms_raw), $N_ROOTPATHS),
                  new SRCEngineResult(trim($related_refined_terms), trim($related_refined_terms_raw),  $R_ROOTPATHS) );
	} // refine_gnd_solr
	
  
  
  
  /**
	 * This is the real method computing SKOS neighbours to a term or a search text
	 * Returns a complex object SRCEngineSKOSResult (a triple of list pairs)
	 * 
	 * @param $term the GND term or a search text
	 * @param $m the limitation to the max numbers of results
	 * @param $lang the output language
	 */ 
  protected function refine_gnd_solr_method($term,$m,$lang,$mode)
	############################################################
  # Find Terme related to $action 
	{ 
    global $RODINSEGMENT;
		$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
		/* Terms in GND are left as they are */
		/* Try to make them like this to SQL-Match them */
		if ($this->getSrcDebug()) print "<br>GNDengineSOLR->$METHODNAME($term)...";
		
		############################################
		list($descriptor,$label) = $this->extractDescriptor($term);
		############################################
		
		if ($descriptor) # Request was made on a node (descriptor) exactely
		{
       $GND_SKOSResult  =  $this->get_loc_skos_nodes_SOLR(null,$descriptor,$m,$lang,$mode);
		} # node
		###########################################
		else # Request is on text
		{ 
      $term= $this->formatAsInThesaurus($term);
			// ----- Search for Labels in GND SOLR  ------

      $GND_SKOSResult  = $this->get_gnd_nodes_SOLR($term,null,$m,$lang,$mode);
      /* in $GND_SKOSResult ar all results for each node */
      
		}  //text	
    // 
    // 
		if ($this->getVerbose())
		{
      list($BROADER_LABELS,$BROADER_DESC)=$GND_SKOSResult->broader;
      list($NARROWER_LABELS,$NARROWER_DESC)=$GND_SKOSResult->narrower;
      list($RELATED_LABELS,$RELATED_DESC)=$GND_SKOSResult->related;
      
      print "<br>refine_skos_solr_method RESULT SKOS ($term):";
      var_dump($GND_SKOSResult); print "<br>";

			if (count($BROADER_DESC))
			{
				print "<br>".count($BROADER_DESC)." broader terms found!";
				for($i=0;$i<count($BROADER_DESC);$i++)
        {
          $desc=$BROADER_DESC[$i];
          $label=$BROADER_LABELS[$i];
						print "<br> broader --> <b>$label ($desc)</b>";
				}
        for($i=0;$i<count($NARROWER_DESC);$i++)
        {
          $desc=$NARROWER_DESC[$i];
          $label=$NARROWER_LABELS[$i];
						print "<br> narrower --> <b>$label ($desc)</b>";
				}
        for($i=0;$i<count($RELATED_DESC);$i++)
        {
          $desc=$RELATED_DESC[$i];
          $label=$RELATED_LABELS[$i];
						print "<br> related --> <b>$label ($desc)</b>";
				}
			}
		} // text
    
    
		return $GND_SKOSResult; // Information block for every skos relation
	
	} // refine_gnd_solr_method
  
  
  
  
  
  
  
  
  
  
  
  
  //process received terms
  //load to vectors singlewise! 
  private function process_results(&$terms,&$descriptors,$text,$q)
  ############################################################
  {
    if (count($terms))
    {
      foreach($terms as $term=>$RANK)
      {
        if (count($descriptors))
          $TERMS_RAW{$term}=$descriptors{$term}; 

        if ($this->srcdebug) print "<br>process_results() PROCESSING ($term)";
        if ($refined_terms) $refined_terms.="$TERM_SEPARATOR\n"; 
          $refined_terms.=trim($term);
        $CANDIDATES{$term}=$RANK;
        if ($this->srcdebug) print "<br> Adding <b>$term</b> to candidates";
      

      } //foreach
    } // terms ex.
    return array($CANDIDATES,$TERMS_RAW);
  }   


  
  
  
  /*
   * Sorts and cut labels ... according to $sortrank
   * Returns the descriptors base64 encoded
   * @param $CANDIDATE_LABELS
   * @param $CANDIDATE_DESCS
   * @param $m
   * @param $sortrank
   */
  private function scrumble_cut_and_process_raw_results(&$CANDIDATE_LABELS,&$CANDIDATE_DESCS,$m,$sortrank='standard')
 	#############################################################################
  {
    global $TERM_SEPARATOR;
    
    if(count($CANDIDATE_LABELS))
    {
      // Create label-term map
      foreach($CANDIDATE_LABELS as $term=>$rank)
      {
        $TERMS_RAW{$term}=$CANDIDATE_DESCS[$i++];
      }
      
      // START OF SORT RANK SECTION
      if ($sortrank='standard') 
      {
        arsort($CANDIDATE_LABELS); // sort using values - top value first
        array_splice($CANDIDATE_LABELS, $m); // keep the first $m 
     
     
        if($this->srcdebug) { 
           print "<br>m = $m";
           print "<br>CANDIDATE LABELS: (binding=".$this->getWordbinding().")";
           var_dump($CANDIDATE_LABELS);
           print "<br>";
           foreach($CANDIDATE_LABELS as $C=>$R)
           {
             print "<br>Candidade label: $C=>$R";
           } 
           print "<br>CANDIDATE DESCS: (binding=".$this->getWordbinding().")";
           var_dump($CANDIDATE_DESCS);
           print "<br>";
           foreach($CANDIDATE_DESCS as $C=>$R)
           {
             print "<br>Candidade desc: $C=>$R";
           } 
        }
     }
     // END OF SORT RANK SECTION
     
     
     $refined_terms=''; //reset
     $refined_terms_raw='';
     
     foreach($CANDIDATE_LABELS as $term=>$RANK)
     {
       if($this->srcdebug) {
         print "<br>Considering candidate $term ...";
       } 
        $nextterm=$term; // we do not need to cut off stop words ... here anymore
//       $nextterm=trim(cleanup_stopwords_str($term));
//       if ($nextterm) // term is non stopword
       {
         if ($this->getWordbinding() == 'GND')
         {
           //Construct a sequence of terms separated by $TERM_SEPARATOR
           if ($refined_terms) $refined_terms.="$TERM_SEPARATOR"; 
           $refined_terms.=$nextterm;
           // construct a sequence of base64coded terms separated by $TERM_SEPARATOR
           if ($refined_terms_raw) $refined_terms_raw.="$TERM_SEPARATOR\n"; 
           $refined_terms_raw.=base64_encode($TERMS_RAW{$term});	 // encode its descriptor						
         }
         //prepare_solr_mlt_context($nextterm);
       }
     }
   }
   
   if($this->srcdebug) {
         print "<br>process_raw_results() Delivering refined_terms_raw:<br>";
         var_dump($refined_terms_raw);
       } 
   
   return array($refined_terms,$refined_terms_raw);
 } // process_raw_results
  
  
   /**
	 * GND implementation of the function, since the data is held
	 * in our server, this function will "succeed" in case there is mode data
	 */
	protected function testDataSourceResponsiveness($user) {
    $isempty=solr_collection_empty($this->solr_collection);
    if ($isempty)
      $response="<error>SOLR collection '".$this->solr_collection."' seems to be empty or not to respond!</error>";
    else 
      $response="<user>$user</user>";
    return $response;
	}

 
 
 /*
  * SOLR special case: Use SOLR insdead of ARC
  * Returns compete URI (descriptor)
  */
 protected function validateTermInOntology($term, $lang = 'en') {
		if ($this->getSrcDebug()) {
			print "<br>loc_validate_term($term,$lang='en') USING SOLR ...";
		}
		list($suggested_term,$descriptor) = $this->checkterm_in_loc($term,$lang);
		
		if ($suggested_term)
      return array($term, $descriptor);
    {
			return false;
		}
	}
	

  
	/**
	 * Checks if $term is contained as a label in the SKOS GND Thesaurus,
	 * returns a pair (suggested term, descriptor) if yes
   * @author: Fabio Ricci
	 */
	protected function checkterm_in_loc($term,$lang) 
  {
  	if ($this->getSrcDebug()) {
			print "<br><br>checkterm_in_loc($exactSearch): $QUERY <br>";
		}
		
		$found = false;
    
    if (($SOLRCLIENT = init_SOLRCLIENT($this->solr_collection,'solr_index_skos_namespaces system error init SOLRCLIENT')))
    {
      $SOLRCLIENT->getPlugin('postbigrequest');

      // get a select query instance
      $query = $SOLRCLIENT->createSelect();
      
      $query->setQuery(($term));

      // set start and rows param (comparable to SQL limit) using fluent interface
      $query->setStart(0)->setRows(1); // wee need one row ...

      // set fields to fetch (this overrides the default setting 'all fields')
      $query->setFields(array('id','skos_prefLabel_'.$lang,'skos_altLabel_'.$lang));

      // sort the results by price ascending
      //$query->addSort('price', Solarium_Query_Select::SORT_ASC);

      // this executes the query and returns the result
      $resultset = $SOLRCLIENT->select($query);
      $noofresults=$resultset->getNumFound();
      //var_dump($resultset);
      // display the total number of documents found by solr
      $returndocument=array();
      // show documents using the resultset iterator
      if ($noofresults)
      {  
        foreach($resultset as $document)
        foreach($document AS $fieldname => $value)
        {
          switch($fieldname)
          {
            case 'id': $descriptor=$value; $found=true; break;
            case 'skos_prefLabel_en': $label=is_array($value)?$value[0]:$value; break;
            case 'skos_prefLabel_de': $label=is_array($value)?$value[0]:$value; break;
            case 'skos_altLabel_de':$label=is_array($value)?$value[0]:$value; break;
            case 'skos_altLabel_en':$label=is_array($value)?$value[0]:$value; break;
          }
        }
      }
    } // SOLRCLIENT
    
    
		if ($found)	{
			$suggested_term = $term;
      $suggested_desc = $descriptor;
		} else {
			$suggested_term = '';
      $suggested_desc = '';
		}
		
		return array($suggested_term,$suggested_desc);
	}  
  
  
  
  
  

/**
 * Search / Collection method
 * Returns an GND SKOS NODES corresponding to a SOLR search for '$term' or a $descriptor
 * @param $term The search text (if given)
 * @param $descriptor The precise descriptor (if given) - one of ($term,$descriptor) must be provided
 * @param $m 
 * @param $lang  
 */
private function get_gnd_nodes_SOLR($term,$descriptor,$m,$lang,$mode)
{
  if (!$term && !$descriptor)
    print "System error: get_gnd_nodes_SOLR called with neither a term nor a descriptor!";
  else
  {
    if ($this->getVerbose())
		{
      print "<br>GNDengineSOLR->get_gnd_nodes_SOLR($term,$descriptor,$m,$lang) ...";
    }
    
    if (($SOLRCLIENT = init_SOLRCLIENT($this->solr_collection,'solr_index_skos_namespaces system error init SOLRCLIENT')))
    {
      $SOLRCLIENT->getPlugin('postbigrequest');

			$suggestions = array();
			if ($mode=='autocomplete')
			{
				$suggestions = $this->collect_labels_for_autocomplete($SOLRCLIENT,SRCengine::$maxsuggestions,'skos_prefLabel_de',$term,$this->GND_SUGGESTION_FIELDS);
				//No suggestions for this language? try again in another language:
				if (!count($suggestions))
				$suggestions = $this->collect_labels_for_autocomplete($SOLRCLIENT,SRCengine::$maxsuggestions,'skos_prefLabel_en',$term,$this->GND_SUGGESTION_FIELDS);
				//No suggestions for this language? try again in another language:
				if (!count($suggestions))
				$suggestions = $this->collect_labels_for_autocomplete($SOLRCLIENT,SRCengine::$maxsuggestions,'skos_prefLabel_fr',$term,$this->GND_SUGGESTION_FIELDS);
				//No suggestions for this language? try again in another language:
				if (!count($suggestions))
				$suggestions = $this->collect_labels_for_autocomplete($SOLRCLIENT,SRCengine::$maxsuggestions,'skos_prefLabel_es',$term,$this->GND_SUGGESTION_FIELDS);
				//No suggestions for this language? try again in another language:
				if (!count($suggestions))
				$suggestions = $this->collect_labels_for_autocomplete($SOLRCLIENT,SRCengine::$maxsuggestions,'skos_prefLabel_it',$term,$this->GND_SUGGESTION_FIELDS);
			} // autocomplete




      // get a select query instance
      $query = $SOLRCLIENT->createSelect();
      
      if ($descriptor) // user wishes an exact retrieve using a (previously) calculated descriptor
         $query->createFilterQuery('id')->setQuery("id:$descriptor");
      else /* $term search */
         $query->setQuery(($term));

      // set start and rows param (comparable to SQL limit) using fluent interface
      //$query->setStart(0)->setRows(3 * $m * ($this->maxROWSinSOLR_eachEntity)); // we need enaugh data ...

      // set fields to fetch (this overrides the default setting 'all fields')
      //$query->setFields($this->GND_SKOS_FIELDS);

      // sort the results by price ascending
      //$query->addSort('price', Solarium_Query_Select::SORT_ASC);

      // this executes the query and returns the result
      $resultset = $SOLRCLIENT->select($query);
      $noofresults=$resultset->getNumFound();
      //var_dump($resultset);
      // display the total number of documents found by solr
      $returndocument=array();
      // show documents using the resultset iterator
      /*
       * This method implements the idea, that even when several nodes
       * in the thesaurus were found, their SKOS relations are merged!
       */
      $BROADER_DESC = $NARROWER_DESC = $RELATED_DESC = array();
      $d=0;
      
      $noofresults=$resultset->getNumFound();
      if (solr_collection_empty($this->solr_collection))
      { print "<br><br>SEVERE ERROR - EMPTY collection ? SOLR running and reachable? '<b>".$this->solr_collection."</b>' please inform your ontology administrator !!!";
        exit;
      }
			
			if ($this->getSrcDebug())
			{
				foreach ($resultset as $document) $docs++;
				
				print "<br>Got $docs documents from SOLR ($term)";
			}
			
      foreach ($resultset as $document) 
      {
      	  $d++;
          if ($this->getVerbose())
          {
            print "<hr>Reading $d. document for query ... '$term' (or '$descriptor')";
          }
					
	          $PREFLABELS_DE=$PREFLABELS_EN=$PREFLABELS_EN=$PREFLABELS_FR=$PREFLABELS_IT=$PREFLABELS_ES=array();
	        // the documents are also iterable, to get all fields
	          foreach($document AS $fieldname => $value)
	          {
	          	 	if ($this->getVerbose())
									print "<br>$fieldname => $value";
	              // this converts multivalue fields to a comma-separated string
	              if(!is_array($value)) $value = array($value);
	              //Filter out SKOS Elements
	
	              switch($fieldname)
	              {
	                //Sum all skos nodes over all found node elements
	                case 'skos_broader': $BROADER_DESC = array_merge($BROADER_DESC,is_array($value)?$value:array($value));break;
	                case 'skos_narrower':$NARROWER_DESC = array_merge($NARROWER_DESC,is_array($value)?$value:array($value));break;
	                case 'skos_related': $RELATED_DESC = array_merge($RELATED_DESC,is_array($value)?$value:array($value));break;
	
	                case 'id': $ID =$value; break;
	                //case 'skos_inScheme': /*noop*/ break;
	
	                /* Labels of the current ID */
	                /* in GND several languages come up */
	                /* HERE - PLEASE - GENERALIZE PROCESSING of LANGUAGES */
	                case 'skos_prefLabel_fr': if (is_array($value)) 
	                                               $PREFLABELS_FR = $value;
	                                          else $PREFLABELS_FR = array(utf8_decode($value));
	                      break;
	                case 'skos_prefLabel_it': if (is_array($value)) 
	                                               $PREFLABELS_IT = $value;
	                                          else $PREFLABELS_IT = array(utf8_decode($value));
	                      break;
	                case 'skos_prefLabel_es': if (is_array($value)) 
	                                               $PREFLABELS_ES = $value;
	                                          else $PREFLABELS_ES = array(utf8_decode($value));
	                      break;
	                case 'skos_prefLabel_de': if (is_array($value)) 
	                                               $PREFLABELS_DE = $value;
	                                          else $PREFLABELS_DE = array(utf8_decode($value));
	                      break;
	                case 'skos_prefLabel_en': if (is_array($value)) 
	                                               $PREFLABELS_EN = $value;
	                                          else $PREFLABELS_EN = array($value);
	                      break;
	
	                case 'rdf_type': /*useless here*/break;
	                case 'body': /*used for a search, useless here*/break;
	                case 'skos_notation': /*MIGHT BE USEFUL*/break;
	                case 'timestamp': /*SOLR*/break;
	                case 'score': /*SOLR*/break;
	
	              } // switch
	
	          } // foreach field
	          //The following are n documents found on the text search...
	          //They shoudl be ranked...
	          //Currently not used - but hties broader/narrower/related nodes
          
          $returndocument[]=array($ID,$PREFLABELS_FR,$PREFLABELS_EN,$PREFLABELS_DE,$PREFLABELS_IT,$PREFLABELS_ES);
      }
    }
    else
      print "SYSTEM ERROR: NO ACCESS TO SOLR COLLECTION: ".$this->solr_collection;
		
		//make found descriptors unique:
    $BROADER_DESC = array_unique($BROADER_DESC);
    $NARROWER_DESC = array_unique($NARROWER_DESC);
		$RELATED_DESC = array_unique($RELATED_DESC);
		//DO NOT CUT NOW, since for some desc, some label might not be found
		//array_splice($RELATED_DESC,$m);
		//array_splice($BROADER_DESC,$m);
    //array_splice($NARROWER_DESC,$m);
		
		
    // Processing... resolving descriptors... all at once

    list ($BROADER_LABELS,
          $NARROWER_LABELS,
          $RELATED_LABELS) 
              = $this->resolve_skos_loc_descriptors(	$BROADER_DESC,
	                                                    $NARROWER_DESC,
	                                                    $RELATED_DESC,
	                                                    $m,
	                                                    $lang );
    // do something with return_document...:

  }

  $ID0= $returndocument   [0][0][0];
  $LABEL0= $returndocument[0][1][0];
  $ID1= $returndocument   [1][0][0];
  $LABEL1= $returndocument[1][1][0];
  
  
  if ($this->getSrcDebug())
    {
      $NoOfFoundDocs=count($returndocument);
      
      //print "<br><br>$NoOfFoundDocs node labels documents: "; var_dump($returndocument);print "<br>";
    
      print "<hr><br>in get_loc_skos_nodes_SOLR():";
      print "<br>resolved BROADER_LABELS:<br>"; var_dump($BROADER_LABELS);
      print "<br>resolved NARROWER_LABELS:<br>"; var_dump($NARROWER_LABELS);
      print "<br>resolved RELATED_LABELS:<br>"; var_dump($RELATED_LABELS);
      
      print "<br>ROOT ID: $ID0=$LABEL0, $ID1=$LABEL1";

    } 
  
    if ($mode=='web')
		{
	    //Compute each ID the path
	    $LOCALrootPATH = $this->walk_loc_root_path($ID0,$lang,$recursion=0);
	    if ($this->getSrcDebug())
	    {
	      print "<br>Result of LOCALrootPATH: (($LOCALrootPATH))";
	    }
			
			$LRP_B= $this->rootpaths($LOCALrootPATH,$BROADER_LABELS);
			$LRP_N= $this->rootpaths($LOCALrootPATH,$NARROWER_LABELS);
			$LRP_R= $this->rootpaths($LOCALrootPATH,$RELATED_LABELS);
	    Logger::logAction(25, array('from'=>'GNDengineSOLR->WebRefine','LOCALrootPATH'=>$LOCALrootPATH));
		}
    //Compute all other paths by using $LOCALrootPATH adding the current label.
    
   $B= array($BROADER_LABELS, $this->denormalize_descriptor($BROADER_DESC,'bnf') , $LRP_B); 
   $N= array($NARROWER_LABELS,$this->denormalize_descriptor($NARROWER_DESC,'bnf'), $LRP_N); 
   $R= array($RELATED_LABELS, $this->denormalize_descriptor($RELATED_DESC,'bnf') , $LRP_R); 
  
    if ($this->getSrcDebug())
    {
      print "<hr><br>Returning DENORMALIZED BROADER OBJ: ";var_dump($B);
			print "<br>Returning DENORMALIZED NARROWER OBJ: ";var_dump($N);
			print "<br>Returning DENORMALIZED RELATED OBJ: ";var_dump($R);
    }
  
  
  
	return new SRCEngineSKOSResult ( $suggestions, $B, $N, $R );
} // get_loc_skos_nodes_SOLR



 /*
  * Returns a +list of comma-separated-labels rootpaths 
  * B1,B2,B3,B4|B1,B2,B3|B1,B2,B3
  */
  private function rootpaths($LOCALrootPATH,&$LABELS)
  {
     $ROOTPATHSEP=',';
     if (!is_array($LABELS))
       return null;
     else
     {
       foreach($LABELS as $LABEL=>$RANK)
       {
         $ROOTPATHS.=$ROOTPATHS?'|':''; // link the next path
         $ROOTPATHS.=base64_encode($LABEL).$ROOTPATHSEP.$LOCALrootPATH; // this is a path
       }
       if ($this->getSrcDebug())
       {
         $NOOFLABELS = count($LABELS);
         print "<br><br>rootpaths($LOCALrootPATH,...) called with $NOOFLABELS LABELS; "; 
         print "<br><br>rootpaths($LOCALrootPATH,...) returning; "; 
         print "<br>(($ROOTPATHS))";
       }
       return $ROOTPATHS;
     }
  }


 /**
 * Returns a triples of array of ranked labels
 * ATTENTION: This method CUT &$BROADER_DESC,&$NARROWER_DESC,&$RELATED_DESC to max $m elements
 * after having found labels
 */
  private function resolve_skos_loc_descriptors(&$BROADER_DESC,&$NARROWER_DESC,&$RELATED_DESC,$m,$lang='fr')
  {
     //Merge each descriptor in one array:
    $descriptors=  array_merge($BROADER_DESC,$NARROWER_DESC,$RELATED_DESC);
		$max_m = $m * 4; // sum of broaders/narrowers/related + same terms in thesaurus
    $descriptors_count= min(count($descriptors),$max_m); // limit the retrieval in SOLR to $m labels max
     
    if ($this->getVerbose())
    {
      print "<br>".count($descriptors)." descriptors to resolve:";
      
      if ($this->getSrcDebug()) 
      {
      	print "<br>".count($BROADER_DESC).' BROADER DESCS:';
        foreach($BROADER_DESC as $d) print "<br>Broader Desc: $d";
      	print "<br>".count($NARROWER_DESC).' NARROWER DESCS:';
        foreach($NARROWER_DESC as $d) print "<br>Narrower Desc: $d";
      	print "<br>".count($RELATED_DESC).' RELATED DESCS:';
        foreach($RELATED_DESC as $d) print "<br>Related Desc: $d";
      }
    }
    
    
    //Pick up only the needed ones:
    $NEEDED_FIELD='skos_prefLabel_'.$lang;
    
    $FIELDS=array('id',$NEEDED_FIELD);
    
    if (($SOLRCLIENT = init_SOLRCLIENT($this->solr_collection,'solr_index_skos_namespaces system error init SOLRCLIENT')))
    {
      $SOLRCLIENT->getPlugin('postbigrequest');

			$suggestions = array();
			if ($mode=='autocomplete')
			{
				$suggestions = $this->collect_labels_for_autocomplete($SOLRCLIENT,SRCengine::$maxsuggestions,'skos_prefLabel_de',$term,$this->GND_SUGGESTION_FIELDS);
				//No suggestions for this language? try again in another language:
				if (!count($suggestions))
				$suggestions = $this->collect_labels_for_autocomplete($SOLRCLIENT,SRCengine::$maxsuggestions,'skos_prefLabel_en',$term,$this->GND_SUGGESTION_FIELDS);
				//No suggestions for this language? try again in another language:
				if (!count($suggestions))
				$suggestions = $this->collect_labels_for_autocomplete($SOLRCLIENT,SRCengine::$maxsuggestions,'skos_prefLabel_fr',$term,$this->GND_SUGGESTION_FIELDS);
				//No suggestions for this language? try again in another language:
				if (!count($suggestions))
				$suggestions = $this->collect_labels_for_autocomplete($SOLRCLIENT,SRCengine::$maxsuggestions,'skos_prefLabel_es',$term,$this->GND_SUGGESTION_FIELDS);
				//No suggestions for this language? try again in another language:
				if (!count($suggestions))
				$suggestions = $this->collect_labels_for_autocomplete($SOLRCLIENT,SRCengine::$maxsuggestions,'skos_prefLabel_it',$term,$this->GND_SUGGESTION_FIELDS);
			} // autocomplete

      // get a select query instance
      $query = $SOLRCLIENT->createSelect();
      
      /* Create a disjunction with each of &$BROADER_DESC,&$NARROWER_DESC,&$RELATED_DESC :*/
      foreach($descriptors as $descriptor)
      {
        //Add only different descriptors:
        if (!$already_using_descriptor{$descriptor})
        {
          $already_using_descriptor{$descriptor} = true;
          $disjunction.=$disjunction?' OR ':'';
          $disjunction.="id:$descriptor";
        }
      }
			$disjunction = str_replace("ark:","ark\\:",$disjunction);

      //Query all entities with id in $descriptors
      $query->createFilterQuery('id')->setQuery($disjunction);

      if ($this->getVerbose())
      {
        print "<br>resolve_skos_loc_descriptors query=($disjunction) ...";
      }
      
      // set start and rows param (comparable to SQL limit) using fluent interface
      $query->setStart(0)->setRows(2 * $descriptors_count); // retrieve max $m rows (altLabels and prefLabels)

      // set fields to fetch (this overrides the default setting 'all fields')
      $query->setFields($FIELDS);

      // sort the results by id ascending
      // $query->addSort('id', Solarium_Query_Select::SORT_ASC);

      // this executes the query and returns the result
      $resultset = $SOLRCLIENT->select($query);
      $noofresults=$resultset->getNumFound();
      $descriptor_label=array();
      
      if ($this->getVerbose())
      {
        print "<br>fieldquery: (($disjunction))";
        print "<br>FIELDS for language=$lang: <br>";
        var_dump($FIELDS);
        print "<br>$noofresults results found for query: <br>";
        var_dump($resultset);
        print "<br>";
      }
      
      
      foreach ($resultset as $document) 
      {
        foreach($document AS $fieldname => $value)
        {
          switch($fieldname)
          {
            case $NEEDED_FIELD:
              $label = trim(is_array($value)?$value[0]:$value); 
              break;
            case 'id':
              $id=$value;
          }  
        } // each fieldname
        if ($label)
				{
		      $descriptor_label{$id}=cleanup_comma_in_descr_label($label); // right label to right id
		      $processed_docs++;
					if ($processed_docs >= $max_m) 
					{
						//print "<br>BREAKING LABELING at $processed_docs >= $m";
						break;
					}
				}
      } // foreach $document
    } // $SOLRCLIENT
    
    /* ATTENTION: IS THE SEQUENCE OF RESULTS THE SAME OF THE DISJUNCTION????? */
    /* TO PROOF: $labels and $descriptors are ALWAYS corresponding */
    
    if ($this->getSrcDebug())
    {
      $labelcount=count($descriptor_label);
      $desccount=count($already_using_descriptor);
      
      if ($labelcount<>$desccount)
        print "<br>UNEQUAL NUMBER OF LABELS FOUND to given descriptors! $labelcount labels for $desccount descriptors";

      print "<br>SHOWING $labelcount lables ...";
      
      foreach($descriptor_label as $desc=>$label)
      {
         print "<br> found ".(++$l)." $label for $desc";
         
         if (in_array($desc, $BROADER_DESC))
                 print " (broader)";
         else 
         if (in_array($desc, $NARROWER_DESC))
                 print " (narrower)";
         else 
         if (in_array($desc, $RELATED_DESC))
                 print " (related)";
      }
    } // debug output
    
    /* reconstruct vectors to give them back as they where: */
    /* at this stage we should rank */
    $RANK=100;
    $b=0;
    foreach($BROADER_DESC as $desc)
		{
			if (($L = $descriptor_label{$desc})) // not null
			{
				$b++;
				//print "<br>$b broader take: $desc -> $L";
	      $BROADER_LABELS{$L}=$RANK;
				$NEW_BROADER_DESC[]=$desc;
				if ($b > $m) break;
			}
		}
		
		$n=0;
    foreach($NARROWER_DESC as $desc)
    {
    	if (($L = $descriptor_label{$desc})) // not null
			{
				$n++;
				//print "<br>$n narrower take: $desc -> $L";
      	$NARROWER_LABELS{$L}=$RANK;
      	$NEW_NARROWER_DESC[]=$desc;
				if ($n > $m) break;
			}
		}
		
		$r=0;
    foreach($RELATED_DESC as $desc)
		{
			if (($L = $descriptor_label{$desc})) // not null
			{
				$r++;
				//print "<br>"$r related take: $desc -> $L";
      	$RELATED_LABELS{$L}=$RANK;
      	$NEW_RELATED_DESC[]=$desc;
				if ($r > $m) break;
      }
		}
		$BROADER_DESC = $NEW_BROADER_DESC;
		$NARROWER_DESC = $NEW_NARROWER_DESC;
		$RELATED_DESC = $NEW_RELATED_DESC;
    
    if ($this->getSrcDebug())
    {
      print "<br>in resolve_skos_loc_descriptors():";
      print "<br>prepared BROADER_LABELS:<br>"; var_dump($BROADER_LABELS);
      print "<br>prepared NARROWER_LABELS:<br>"; var_dump($NARROWER_LABELS);
      print "<br>prepared RELATED_LABELS:<br>"; var_dump($RELATED_LABELS);
    } 
      
    return array($BROADER_LABELS,$NARROWER_LABELS,$RELATED_LABELS);
  } // resolve_skos_loc_descriptors
  
  
  
  
/*
 * SKOS context
 * Returns a text containing base64 chunks separated by commas
 * Each chunk is the preferred label of the father ... of the term/descriptor
 * Recursive function used to calculate a context to MLT against widget results
 * 
 * See also rootpaths() to pack information
 */
  private function walk_loc_root_path($descriptor,$lang='fr',$recursion=0)
  {
		if (trim($descriptor)=='' 
		|| (!strstr('x'.$descriptor,'gnd')))		{
			return '';
		}
		
  	$descriptor=str_replace("ark:","ark\\:",$descriptor); // for SOLR : is a meta char and has to be escaped
    $ROOTPATHSEP=',';
    if ($this->getSrcDebug())
    {
      print "<br>walk_loc_root_path called with lang=$lang and descriptor: ";var_dump($descriptor);print "<br>";
    }

    static $MAXRECURSION=100; // allow maximum 100 recursions
    $recursion++;
    
    if ($recursion > $MAXRECURSION)
    {
      print "<br>walk_loc_root_path($descriptor) superated MAXRECURSION=$MAXRECURSION";
      print "<br>PLEASE ANALYSE the case and enhance the MAXRECURSION value! ";
      print "<br>Recursion PRUNED";
      return '';
    }  
    
    //Pick up only the needed ones:
	  $NEEDED_FIELD='skos_prefLabel_'.$lang;
    
    $FIELDS=array($NEEDED_FIELD,'skos_broader');
    
    if (($SOLRCLIENT = init_SOLRCLIENT($this->solr_collection,'solr_index_skos_namespaces system error init SOLRCLIENT')))
    {
      // get a select query instance
      $query = $SOLRCLIENT->createSelect();
      $query->createFilterQuery('id')->setQuery("id:$descriptor");
      $query->setStart(0)->setRows(2); // retrieve max 1 rowprefLabel
      $query->setFields($FIELDS);
      $resultset = $SOLRCLIENT->select($query);
      $noofresults=$resultset->getNumFound();
      $descriptor_label=array();
      
      if ($this->getVerbose())
      {
        print "<br>fieldquery: ((id:$descriptor))";
        print "<br>FIELDS for language=$lang: <br>";
        var_dump($FIELDS);
        print "<br>$noofresults results found for query: <br>";
        var_dump($resultset);
        print "<br>";
      }
      
      //There should be one document but wee need the iterator
      foreach ($resultset as $document)
      {
        foreach($document AS $fieldname => $value)
        {

          switch($fieldname)
          {
            case $NEEDED_FIELD:
              $label = is_array($value)?$value[0]:$value; // take always the first one
              break;
            case 'skos_broader':
              $broader = is_array($value)?$value[0]:$value; // take all
          }  
        } // each fieldname
        $info{$descriptor}=array($label,$broader); // right label to right id

      } // foreach $document
    } // $SOLRCLIENT
    
    // take label and broader descr:
    $preflabel=$info[$descriptor][0];
    $broader=$info[$descriptor][1];
    
    if ($this->getSrcDebug())
    {
      print "<br>in walk_loc_root_path($descriptor): $preflabel";
      print "<br>prepared BROADER_DESCR:<br>"; var_dump( $broader ) ;
    } 
    
    $path_to_root = base64_encode($preflabel);
    
    if ($broader && !is_array($broader)) $broader = array($broader);
    
    if (count($broader))
    {  
      //Apply recursively to the root
      foreach($broader as $singlebroader_descriptor)
      {
        if (!trim($singlebroader_descriptor))
          print "<br>PROCESS SKOSCONTEXT EMPTY DESCRIPTOR ($singlebroader_descriptor)"
          ;
        else
        { 
          //print "<br>PROCESS SKOSCONTEXT TO ($singlebroader_descriptor)";
          
          $pathremote = $this->walk_loc_root_path($singlebroader_descriptor,$lang,$recursion);
          // Add:
          if ($pathremote)
          $path_to_root.=$ROOTPATHSEP.$pathremote;
        }
      }
    }
    
    //return
    return $path_to_root;
  } // walk_loc_root_path
  
  
  
  
  
  
  private function denormalize_descriptor($GND_SOLR_DESCRIPTORS,$namespace)
  {
    if (!is_array($GND_SOLR_DESCRIPTORS)) 
    {
      return str_replace($namespace.'_',$this->namespaces[$namespace],$GND_SOLR_DESCRIPTORS);
    }
    else
    { 
      foreach($GND_SOLR_DESCRIPTORS as $GND_SOLR_DESCRIPTOR)
      {
        $NORMALIZED_DESCRIPTORS[] = str_replace($namespace.'_',$this->namespaces[$namespace],$GND_SOLR_DESCRIPTOR);
      }
      return $NORMALIZED_DESCRIPTORS;
    }
  }   
  
	
} // class GNDengineSOLR



?>