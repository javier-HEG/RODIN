<?php

	# STW Engine 3 (improvement of STW engine 2 using SOLR)
	#
	# Dex 2012
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


include_once("$DOCROOT$UPATH1/SOLRinterface/solr_interface.php");

	
class STWengine3 extends STWengine
{
	protected $maxROWSinSOLR_eachEntity;
  protected $STW_SKOS_FIELDS;
  protected $zbw_stw_namespaces;
  protected $solr_collection;
  
	function __construct() 
	#########################
	{
		parent::__construct();
    
		$this->currentclassname='STWengine3';

    $this->solr_collection = 'zbw_stw';
		$this->setWordbinding('STW');

    $this->maxROWSinSOLR_eachEntity = 10; // 10 rows each entity in STW SOLR
    /*
     * The following are standard fields we want to see when 
     * recalling results in solr
     */
    $this->STW_SKOS_FIELDS=array(
                                    'id',
                                    'score',

                                    'skos_prefLabel_de',
                                    'skos_prefLabel_en',
                                    'skos_altLabel_de',
                                    'skos_altLabel_en',

                                    'skos_broader',
                                    'skos_narrower',
                                    'skos_related'
                                );
    
    
    $this->zbw_stw_namespaces= get_namespaces_from_DB();

		//print "<br> STWengine2<hr>"; var_dump($this->get_zbwdbpedia_store());print "<hr>";

	} //STWengine1 
	
	

	protected function refine_skos_solr_available()
  {
    return true; // overloading base class method
  }
	
  
  
  protected function refine_method($term,$action,$lang)
  {
    // obsolete ... is here for the sake of class completeness
  }   
  
  
  
  
  
  
  /*
   * ENTRY POINT for computation of SKOS nodes around "$text"
   */
	protected function refine_skos_solr($text, $q, $m, $lang, $sortrank='standard')
	##################################
	# 
	# Refines all relevant token in text using $this->refineFunctionName
	# To be set on init by the class involved
	# returns each token the related term
  # for each of action=broader,narrower,related
	#
	{	/*
		returns a list of ranked terms AND the same list as a base64encoded comma separated terms
		*/
		global $TERM_SEPARATOR;
		global $VERBOSE;
		global $SRCDEBUG;
		if ($this->srcdebug) print "<br>STWengine3->refine_skos_solr($text, $q, $m, $lang) ...";
		$ok=true;
		$TERMS_RAW=array();
		$words=explode($TERM_SEPARATOR,$text);
		if ($ok)
		{
      // call SLOR STW engine for each term in $text using the default solr dismax query handler
      if (trim($text))
      {
        $STW_SKOSResult = $this->refine_skos_solr_method( trim($text),$m,$lang );
        list($broader_terms,  $broader_descriptors,   $B_ROOTPATHS)= $STW_SKOSResult->broader;
        list($narrower_terms, $narrower_descriptors,  $N_ROOTPATHS)= $STW_SKOSResult->narrower;
        list($related_terms,  $related_descriptors,   $R_ROOTPATHS)= $STW_SKOSResult->related;
      
        if ($this->srcdebug) {
          print "<hr>refine_skos_solr(): refine_skos_solr_method() returns: "
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
    
    
		return array( new SRCEngineResult(trim($broader_refined_terms), trim($broader_refined_terms_raw),  $B_ROOTPATHS),
                  new SRCEngineResult(trim($narrower_refined_terms),trim($narrower_refined_terms_raw), $N_ROOTPATHS),
                  new SRCEngineResult(trim($related_refined_terms), trim($related_refined_terms_raw),  $R_ROOTPATHS) );
	} // refine_skos_solr
	
  
  
  
  
  protected function refine_skos_solr_method($term,$m,$lang)
	############################################################
  # Find Terme related to $action 
	{ 
    global $RODINSEGMENT;
		$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
		/* Terms in STW are First letter capital */
		/* Try to make them like this to SQL-Match them */
		if ($this->getSrcDebug()) print "<br>STWengine3->$METHODNAME($term)...";
		
		############################################
		list($descriptor,$label) = $this->extractDescriptor($term);
		############################################
		
		if ($descriptor) # Request was made on a node (descriptor) exactely
		{
     $STW_SKOSResult  =  $this->get_stw_skos_nodes_SOLR(null,$descriptor,$m,$lang);
		} # node
		###########################################
		else # Request is on text
		{ 
      $term= $this->formatAsInThesaurus($term);
			// ----- Search for Labels in STW SOLR  ------

      $STW_SKOSResult  = $this->get_stw_skos_nodes_SOLR($term,null,$m,$lang);
      /* in $STW_SKOSResult ar all results for each node */
      
		}  //text	
    // 
    // 
		if ($this->getVerbose())
		{
      list($BROADER_LABELS,$BROADER_DESC)=$STW_SKOSResult->broader;
      list($NARROWER_LABELS,$NARROWER_DESC)=$STW_SKOSResult->narrower;
      list($RELATED_LABELS,$RELATED_DESC)=$STW_SKOSResult->related;
      
      print "<br>refine_skos_solr_method RESULT SKOS ($term):";
      var_dump($STW_SKOSResult); print "<br>";

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
    
    
		return $STW_SKOSResult; // Information block for every skos relation
	
	} // 
  
  
  
  
  
  
  
  
  
  
  
  
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
        $patternterm=str_replace('/','\/',$term);
        if (!preg_match("/$patternterm/",$text) 
         && !preg_match("/$patternterm/",$q))
        {
          if ($refined_terms) $refined_terms.="$TERM_SEPARATOR\n"; 
            $refined_terms.=trim($term);
          $CANDIDATES{$term}=$RANK;
          if ($this->srcdebug) print "<br> Adding <b>$term</b> to candidates";
        }
        else if ($this->srcdebug) print "<br> DISCARDING <b>$term</b> from candidates";

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
         if ($this->getWordbinding() == 'STW')
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
	 * STW implementation of the function, since the data is held
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
			print "<br>stw_validate_term($term,$lang='en') USING SOLR ...";
		}
		list($suggested_term,$descriptor) = $this->checkterm_in_stw($term,$lang);
		
		if ($suggested_term)
      return array($term, $descriptor);
    {
			return false;
		}
	}
	


  
	/**
	 * Checks if $term is contained as a label in the SKOS STW Thesaurus,
	 * returns a pair (suggested term, descriptor) if yes
   * @author: Fabio Ricci
	 */
	protected function checkterm_in_stw($term,$lang) 
  {
  	if ($this->getSrcDebug()) {
			print "<br><br>checkterm_in_stw($exactSearch): $QUERY <br>";
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
      
      if ($lang=='en')
        $query->setFields(array('id','skos_prefLabel_en','skos_altLabel_en'));
      else
      if ($lang=='de')
        $query->setFields(array('id','skos_prefLabel_de','skos_altLabel_de'));

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
  
  
  
  
  

/*
 * Returns an STW SKOS NODE corresponding to a SOLR search for '$term'
 */
private function get_stw_skos_nodes_SOLR($term,$descriptor,$m,$lang)
{  
  if (!$term && !$descriptor)
    print "System error: get_stw_skos_nodes_SOLR called with neither a term nor a descriptor!";
  else
  {
    if ($this->getVerbose())
		{
      print "<br>STWengine3->get_stw_skos_nodes_SOLR($term,$descriptor,$m,$lang) ...";
    }
    
    if (($SOLRCLIENT = init_SOLRCLIENT($this->solr_collection,'solr_index_skos_namespaces system error init SOLRCLIENT')))
    {
      $SOLRCLIENT->getPlugin('postbigrequest');

      // get a select query instance
      $query = $SOLRCLIENT->createSelect();
      
      if ($descriptor) // user wishes an exact retrieve using a (previously) calculated descriptor
         $query->createFilterQuery('id')->setQuery("id:$descriptor");
      else /* $term search */
         $query->setQuery(($term));

      // set start and rows param (comparable to SQL limit) using fluent interface
      $query->setStart(0)->setRows(3 * $m * ($this->maxROWSinSOLR_eachEntity)); // wee need enaugh data ...

      // set fields to fetch (this overrides the default setting 'all fields')
      $query->setFields($this->STW_SKOS_FIELDS);

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
      { print "<br><br>SEVERE ERROR - EMPTY collection? '<b>".$this->solr_collection."</b>' please inform your ontology administrator !!!";
        exit;
      }
      foreach ($resultset as $document) 
      {   $d++;
          if ($this->getVerbose())
          {
            print "<br>Reading $d. document for query ... '$term' (or '$descriptor')";
          }
          $PREFLABELS_DE=$PREFLABELS_EN=$ALTLABELS_DE=$ALTLABELS_EN=array();
        // the documents are also iterable, to get all fields
          foreach($document AS $fieldname => $value)
          {
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
                case 'skos_inScheme': /*noop*/ break;
                case 'gbv_gvkppn': /* NOT USED NOW BUT MIGHT BE USEFUL */

                /* Labels of the current ID */
                case 'skos_prefLabel_de': if (is_array($value)) 
                                               $PREFLABELS_DE = $value;
                                          else $PREFLABELS_DE = array(utf8_decode($value));
                      break;
                case 'skos_prefLabel_en': if (is_array($value)) 
                                               $PREFLABELS_EN = $value;
                                          else $PREFLABELS_EN = array($value);
                      break;
                case 'skos_altLabel_de':  if (is_array($value)) 
                                               $ALTLABELS_DE = $value;
                                          else $ALTLABELS_DE = array($value);
                      break;
                case 'skos_altLabel_en':  if (is_array($value)) 
                                               $ALTLABELS_EN = $value;
                                          else $ALTLABELS_EN = array($value);
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
          $returndocument[]=array($ID,$PREFLABELS_DE,$PREFLABELS_EN,$ALTLABELS_DE,$ALTLABELS_EN);
      }

    }
    else
      print "SYSTEM ERROR: NO ACCESS TO SOLR COLLECTION: ".$this->solr_collection;
    // Processing... resolving descriptors... all at once

    list ($BROADER_LABELS,
          $NARROWER_LABELS,
          $RELATED_LABELS) 
              = $this->resolve_skos_stw_descriptors($BROADER_DESC,
                                                    $NARROWER_DESC,
                                                    $RELATED_DESC,
                                                    $m,
                                                    $lang);
    // do something with return_document...:

  }

  $ID0= $returndocument   [0][0][0];
  $LABEL0= $returndocument[0][1][0];
  $ID1= $returndocument   [1][0][0];
  $LABEL1= $returndocument[1][1][0];
  
  
  if ($this->getSrcDebug())
    {
      $NoOfFoundDocs=count($returndocument);
      
      print "<br><br>$NoOfFoundDocs node return documents: "; var_dump($returndocument);print "<br>";
    
      print "<br>in get_stw_skos_nodes_SOLR():";
      print "<br>prepared BROADER_LABELS:<br>"; var_dump($BROADER_LABELS);
      print "<br>prepared NARROWER_LABELS:<br>"; var_dump($NARROWER_LABELS);
      print "<br>prepared RELATED_LABELS:<br>"; var_dump($RELATED_LABELS);
      
      print "<br>ROOT ID: $ID0=$LABEL0, $ID1=$LABEL1";

    } 
  
    
    //Compute each ID the path
    $LOCALrootPATH = $this->walk_stw_root_path($ID0,$lang,$recursion=0);
    if ($this->getSrcDebug())
    {
      print "<br>Result of LOCALrootPATH: (($LOCALrootPATH))";
    }
    Logger::logAction(25, array('from'=>'STWengine3->WebRefine','LOCALrootPATH'=>$LOCALrootPATH));

    //Compute all other paths by using $LOCALrootPATH adding the current label.
    
   $B= array($BROADER_LABELS, $this->denormalize_descriptor($BROADER_DESC,'stw') ,$this->rootpaths($LOCALrootPATH,$BROADER_LABELS)); 
   $N= array($NARROWER_LABELS,$this->denormalize_descriptor($NARROWER_DESC,'stw'),$this->rootpaths($LOCALrootPATH,$NARROWER_LABELS)); 
   $R= array($RELATED_LABELS, $this->denormalize_descriptor($RELATED_DESC,'stw') ,$this->rootpaths($LOCALrootPATH,$RELATED_LABELS)); 
  
	return new SRCEngineSKOSResult ( $B, $N, $R );
} // get_stw_node_SOLR



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


   /*
 * Returns a triples of array of ranked labels
 */
  private function resolve_skos_stw_descriptors(&$BROADER_DESC,&$NARROWER_DESC,&$RELATED_DESC,$m,$lang='en')
  {
     //Merge each descriptor in one array:
    $descriptors=  array_merge($BROADER_DESC,$NARROWER_DESC,$RELATED_DESC);
    $descriptors_count=count($descriptors);
    
    if ($this->getVerbose())
    {
      print "<br>".count($descriptors)." descriptors to resolve:";
      
      if ($this->getSrcDebug()) 
      {
        foreach($BROADER_DESC as $d) print "<br>Broader Desc: $d";
        foreach($NARROWER_DESC as $d) print "<br>Narrower Desc: $d";
        foreach($RELATED_DESC as $d) print "<br>Related Desc: $d";
      }
    }
    
    
    //Pick up only the needed ones:
    if ($lang=='de')
      $NEEDED_FIELD='skos_prefLabel_de';
    else if ($lang=='en')
      $NEEDED_FIELD='skos_prefLabel_en';
    
    
    $FIELDS=array('id',$NEEDED_FIELD);
    
    if (($SOLRCLIENT = init_SOLRCLIENT($this->solr_collection,'solr_index_skos_namespaces system error init SOLRCLIENT')))
    {
      $SOLRCLIENT->getPlugin('postbigrequest');

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
      //Query all entities with id in $descriptors
      $query->createFilterQuery('id')->setQuery($disjunction);

      if ($this->getVerbose())
      {
        print "<br>resolve_skos_stw_descriptors query=($disjunction) ...";
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
              $label = is_array($value)?$value[0]:$value; // take always the first one
              break;
            case 'id':
              $id=$value;
          }  
        } // each fieldname
        $descriptor_label{$id}=$label; // right label to right id
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
         print "<br> found $label for $desc";
         
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
    foreach($BROADER_DESC as $desc)
      $BROADER_LABELS{$descriptor_label{$desc}}=$RANK;;
    foreach($NARROWER_DESC as $desc)
      $NARROWER_LABELS{$descriptor_label{$desc}}=$RANK;;
    foreach($RELATED_DESC as $desc)
      $RELATED_LABELS{$descriptor_label{$desc}}=$RANK;;
    
    if ($this->getSrcDebug())
    {
      print "<br>in resolve_skos_stw_descriptors():";
      print "<br>prepared BROADER_LABELS:<br>"; var_dump($BROADER_LABELS);
      print "<br>prepared NARROWER_LABELS:<br>"; var_dump($NARROWER_LABELS);
      print "<br>prepared RELATED_LABELS:<br>"; var_dump($RELATED_LABELS);
    } 
      
    return array($BROADER_LABELS,$NARROWER_LABELS,$RELATED_LABELS);
  } // resolve_skos_stw_descriptors
  
  
  
  
/*
 * SKOS context
 * Returns a text containing base64 chunks separated by commas
 * Each chunk is the preferred label of the father ... of the term/descriptor
 * Recursive function used to calculate a context to MLT against widget results
 * 
 * See also rootpaths() to pack information
 */
  private function walk_stw_root_path($descriptor,$lang='en',$recursion=0)
  {
    $ROOTPATHSEP=',';
    if ($this->getSrcDebug())
    {
      print "<br>walk_stw_root_path called with lang=$lang and descriptor: ";var_dump($descriptor);print "<br>";
    }

    static $MAXRECURSION=100; // allow maximum 100 recursions
    $recursion++;
    
    if ($recursion > $MAXRECURSION)
    {
      print "<br>walk_stw_root_path($descriptor) superated MAXRECURSION=$MAXRECURSION";
      print "<br>PLEASE ANALYSE the case and enhance the MAXRECURSION value! ";
      print "<br>Recursion PRUNED";
      return '';
    }  
    
    //Pick up only the needed ones:
    if ($lang=='de')
      $NEEDED_FIELD='skos_prefLabel_de';
    else if ($lang=='en')
      $NEEDED_FIELD='skos_prefLabel_en';
    
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
      print "<br>in walk_stw_root_path($descriptor): $preflabel";
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
          
          $pathremote = $this->walk_stw_root_path($singlebroader_descriptor,$lang,$recursion);
          // Add:
          if ($pathremote)
          $path_to_root.=$ROOTPATHSEP.$pathremote;
        }
      }
    }
    
    //return
    return $path_to_root;
  } // walk_stw_root_path
  
  
  
  
  
  
  private function denormalize_descriptor($STW_SOLR_DESCRIPTORS,$namespace)
  {
    if (!is_array($STW_SOLR_DESCRIPTORS)) 
    {
      return str_replace($namespace.'_',$this->zbw_stw_namespaces[$namespace],$STW_SOLR_DESCRIPTORS);
    }
    else
    { 
      foreach($STW_SOLR_DESCRIPTORS as $STW_SOLR_DESCRIPTOR)
      {
        $NORMALIZED_DESCRIPTORS[] = str_replace($namespace.'_',$this->zbw_stw_namespaces[$namespace],$STW_SOLR_DESCRIPTOR);
      }
      return $NORMALIZED_DESCRIPTORS;
    }
  }   
  
	
	
} // class STWengine1



?>