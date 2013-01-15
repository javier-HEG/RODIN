<?php

/*
 * SKOS partial evaluation resources
 */
//siehe http://arc.semsol.org/docs/v2/getting_started


// http://195.176.237.62/rodin/x/app/u/SOLRinterface/solr_bridge.php?coll=bnf_rameau&method=select&q=id:*&rows=0&omitHeader=true


function get_solr_check_docs_href($solr_collection)
{
	$HOST=$_SERVER['HTTP_HOST'];
	
	global $RODINSEGMENT;
	$SOLR_CHECK_URL="http://$HOST/rodin/$RODINSEGMENT/app/u/SOLRinterface/solr_bridge.php?coll=bnf_rameau&method=select&q=id:*&rows=0&omitHeader=true";
	
	$SOLR_CHECK_HREF="<a href='$SOLR_CHECK_URL' target='blank' title='Click to open SOLR check number od docs for the named collection $solr_collection'>$solr_collection</a>";
	
	return $SOLR_CHECK_HREF;
}


function solr_index_skos_namespaces($namespaces,$solr_collection)
{
  global $printline;
  
  $documents=array();
  foreach($namespaces as $ns=>$nsdescription)
  {
    if ($printline) print "<tr><td>$ns</td><td> <b>$nsdescription</b></td></tr>";
    #USE SOLR COLLECTION $solr_collection:
    if (($SOLRCLIENT = init_SOLRCLIENT($solr_collection,'solr_index_skos_namespaces system error init SOLRCLIENT')))
    {
      $document = new Solarium_Document_ReadWrite();

      $document->setField('id', $ns);
      $document->setField('namespace', $nsdescription);
      
      $documents[]= $document;
     }
  }
  solr_synch_update(false,$solr_collection,$SOLRCLIENT, $documents,false,false);
}






//REFINE ans USE:
class SKOSentity
{
  protected $_descriptor;
  protected $_language;
  protected $_prefLabel;
  protected $_altLabel;
  protected $_hiddenLabel;
  protected $_broader;
  protected $_narrower;
  protected $_related;
  
  public function __construct()
  {
  }
}

//REFINE ans USE:
class SKOSXLentity
{
  protected $_descriptor;
  protected $_language;
  protected $_prefLabel;
  protected $_altLabel;
  protected $_hiddenLabel;
  protected $_broader;
  protected $_narrower;
  protected $_related;
  
  public function __construct()
  {
  }
}













function show_triples($store,$QUERY)
{
  print "<br>QUERY: <br>".htmlentities($QUERY)."<br><br>";
	
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		$i=0;
		print "<table border=1>";
		foreach($rows as $row) 	
		{	$i++;
			print "<tr>";
			print "<td>$i";
			print "</td>";
			print "<td nowrap>";
			print $row['s'];
			print "</td>";
			print "<td nowrap>";
			print $row['p'];
			print "</td>";
			print "<td nowrap>";
			print $row['o'];
			print "</td>";
			print "</tr>";
		}
		print "<table>";
	}
	else print "?";
}





/*
 * Returns triples for each concepts in scheme in store
 * in skos these are descriptor expressions
 */
function get_descriptors_skos($storename)
{
	global $ARCCONFIG;
  global $MAXLOOPSTEPS;
	$LOCALARCCONFIG = $ARCCONFIG;
	$LOCALARCCONFIG{'store_name'} = $storename;
	$store = ARC2::getStore($LOCALARCCONFIG);

//      FILTER regex(?s, "/descriptor/") .
	
	$QUERY=<<<EOQ
 	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	select ?s  
	where
	{
		?s 	skos:inScheme  ?x .
  } 
EOQ;

  if ($rows = $store->query($QUERY, 'rows')) 
	{
		$i=0;
		foreach($rows as $row) 	
		{	$i++;
      if ($MAXLOOPSTEPS>0 && $i>$MAXLOOPSTEPS) break;
      $descriptors[]=$row['s'];
    }

	}
  return $descriptors;
}






/*
 * Returns a lable for the given scheme if found
 * or the scheme if not found in the store
 */
function get_scheme_label($storename,$scheme)
{
	global $ARCCONFIG;
  global $MAXLOOPSTEPS;
	$LOCALARCCONFIG = $ARCCONFIG;
	$LOCALARCCONFIG{'store_name'} = $storename;
	$store = ARC2::getStore($LOCALARCCONFIG);

//      FILTER regex(?s, "/descriptor/") .
	
	$QUERY=<<<EOQ
 	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	select ?x
	where
	{
		<$scheme> skos:prefLabel  ?x .
  } 
EOQ;

	$label=$scheme; // default
  if ($rows = $store->query($QUERY, 'rows')) 
	{
		$label=$rows[0]['x'];
	}
	//else print "NO";
	
	//print "<br>Translating in storename $storename scheme $scheme to ($label)";
	//print "<br>Using query: <br>".htmlentities($QUERY)."<br>";
	
  return $label;
}






/*
 * Returns triples for each concepts in scheme in store
 * in skosxl these are concepts
 */
function get_descriptors_skosxl($storename)
{
	global $ARCCONFIG;
  global $MAXLOOPSTEPS;
	$LOCALARCCONFIG = $ARCCONFIG;
	$LOCALARCCONFIG{'store_name'} = $storename;
	$store = ARC2::getStore($LOCALARCCONFIG);
	
	$QUERY=<<<EOQ
 	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	select ?s  
	where
		 {
			?s 	skos:inScheme  <http://lod.gesis.org/thesoz/> .
      FILTER regex(?s, "/thesoz/concept/") .
   	 } 
EOQ;

  if ($rows = $store->query($QUERY, 'rows')) 
	{
		$i=0;
		foreach($rows as $row) 	
		{	$i++;
      if ($MAXLOOPSTEPS>0 && $i>$MAXLOOPSTEPS) break;
      $descriptors[]=$row['s'];
    }

	}
  return $descriptors;
}








/*
 * Returns a vector of tuples
 * from the store
 * reflecting the resource
 * following the skos standards
 */
function get_skos_resource($desc,&$store)
{	
  //print "get_skos_resource($desc)<br>";
  if (!$store) return null;
  
	$QUERY=<<<EOQ
	
	select ?p ?o
	where
		 {
			<$desc> 	?p 			?o .
		 }
EOQ;
  
  $QUERY=utf8_encode($QUERY);
	
  //print "$QUERY";
  
  $p_o_arr=array();
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		foreach($rows as $row) 	
		{
			$p_o_arr[]=array($row['p'],$row['o'],$row["o lang"]);
		}
	}
	return $p_o_arr;
}




/*
 * Returns a vector of tuples
 * from the store
 * reflecting the resource
 * following the skos standards
 */
function get_gnd_resource($desc,&$store)
{	
  //print "get_skos_resource($desc)<br>";
  if (!$store) return null;
  
	$QUERY=<<<EOQ
	
	select ?p ?o
	where
		 {
			<$desc> 	?p 			?o .
		 }
EOQ;
  
  $QUERY=utf8_encode($QUERY);
	
  //print "$QUERY";
  
  $p_o_arr=array();
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		foreach($rows as $row) 	
		{
			$p_o_arr[]=array($row['p'],$row['o'],$row["o lang"]);
		}
	}
	return $p_o_arr;
}







/*
 * Returns a list of descriptors found sparql searching for $searchterm
 * using the skos system (maybe null)
 */
function get_gnd_desc_list($searchterm,&$store)
{
	global $limit;
	//print "get_gnd_desc_list";
	
	if (strstr($searchterm,'*'))
	{
		$searchterm4query_a=explode('*',$searchterm);
		$searchterm4query=$searchterm4query_a[0]; // take only first token
		$QUERY=<<<EOQ
	
	prefix gnd2:  <http://d-nb.info/standards/elementset/gnd#>
	
	select ?d 
  {
    {
     ?d 	gnd2:preferredNameForTheCorporateBody 	?name .
     FILTER regex (?name,"$searchterm4query","i") .
    }
    
   }
   LIMIT $limit
EOQ;
/*
 * UNION
    {
     ?d 	gnd2:variantNameForTheCorporateBody 	?name .
     FILTER regex (?name,"$searchterm4query","i") .
    }
 */
	}
	else
	$QUERY=<<<EOQ
	
	prefix gnd2:  <http://d-nb.info/standards/elementset/gnd#>
	
	select ?d 
  {
    {
     ?d 	gnd2:preferredNameForTheCorporateBody 	'$searchterm' .
    }
    UNION
    {
     ?d 	gnd2:variantNameForTheCorporateBody 	'$searchterm' .
    }
   }
   LIMIT $limit
EOQ;

	$QUERY=utf8_encode($QUERY);
	//print "<br><br>exec_sparql: ".str_replace("\n","<br>",htmlentities($QUERY))."<br>";

	$result=array();
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		foreach($rows as $row) 	
		{
			$result[]= ($row['d']);
		}
	}
  
  //print "<br>Descriptors: ";var_dump($result);
  //returns list of descriptors!!
	return $result;
  
} // get_gnd_desc_list







/*
 * Returns a list of descriptors found sparql searching for $searchterm
 * using the skos system (maybe null)
 */
function get_skos_desc_list($searchterm,&$store)
{
	
	print "get_skos_desc_list";
	
	$QUERY=<<<EOQ
	
	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	
	select ?d 
	where
  {
    {
     ?d 	skos:prefLabel 	'$searchterm' .
    }
    UNION
    {
     ?d 	skos:altLabel 	'$searchterm' .
    }
   }
EOQ;

	$QUERY=utf8_encode($QUERY);
	//print "<br><br>exec_sparql: ".str_replace("\n","<br>",htmlentities($QUERY))."<br>";

	$result=array();
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		foreach($rows as $row) 	
		{
			$result[]= ($row['d']);
		}
	}
  
  //print "<br>Descriptors: ";var_dump($result);
  //returns list of descriptors!!
	return $result;
  
} // get_skos_desc_list






/*
 * Returns a list of descriptors found sparql searching for $searchterm
 * using the skosxl system (maybe null)
 */
function get_skosxl_desc_list($searchterm,&$store)
{
  
} // get_skos_desc_list







/*
 * Returns a vector of n-tupels connotating a resource
 * defined by $desc (a skosxl concept)
 * 
 * DISREGARDED graph portion: classifications inside terms.
 * REASON for disregarding: Not relevant to a RODIN SRC component
 */
function get_resource_skosxl_core($desc,$store)
##################################
# 
# returns the resource from the descriptor (concept)
#
{
	global $LANGUAGES;
  $PREFIXES=<<<EOP
  PREFIX thesozext:   <http://lod.gesis.org/thesoz/ext/> 
  PREFIX thesoz:      <http://lod.gesis.org/thesoz/> 
  PREFIX  skos:       <http://www.w3.org/2004/02/skos/core#>
  PREFIX  skosxl:     <http://www.w3.org/2008/05/skos-xl#>
  PREFIX     rdf:     <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
EOP;
  
	$QUERY=<<<EOQ
	$PREFIXES
	select ?p ?o_tmp ?x ?o ?y ?v
	where
  {
		 {
			<$desc> 	?p 			?o .
        FILTER( ?p != skos:InScheme ) .
        FILTER( ?p != rdf:type ) .
        FILTER( ?p != skosxl:prefLabel ) .
        FILTER( ?p != skosxl:altLabel ) .
		 } 
     UNION
     {
			<$desc> 	?p ?o_tmp  .
          ?o_tmp 	?x 			?o .
        FILTER( ?p != skos:InScheme ) .
        FILTER( ?p != rdf:type ) .
        FILTER( ?p = skosxl:prefLabel || ?p = skosxl:altLabel || ?p = skosxl:hiddenLabel) .
        FILTER( ?x = skosxl:literalForm || ?x = skos:notation).
		 } 
     UNION
     {
			<$desc> 	?p ?o_tmp  .
          ?o_tmp 	?x 	?o .
          ?o ?y ?v
        FILTER( ?p != skos:broader ) .
        FILTER( ?p != skos:narrower ) .
        FILTER( ?p != skos:related ) .
        FILTER( ?p != skos:InScheme ) .
        FILTER( ?p != rdf:type ) .
        FILTER( ?p = skosxl:prefLabel || ?p = skosxl:altLabel || ?p = skosxl:hiddenLabel) .
        FILTER( ?x = thesozext:EquivalenceRelationship || ?x = thesozext:CompoundEquivalence || ?x = thesozext:hasTranslation ) .
        FILTER( ?y != rdf:type && ?y != thesozext:hasTranslation ) .
    } 
  }
EOQ;
  /* second segment:    
   *    FILTER( ?x = skosxl:literalForm || ?x = skos:notation).
   *    FILTER( ?x = thesozext:EquivalenceRelationship || ?x = thesozext:CompoundEquivalence || ?x = thesozext:hasTranslation ) .
    
   * third segment:
   * longer trip: 
   * FILTER( 
   * ?x = thesozext:EquivalenceRelationship 
   * ?x = thesozext:CompoundEquivalence
   * ?x = thesozext:hasTranslation
   */
         
        
  //print "<br>SPARQL: <br>".str_replace("\n","<br>",htmlentities($QUERY));
  $p_o_arr=array();
  if(!$store)
      print "<br>get_resource_skosxl_core() store is null!!!";
  else
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		foreach($rows as $row) 	
		{
			$p_o_arr[]=array(
          $row['p'],      $row['p lang'],
          $row['o_tmp'],  $row['o_tmp lang'],
          $row['x'],      $row['x lang'],
          $row['o'],      $row['o lang'],
          $row['y'],      $row['y lang'],
          $row['v'],      $row['v lang']
          );
		}
	}
	return $p_o_arr;
}






function exec_sparql_explore_namespaces($storename,$n)
##################################
# 
# Explores every URI
# For normalisation
{
	global $ARCCONFIG;
	$LOCALARCCONFIG = $ARCCONFIG;
	$LOCALARCCONFIG{'store_name'} = $storename;
	
	var_dump($LOCALARCCONFIG);
	
	$store = ARC2::getStore($LOCALARCCONFIG);
	if (!$store->isSetUp()) {
	  $store->setUp();
	}
	
	$QUERY=<<<EOQ
	select *  
	where
		 {
			?s 	?p  ?o.
		 } 
EOQ;

if ($rows = $store->query($QUERY, 'rows')) 
	{
		$i=0;
		print "<table border=1>";
		foreach($rows as $row) 	
		{	$i++;
      $s_expressions{cut4namespace($row['s'])};
      $p_expressions{cut4namespace($row['p'])};
      $o_expressions{cut4namespace($row['o'])};
		}
		print "<table>";
	}
	else print "?";  
  
  return array($s_expressions,$p_expressions,$o_expressions,$n);
  
} // exec_sparql_explore_namespaces




/*
 * Returns a separation in left/right expression
 * list($left,$right,$literal)
 * 
 * where $right is the actual token (or literal)
 * and left the rest of the expression
 */
function analyzeURI($URIexpr,&$namespaces)
{
  $literal='';
  list($left,$right) = splitrn($URIexpr,$SEP='#',1);
  if (!$right)
    list($left,$right) = splitrn($URIexpr,$SEP='/',1);
  if (! $right  
       ||   is_numeric($right) )
    list($left,$right) = splitrn($URIexpr,$SEP='/',2);
  if (!$right)
  { // literal
    $literal=utf8_decode($URIexpr);
    $right='';
    $left='';
  } else
  {
    $left= separate_namespace($namespaces,$left.$SEP,':');
  }  
      

  return array($left,$right,$literal);
}



function prettyprintURI($URIexpr,&$namespaces)
{
  $SEP='';

  list($left,$right,$literal) = analyzeURI($URIexpr,$namespaces);
  
  if ($left)
  {
    $left= separate_namespace($namespaces,$left.$SEP,':');
    $right= regognize_change_descriptor_link($left,$right);  
  }  
  
  //print "<br>prettyprintURI($URIexpr)=($left,$right,$literal)";
// <span class='urigray'>$left</span><span class='uribold'>$right</span> <span class='uriliteral'>$literal</span>

  return <<< EOR
 $left<span class='uribold'>$right</span> <span class='uriliteral'>$literal</span>
EOR;
}








function regognize_change_descriptor_link($left,$o)
{
	
	//print "<br>regognize_change_descriptor_link($left,$o)";
	
  if (!$link && !($link = regognize_bnf_descriptor($left,$o)));
	if (!$link && !($link = regognize_loc_descriptor($left,$o)));
	if (!$link && !($link = regognize_stw_descriptor($left,$o)));
	if (!$link && !($link = regognize_soz_descriptor($left,$o)));
	if (!$link && !($link = regognize_gnd_descriptor($left,$o)));
					
  if (!$link)
    $link = $o;

  
  return $link;
}






function regognize_stw_descriptor($left,$o)
{
  $link = '';
  //BNF pattern
  
  if (strstr($left,'stw'))
  {
  	if (strstr($o,'thsys'))
		{
			print " STW THSYS ";
	  	$pattern="/thsys\/(.+)/";
	  	print "<br>STW: $left - $o"; 
	    if (preg_match($pattern,$o,$match))
	    {
	      $desc_number=$match[1];
	      $fulldescriptor = "http://zbw.eu/stw/thsys/$desc_number";
	    }
    }
		else 
		{
			$desc_number=$o;
      $fulldescriptor = "http://zbw.eu/stw/descriptor/$desc_number";
		}	
		$link = "<a href='#'
	        onclick=\"document.f.desc.value='$fulldescriptor';f.submit()\"
	        target='click to navigate'>$o</a>";
		
  }
	//print "<br>regognize_stw_descriptor($left,$o) returning $link";
	return $link;
}





function regognize_soz_descriptor($left,$o)
{
  $link = '';
  $pattern="/concept\/(\d+)/";
	if (strstr($left,'thesoz'))
  {
	  if (preg_match($pattern,$o,$match))
	  {
	    $desc_number=$match[1];
	    $fulldescriptor = "http://lod.gesis.org/thesoz/concept/$desc_number";
	    $HREF="";
	    $link = "<a href='#'
	      onclick=\"document.f.desc.value='$fulldescriptor';f.submit()\"
	      target='click to navigate'>$o</a>";
	  }
  }
	//print "<br>regognize_soz_descriptor($left,$o) returning $link";
	return $link;
}






function regognize_gnd_descriptor($left,$o)
{
  $link = '';
	
	//print "<br>regognize_gnd_descriptor(o=$o)";
	
  $pattern="/(\d+)-(.+)/";
	if (strstr($left,'gnd'))
  {
	  if (preg_match($pattern,$o,$match))
	  {
	    $desc_number1=$match[1];
	    $desc_number2=$match[2];
	    $fulldescriptor = "http://d-nb.info/gnd/$desc_number1-$desc_number2";
	    $HREF="";
	    $link = "<a href='#'
	      onclick=\"document.f.desc.value='$fulldescriptor';f.submit()\"
	      target='click to navigate'
	      title='Click to switch to $fulldescriptor'>$o</a>";
	  }
  }
	//print "<br>regognize_gnd_descriptor($left,$o) returning $link";
  return $link;
}







function regognize_loc_descriptor($left,$o)
{
  $link = '';
  //LOC pattern ... shnnnnnnnnn
  $pattern="/sh(\d+)/";
  
  if (strstr($left,'locas'))
  { 
    if (preg_match($pattern,$o,$match))
    {
      $desc_number=$match[1];

      $fulldescriptor = "http://id.loc.gov/authorities/subjects/sh$desc_number";
      $HREF="";
      //print " YES ";
      $link = "<a href='#'
        onclick=\"document.f.desc.value='$fulldescriptor';f.submit()\"
        target='click to navigate'>$o</a>";
    }
  }
	//print "<br>regognize_loc_descriptor($left,$o) returning $link";
	return $link;
}





function regognize_bnf_descriptor($left,$o)
{
  $link = '';
  //BNF pattern
  $pattern="/cb(\d+)(.+)/";
  
  if (strstr($left,'bnf') || strstr($left,'bnfc') || strstr($left,'rameau_stitch'))
  { 
    if (preg_match($pattern,$o,$match))
    {
      $desc_number=$match[1];
      $desc_suffix=$match[2]; 

      $fulldescriptor = "http://data.bnf.fr/ark:/12148/cb$desc_number$desc_suffix";
      $HREF="";
      //print " YES ";
      $link = "<a href='#'
        onclick=\"document.f.desc.value='$fulldescriptor';f.submit()\"
        target='click to navigate'>$o</a>";
    }
  }
	//print "<br>regognize_bnf_descriptor($left,$o) returning $link";
	 return $link;
}












function make_browse_link($term)
################################
{
	global $thislink, $L;

	$LINK=<<<EOL
	<a href="#" onClick="window.open('$thislink?term=$term&L='+f.L.value,'_self')" title="Klick um im aktuellen SKOS repository nach '$term' zu browsen... ">$term</a>
EOL;
	return $LINK;
}



/*
 * separate an expression into namespace and term
 * in case of href=true, namespace is a href which leads to its definition page
 * returns the term without the namespace
 */
function separate_namespace(&$namespaces,$term,$sep=':',$href=false)
{
  //OPTIMIZE
  //print "<br><br>separate_namespace ... Test substitution on <b>$term</b>";
  if (!(strstr($term,'http://')))
  {
    //print "<br>Exit: Term '<b>$term</b>' already done";
    return $term;
  }
  else
  {  
    foreach($namespaces as $ns=>$expr)
    {
      $ns_title_span_expl=$href
      									 ?"<a class='urigray' href='$expr' target='_blank' title='Click to explore namespace definition\n$expr\nin a new tab'>$ns</a>"
												 :$ns;
      //ATTENTION: GERMAN UMLAUTS ... NOT HANDLED HERE?
      $expr2=str_replace("/","\/",$expr);
      $pattern="/$expr2(.*)/";

      //print "<br>Pattern $pattern ";

      if (preg_match($pattern,$term,$match))
      {
        $matched=1;
        $nakedterm=$match[1]; //cut first "/"
        if ($nakedterm[0]=='/')
            $nakedterm=substr($nakedterm,1);

        $returnterm=$ns_title_span_expl.$sep.$nakedterm;
        //print " YES ";
        break;
      }
    }

    if (!$matched) 
    {
      $returnterm=$term;
      //print " ERROR applying $pattern -> $returnterm<br>";
    } 
    //else print " SUCCESS: $term -> $returnterm<br>";
  }
  return $returnterm;
}


/*
 * Prepare SOLR document to be indexed
 * in case mode=='bigdata' index very suddenly 
 * returns document (indexed or to be indexed)
 */
function prepare_skos_entity_solr_document(&$SOLRCLIENT,&$namespaces,$descriptor_clean,&$p_o_resource,$storename,$solr_collection,$mode,$indexdebug, $showdetails)
{
  global $SOLR_RODIN_CONFIG;
  global $SOLARIUMDIR;
  global $USER;
  global $printline;
  
  $resultNumber=0;

  $document = new Solarium_Document_ReadWrite();
  $document->id = $descriptor_clean;
  $notation= get_skos_notation($p_o_resource);
  $fulltext='';
  foreach($p_o_resource as $triple)
  {
    list($p,$o,$lang)=$triple;

    $pred= separate_namespace($namespaces,$p,'_');
    $obj= separate_namespace($namespaces,$o,'_');
		
		//Translate inScheme if possible
		if (strstr($pred,'skos_inScheme'))
		{
			$label = get_scheme_label($storename,$o);
			//Add an extra label to the inSchemeField denoting the label:
			//This should be improved by providing a lang...
			$document->addField('skos_inScheme_label', $label);
		}
    if (true
//         !strstr($pred,'skos:inScheme')  //forget
//        && !strstr($pred,'rdf:type')   //forget ??
        )
    { // foresee for indexing

      if (strstr($pred,'abel')) // Correct label, altLabel, prefLabel
      {
        if ($notation<>'')
        {
          $nobj=preg_replace("/$notation  /", '', $obj, 1); // replace only once!

          //$nobj = trim(str_replace($notation,'',$obj)); //cleaning up
          //print "<br>(<b>$nobj</b>) <-- (notation $notation) in ($obj) - $descriptor_clean";
          $obj=$nobj;
        } 

        if($lang<>'')
        {
          $pred=$pred.'_'.$lang; // add fieldname with language extension 
        }  
      }

      $fulltext.=$fulltext?' ':'';
      
      //add to fulltext body only if label but only once! (if not ylready contained
      if (strstr($pred,'abel'))
			{
				if (!strstr($fulltext,$obj))
        $fulltext.=$obj;
			}
      $document->addField($pred, $obj);
    } // foresee for indexing
  } // foreach
  //
  //print "<hr>SAVING DOCUMENTS:<br>"; var_dump($documents);

  if ($fulltext) // something filled ?
  {
    $document->addField('body', $fulltext);
	
	  if (trim($fulltext)=='') // sth wrong... 
	  {
	    $document=null;
	    if($printline) print "<tr><th align='right'></th><th valign='bottom' align='left' colspan='2'> EMPTY BODY!! smt WRONG </th><th/></tr>";
	  }
	  else
	  {
	  	//print "<br>mode: $mode";
	  	if ($mode=='bigdata') // INDEX NOW!
	  	{
	  		$documents=array($document);
	    	solr_synch_update(false,$solr_collection,$SOLRCLIENT, $documents, $indexdebug, $showdetails);
			}
	  }
  }
	else // nothing to add ...
	{
		$document=null;
		// communicate/warn $descriptor_clean, $p_o_resource
		
		print "<br><hr>prepare_skos_entity_solr_document encountered a singular situation: no data to descriptor (triples complete???)";
		print "<br>descriptor: (<b>$descriptor_clean)</b>";
		print "<br>data: <br><b>";
		var_dump($p_o_resource);
		print "</b><br><br>";
	}
  
  return $document;
} // prepare_skos_entity_solr_document









function prepare_skos_entity_solrxl_document(&$namespaces,$descriptor_clean,&$p_o_resource,$solr_collection)
{
	//No difference as for the SKOS case
	return prepare_skos_entity_solr_document($namespaces,$descriptor_clean,$p_o_resource,$solr_collection);
	
} // prepare_skos_entity_solrxl_document







function get_skos_notation($p_o_resource)
{
  $notation='';
  foreach($p_o_resource as $pair)
  {
      list($p,$o)=$pair;
      if (strstr($p,'http://www.w3.org/2004/02/skos/core#notation'))
      {
        $notation=$o;
        break;
      }   
  }
  
  //print "<br><b>scan_skos_notation:  ($notation)</b>";
  
  return trim($notation);
}







function exec_sparql_explore($storename)
##################################
# 
# Computes the query to SKOS $verb
# $verb= related, broader, narrower
{
	global $ARCCONFIG;
	$LOCALARCCONFIG = $ARCCONFIG;
	$LOCALARCCONFIG{'store_name'} = $storename;
	
	var_dump($LOCALARCCONFIG);
	
	$store = ARC2::getStore($LOCALARCCONFIG);
	if (!$store->isSetUp()) {
	  $store->setUp();
	}
	
	$QUERY=<<<EOQ
	select *  
	where
		 {
			?s 	?p  ?o.
		 } 
EOQ;

	show_triples($store,$QUERY);
  
}





function exec_sparql_skos($verb,$term,$labeltype='prefLabel')
##################################
# 
# Computes the query to SKOS $verb
# $verb= related, broader, narrowe
#
# example: exec_sparql('related' ,$term);
#
{
	global $store;
	global $LANGUAGES;
  
  //print "<br>exec_sparql_skos $verb,$term,$labeltype";
  
	$QUERY=<<<EOQ
	
	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	
	select ?x 
	where
		 {
			?d1 	skos:$labeltype  ?x.
			?d1 	skos:$verb 			?d2 .
			?d2 	skos:prefLabel 	'$term' .
		 } 
EOQ;
	//print "<br><br>exec_sparql: $QUERY <br>";

	$result=array();
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		foreach($rows as $row) 	
		{
			if (in_array($row['x lang'],$LANGUAGES)) 
				$result[]= ($row['x']);
		}
	}
  
  
  //print "<hr>$verb:<br>"; var_dump($result);

  
	return $result;
}






function exec_sparql_skosxl($verb,$term,$labeltype='prefLabel')
##################################
# 
# Computes the query to SKOS $verb
# $verb= related, broader, narrowe
#
# example: exec_sparql('related' ,$term);
#
{
	global $store;
	global $LANGUAGES;
	
  
  print "<br>exec_sparql_skosxl $verb,$term,$labeltype";
//) .
  
	$QUERY=<<<EOQ
	
	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	
	select ?x 
	where
		 {
			?d1 	skos:$labeltype  ?x.
			?d1 	skos:$verb 			?d2 .
			?d2 	skos:prefLabel 	'$term' .
		 } 
EOQ;
	//print "<br><br>exec_sparql: $QUERY <br>";

	$result=array();
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		foreach($rows as $row) 	
		{
			if (in_array($row['x lang'],$LANGUAGES)) 
				$result[]= make_browse_link($row['x']);
		}
	}
	return $result;
}




function get_skosxl_resource($desc,$store)
{
  global $KOSTYPE;
  if ($KOSTYPE=='SKOS')
  {
    return get_resource_skos($desc);
  }
  else if ($KOSTYPE=='SKOSXL')
  {
    return get_resource_skosxl($desc,$store);
  }
}


function get_resource_skos($desc)
##################################
# 
# returns the resource from the descritpor
#
{
	global $store;
	global $LANGUAGES;
	
	$QUERY=<<<EOQ
	
	select ?p ?o
	where
		 {
			<$desc> 	?p 			?o .
		 } 
EOQ;
  $p_o_arr=array();
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		foreach($rows as $row) 	
		{
			$p_o_arr[]=array($row['p'],$row['o']);
		}
	}
	return $p_o_arr;
}



?>
</body>
</html>