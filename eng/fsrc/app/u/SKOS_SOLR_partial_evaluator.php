<html>
<head>
<title>    SOLR SKOS Partial Evaluator
</title> 
</head>
<body>

<?php

//siehe http://arc.semsol.org/docs/v2/getting_started

include("../sroot.php");
$PATH2U="../../gen/u";
include_once("$PATH2U/arc/ARC2.php");
include_once("$PATH2app/u/SOLRinterface/solr_init.php");

$MAXLOOPSTEPS=$_REQUEST['loop'];



print "<h2> SOLR SKOS Partial Evaluator </h2>";
print "Geladen:";
//print "<br><a href='$SKOS_DBPEDIA_FILE' title='File einsehen im anderen Tab' target='_blank'>DBPEDIA STW</a>";
print "<br><a href='$SKOS_ZBW_FILE' title='File einsehen im anderen Tab' target='_blank'>SCHWEIZERISCHE NATIONALBANK STW (Standard Thesaurus Wirtschaft)</a><br><br>";

$SUBMITONENTER=" onKeyPress=\"return submitenter(this,event)\"";



//exec_sparql_explore('dbpediastw');
//exec_sparql_explore('zbw');
solr_skos_indexing_evaluator('zbw');





$zbw_stw_namespaces=null;


function solr_skos_indexing_evaluator($storename)
##################################
# 
# navigates the SKOS thesaurus
# gathers SKOS entities
# index them on SOLR together with namespaces
{
  
  $zbw_stw_namespaces=array(
                  
              'cc'=>       'http://creativecommons.org/ns#' ,
              'dcterms'=>  'http://purl.org/dc/terms/' ,
              'gbv'=>      'http://purl.org/ontology/gbv/' ,
              'owl'=>      'http://www.w3.org/2002/07/owl#' ,
              'rdf'=>      'http://www.w3.org/1999/02/22-rdf-syntax-ns#' ,
              'rdfs'=>     'http://www.w3.org/2000/01/rdf-schema#' ,
              'skos'=>     'http://www.w3.org/2004/02/skos/core#' ,
              'stw'=>      'http://zbw.eu/stw/' ,
              'xsd'=>      'http://www.w3.org/2001/XMLSchema#' ,
              'zbwext'=>   'http://zbw.eu/namespaces/zbw-extensions/' ,
              'base'=>     'http://zbw.eu/stw/'
);
  
	global $ARCCONFIG;
	$LOCALARCCONFIG = $ARCCONFIG;
	$LOCALARCCONFIG{'store_name'} = $storename;
	
	$store = ARC2::getStore($LOCALARCCONFIG);
	if (!$store->isSetUp()) {
	  $store->setUp();
	}
   
  solr_index_skos_namespaces($zbw_stw_namespaces);

  
  print "<table>";
  print "<tr><th>Namespaces:</th><th/></tr>";
    
  foreach($zbw_stw_namespaces as $ns=>$nsdescription)
    print "<tr><td>$ns</td><td> <b>$nsdescription</b></td></tr>";
  
  print "<tr><td colspan='2'></td></tr>";

  
	$descriptors = get_descriptors($storename);

  foreach($descriptors as $descriptor)
  {
    
    $i++;
    //if ($i==2) break;
    $descriptor_clean= separate_namespace($zbw_stw_namespaces,$descriptor,'_');

    print "<tr height='60'><th align='right'>$i</th><th valign='bottom' align='left' colspan='2'>$descriptor_clean</th><th/></tr>";
    
    $p_o_resource=get_resource($store,$descriptor);
  
    //print "<hr>"; var_dump($p_o_resource);
    
//    foreach($p_o_resource as $triple)
//    {
//      list($p,$o,$lang)=$triple;
//      
//      $pred= separate_namespace($zbw_stw_namespaces,$p);
//      $obj= separate_namespace($zbw_stw_namespaces,$o);
//      
//      print "<tr><td/><td>$pred</td><td> <b>$obj</b></td></tr>";
//    }
      solr_index_skos_entity($zbw_stw_namespaces,$descriptor_clean,$p_o_resource);
  }
  print "</table>";  
  
} // solr_skos_indexing_evaluator



function solr_index_skos_namespaces($zbw_stw_namespaces)
{
  $documents=array();
  foreach($zbw_stw_namespaces as $ns=>$nsdescription)
  {
    print "<tr><td>$ns</td><td> <b>$nsdescription</b></td></tr>";
    #USE SOLR COLLECTION 'src_zbw_stw':
    $solr_collection='zbw_stw';
    if (($SOLRCLIENT = init_SOLRCLIENT($solr_collection,'solr_index_skos_namespaces system error init SOLRCLIENT')))
    {
      $document = new Solarium_Document_ReadWrite();

      $document->setField('id', $ns);
      $document->setField('namespace', $nsdescription);
      
      $documents[]= $document;
     }
  }
  solr_synch_update(false,$solr_collection,$SOLRCLIENT, $documents,true,true);
  
}



function solr_index_skos_entity(&$zbw_stw_namespaces,$descriptor_clean,$p_o_resource)
{
  global $SOLR_RODIN_CONFIG;
  global $SOLARIUMDIR;
  global $USER;
  
  #USE SOLR COLLECTION 'src_zbw_stw':
  $solr_collection='zbw_stw';
  if (($SOLRCLIENT = init_SOLRCLIENT($solr_collection,'solr_index_skos_entity system error init SOLRCLIENT')))
  {
  $resultNumber=0;
  
    //print "<br>idx $descriptor_clean";

    $document = new Solarium_Document_ReadWrite();

    $document->id = $descriptor_clean;
    $notation= scan_skos_notation($p_o_resource);
    $fulltext='';
    foreach($p_o_resource as $triple)
    {
      list($p,$o,$lang)=$triple;
      
      $pred= separate_namespace($zbw_stw_namespaces,$p,'_');
      $obj= separate_namespace($zbw_stw_namespaces,$o,'_');
      if (
           !strstr($pred,'skos:inScheme')  //forget
//        && !strstr($pred,'rdf:type')      //forget ??
          )
      { // foresee for indexing
        
        if (strstr($pred,'abel')) // Correct label, altLabel, prefLabel
        {
          if ($notation<>'')
          {
            //print "<br>notation $notation: ($obj)-->";
            $obj = trim(str_replace($notation,'',$obj)); //cleaning up
            //print "($obj)";
            
          } 
          //Lables must be utf8 to be rendered in solr
          $obj= utf8_encode($obj);

          if($lang<>'')
          {
            $pred=$pred.'_'.$lang; // add fieldname with language extension 
          }  
          
        }
        $fulltext.=$fulltext?' ':'';
        $fulltext.=$obj;
//        print "<br>fulltest: ($fulltext)";
        $document->addField($pred, $obj);
      } // foresee for indexing
    } // foreach
    //
    //print "<hr>SAVING DOCUMENTS:<br>"; var_dump($documents);

    $document->addField('body', $fulltext);
    $documents= array($document);
    
    solr_synch_update(false,$solr_collection,$SOLRCLIENT, $documents,true,true);
    
  } //SOLRCLIENT OK
} // solr_index_skos_entity



function scan_skos_notation($p_o_resource)
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
  
  public function __construct($bla)
  {
    $this->_bla = $bla;
  }
  
  
}











function separate_namespace(&$zbw_stw_namespaces,$term,$sep=':')
{
  //OPTIMIZE
  //print "<br>Test substitution on <b>$term</b>";

  foreach($zbw_stw_namespaces as $ns=>$expr)
  {
    //ATTENTION: GERMAN UMLAUTS ... NOT HANDLED HERE?
    $expr2=str_replace("/","\/",$expr);
    $pattern="/$expr2(.*)/";
    
    //print "<br>Pattern $pattern ";
    
    if (preg_match($pattern,$term,$match))
    {
      $matched=1;
      $nsterm=$ns;
      $nakedterm=$match[1]; //cut first "/"
      if ($nakedterm[0]=='/')
          $nakedterm=substr($nakedterm,1);
      
      $returnterm=$ns.$sep.$nakedterm;
      //print " YES ";
      break;
    }
  }
  
  if (!$matched) 
  {
    $returnterm=$term;
    //print " ERROR -> $returnterm<br>";

  } 
  //else print " SUCCESS -> $returnterm<br>";
  
  return $returnterm;
}



function get_resource(&$store,$desc)
##################################
# 
# returns the resource from the descritpor
#
{	
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
			$p_o_arr[]=array($row['p'],$row['o'],$row["o lang"]);
		}
	}
	return $p_o_arr;
}







function get_descriptors($storename)
##################################
# 
# Computes the query to SKOS $verb
# $verb= related, broader, narrower
{
	global $ARCCONFIG;
  global $MAXLOOPSTEPS;
	$LOCALARCCONFIG = $ARCCONFIG;
	$LOCALARCCONFIG{'store_name'} = $storename;
		
	$store = ARC2::getStore($LOCALARCCONFIG);
	if (!$store->isSetUp()) {
	  $store->setUp();
	}
	
	$QUERY=<<<EOQ
	select *  
	where
		 {
			?s 	<http://www.w3.org/2004/02/skos/core#inScheme>  <http://zbw.eu/stw>.
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







function make_browse_link($term)
################################
{
	global $thislink, $L;

	$LINK=<<<EOL
	<a href="#" onClick="window.open('$thislink?term=$term&L='+f.L.value,'_self')" title="Klick um im aktuellen SKOS repository nach '$term' zu browsen... ">$term</a>
EOL;
	return $LINK;
}





?>
</body>
</html>