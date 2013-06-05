<html>
<head>
  
    <link rel="stylesheet" type="text/css" href="../../../../app/css/rodin.css" />

<title> RDF GND Partial Evaluator SOLR INDEXER
</title> 
</head>
<body>

<?php

	#
	# Feb 2013 HEG
	# Author: fabio.ricci@ggaweb.ch  
	# Tel.: +41-76-5281961
  #

include("../../sroot.php");
$PATH2U="../../../../gen/u";
include_once("$PATH2U/arc/ARC2.php");
include_once("$DOCROOT$UPATH1/SOLRinterface/solr_interface.php");
include_once("../FRIutilities.php");
include_once("SKOS_SOLR_partial_evaluator_resources.php");

//Load Class GNDengineSPARQL for special functions:
include_once("../../engine/SRCEngineInterface.php");
include_once("../../engine/SRCengine.php");
include_once("../../engine/GNDengine/GNDengine.php");
include_once("../../engine/GNDengine/GNDengineSPARQL/GNDengineSPARQL.php");


//We need on the server at HEG to enhance php execution time limit, 
//since this server is slowlee and need more time than the local power macs
set_time_limit ( 100000 ); // Feature in 5.3.0 deprecated, in 5.4.0 deleted - but useful

$storename=$_GET['storename'];
if (!$storename)
{
	print "Please provide a storename and a solr_collection to take data";
	exit;
}
$solr_collection = $_GET['solr_collection'];
if (!$solr_collection)
	$solr_collection = $storename;


//*************************************************************************
$mode = $_REQUEST['mode'];    // fast, bigdata
if (!$mode) $mode = 'fast';

$printline = $_REQUEST['printline'];    // 1 --> descriptor line printed
$doindex = $_REQUEST['doindex'];        // 1 --> we really INDEX into SOLR
$indexdebug = $_REQUEST['indexdebug'];  // 1 --> each indexed document is shown in debug file (see solr_interface)
$showdetails= $_REQUEST['showdetails']; // 1 --> each RDF resource are shown details (and even in the debug file)
$MAXLOOPSTEPS=$_REQUEST['loop'];        // n (>0) --> perform only n steps (index/show only n resources from store)

//*************************************************************************
//?storename=bnf_rameau&doindex=1&loop=&printline=1&indexdebug=0&mode=bigdata

if ($storename && $solr_collection)
{
		
	$descriptors = get_descriptors_gnd($storename);
	$cnt_descriptors = count($descriptors);
	
	$SOLR_DOC_CHECK_HREF=get_solr_check_docs_href($solr_collection);
	
	print "<h2> SOLR GND Partial Evaluator </h2>";
	print "<h3>Indexing triple store '$storename' to SOLR -> collection '$SOLR_DOC_CHECK_HREF'</h3>";
	
	if ($doindex)
	{
		if ($mode=='bigdata')
			print "<h3>USING BIG DATA (indexing) = one skos obj at a time</h3>";
		else {
		if ($mode=='fast')
		{
			print "<h3>USING FAST (indexing) = all skos objs in one shot</h3>";
			
			//CHECK FEASIBILITY
			if ($cnt_descriptors > 20000)
			{
				fontprint("<hr>ARE YOU SURE? YOU HAVE MORE THAN 20000 descriptors ($cnt_descriptors)<br>"
								."You should use mode=bigdata ! <hr>",'red');
				exit;
			}
		}
		}
	}
}


$seephperrors = $_REQUEST['seephperrors'];
if ($seephperrors) error_reporting(E_ALL); // forces error reporting



//exec_sparql_explore("dbpediastw');
//exec_sparql_explore('zbw');

solr_gnd_indexing_evaluator($storename,$solr_collection,$mode,$descriptors);



function solr_gnd_indexing_evaluator(&$storename,$solr_collection,$mode,&$descriptors)
##################################
# 
# navigates the GND thesaurus
# gathers GND entities a la skos (using GND predicates)
# index them on SOLR together with namespaces
{
  global $printline;
  global $doindex;
  global $indexdebug;
  global $showdetails;
  global $zbw_stw_namespaces;
  global $gesis_thesoz_namespaces;
  global $bnf_rameau_namespaces;
  global $loc_sh_namespaces;
	global $ARCCONFIG;
  
  $namespaces=get_namespaces_from_DB();
	
	$LOCALARCCONFIG = $ARCCONFIG;
	$LOCALARCCONFIG{'store_name'} = $storename;
	
	$store = ARC2::getStore($LOCALARCCONFIG);
	if (!$store->isSetUp()) {
	  $store->setUp();
	}
 	
 	$SOLRCLIENT = init_SOLRCLIENT($solr_collection,'solr_skos_indexing_evaluator system error init SOLRCLIENT');

	$aGNDengineSPARQL = new GNDengineSPARQL();

  
  print "<table>";
  
  //Read every main resource using its main descriptor:
  $cnt_descriptors = count($descriptors);
  
  print "<tr height='40'><td/><td><b>$cnt_descriptors descriptors</b> found in store!</td></tr>";
		
  if ($doindex) print "<tr height='40'><td/><td><b>Indexing RDF entities into SOLR collection '$solr_collection' ...</b></td></tr>";
  else 
  {  
    if($printline)
    print "<tr height='40'><td/><td><b>Showing RDF entities ...</b></td></tr>";
  }
   
  $documents=array();
  foreach($descriptors as $descriptor)
  {
    $i++;
    //if ($i==2) break;
    $descriptor_pretty = prettyprintURI($descriptor,$namespaces);
    $descriptor_clean= separate_namespace($namespaces,$descriptor,'_');

    if($printline) print "<tr height='20'><th align='right'>$i</th><th valign='bottom' align='left' colspan='2'>$descriptor_pretty </th><th/></tr>";

    $p_o_resource=get_gnd_resource($descriptor,$store);
    
    //print "<hr>"; var_dump($p_o_resource);
	  if ($showdetails)
	  {
	    foreach($p_o_resource as $triple)
	    {
	      list($p,$o,$lang)=$triple;
	      
	      $pred= separate_namespace($namespaces,$p);
	      $obj= separate_namespace($namespaces,$o);
	      
	      print "<tr><td/><td>$pred</td><td> <b>$obj</b></td></tr>";
	    }
	  }
	  
	  if ($doindex) 
	  {
	    $doc = prepare_gnd_entity_solr_document($SOLRCLIENT,$aGNDengineSPARQL,$namespaces,$descriptor,$descriptor_clean,$p_o_resource,$storename,$solr_collection,$mode,$indexdebug, $showdetails);
	    
	    if ($mode=='fast')
	    {
		    if ($doc)
		      $documents[] = $doc;
			} 
	      //print "<hr>doc: ";var_dump($doc);
	   }
	  } // foreach descriptor
	  print "</table>";  
	    
	  if ($mode=='fast') // Index all in one
		{
		  if ($SOLRCLIENT)
		  {
		    //index $documents all in one
		    solr_synch_update(false, $solr_collection, $SOLRCLIENT, $documents, $indexdebug, $showdetails);
		  } // SOLRCLIENT
		}
	} // solr_gnd_indexing_evaluator




?>
</body>
</html>