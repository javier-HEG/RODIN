<html>
<head>
      <link rel="stylesheet" type="text/css" href="../../../../app/css/rodin.css" />
<title> RDF SKOSXL Partial Evaluator SOLR INDEXER
</title> 
</head>
<body>

<?php

$storename='gesis_thesoz';
$solr_collection = $storename;
$KOSTYPE='SKOSXL';


include("../../sroot.php");
$PATH2U="../../../../gen/u";
include_once("$PATH2U/arc/ARC2.php");
include_once("$DOCROOT$UPATH1/SOLRinterface/solr_interface.php");
include_once("../FRIutilities.php");
include_once("SKOS_SOLR_partial_evaluator_resources.php");


//We need on the server at HEG to enhance php execution time limit, 
//since this server is slowlee and need more time than the local power macs
set_time_limit ( 10000 ); // 2.5h -> Feature in 5.3.0 deprecated, in 5.4.0 deleted - but useful

//*************************************************************************

$printline = $_REQUEST['printline'];    // 1 --> descriptor line printed
$doindex = $_REQUEST['doindex'];        // 1 --> we really INDEX into SOLR
$indexdebug = $_REQUEST['indexdebug'];  // 1 --> each indexed document is shown in debug file (see solr_interface)
$showdetails= $_REQUEST['showdetails']; // 1 --> each RDF resource are shown details (and even in the debug file)
$MAXLOOPSTEPS=$_REQUEST['loop'];        // n (>0) --> perform only n steps (index/show only n resources from store)

//*************************************************************************

$seephperrors = $_REQUEST['seephperrors'];
if ($seephperrors) error_reporting(E_ALL); // forces error reporting


if ($storename && $solr_collection)
{
	$SOLR_DOC_CHECK_HREF=get_solr_check_docs_href($solr_collection);
	
	print "<h2> SOLR SKOS XL Partial Evaluator </h2>";
	print "<h3>Indexing triple store '$storename' to SOLR -> collection '$SOLR_DOC_CHECK_HREF'</h3>";
	
	if ($doindex)
	{
		if ($mode=='bigdata')
			print "<h3>USING BIG DATA (indexing) = one skos obj at a time</h3>";
		else {
		if ($mode=='fast')
			print "<h3>USING FAST (indexing) = all skos objs in one shot</h3>";
		}
	}
}

solr_skosxl_indexing_evaluator($storename,$solr_collection);





function solr_skosxl_indexing_evaluator($storename,$solr_collection)
##################################
# 
# navigates the SKOS thesaurus
# gathers SKOS entities
# index them on SOLR together with namespaces
{
  global $printline;
  global $doindex;
  global $indexdebug;
  global $showdetails;
  global $gesis_thesoz_namespaces;
  
  //Set right namespaces to use:
  $namespaces=get_namespaces_from_DB();
	
	global $ARCCONFIG;
	$LOCALARCCONFIG = $ARCCONFIG;
	$LOCALARCCONFIG{'store_name'} = $storename;
	
	$store = ARC2::getStore($LOCALARCCONFIG);
	 
  /*
   * The following method is used also for SKOSXL
   */
  // WE DO NOT NEED TO STORE namespaces into SOLR ANYMORE
	// NAMESPACES ARE IN the DB 
  // solr_index_skos_namespaces($namespaces,$solr_collection);
  
  print "<table>";
  
	$descriptors = get_descriptors_skosxl($storename);
  
  $cnt_descriptors = count($descriptors);
  
  print "<tr height='40'><td/><td><b>$cnt_descriptors descriptors</b> extracted from store!</td></tr>";
  if ($doindex) print "<tr height='40'><td/><td><b>Indexing RDF entities into SOLR collection '$solr_collection' ...</b></td></tr>";
  else print "<tr height='40'><td/><td><b>Showing RDF entities ...</b></td></tr>";

  $documents=array();
  foreach($descriptors as $descriptor)
  {
   $i++;
   //if ($i==2) break;

   $descriptor_pretty = prettyprintURI($descriptor,$namespaces);
   $descriptor_clean = separate_namespace($namespaces,$descriptor,'_');

   if($printline) print "<tr height='20'><th align='right'>$i</th><th valign='bottom' align='left' colspan='2'>$descriptor_pretty</th><th/></tr>";

   $p_o_resource=get_skosxl_resource($descriptor,$store,$namespaces);

   if (!$p_o_resource)
   {
      fontprint("<hr>Store <b>'$storename'</b>: EMPTY Resource on descriptor: $descriptor_pretty ($descriptor) is: ",'red');; var_dump($p_o_resource);
   }
   else // ok - show or index
   {  
      if ($showdetails)
      {
       foreach($p_o_resource as $triple)
       {
         list($p,$o,$lang)=$triple;

         $pred= separate_namespace($namespaces,$p);
         $obj= separate_namespace($namespaces,$o);

         print "<tr><td/><td>$pred</td><td> <b>$obj</b></td></tr>";
       }
      } // $showdetails
      if ($doindex) 
      {
         $doc = prepare_skos_entity_solrxl_document($namespaces,$descriptor_clean,$p_o_resource,$solr_collection);

         if ($doc)
           $documents[] = $doc;
      }
    } // show or index
 } // foreach
 
 print "</table>";
    
  
  if (($SOLRCLIENT = init_SOLRCLIENT($solr_collection,'solr_index_skosxl_entity system error init SOLRCLIENT')))
  {
    //index $documents
    solr_synch_update(false, $solr_collection, $SOLRCLIENT, $documents, $indexdebug, $showdetails);
  } // SOLRCLIENT
  
} // solr_skos_indexing_evaluator






/*
 * Returns a set of triples
 * connotating the resource described by $desc (a concept)
 * $desc (descriptor) is a concept
 * Due to the inner graph structure
 * we have to grasp data from different positions
 * See used method 'get_resource_skosxl_core()' for more datails.
 */
function get_skosxl_resource($desc,&$store,&$namespaces)
##################################
# 
# returns the resource from the descritpor
#
{	
	$p_o_arr = get_resource_skosxl_core($desc,$store);

  //Interprete and flatten down elements
  foreach($p_o_arr as $arr)
  {
    $fieldname = $value = $lang='';
    $found=false;
    list( $p,     $p_lang,
          $o_tmp, $o_tmp_lang,
          $x,     $x_lang,
          $o,     $o_lang,
          $y,     $y_lang, 
          $v,     $v_lang
       ) = $arr;
  
    $_p = separate_namespace($namespaces,$p,'_');
    $fieldname=$_p;
    //The values are in different places...
    //depending on the value of $p
    
    switch ($_p)
    {
      case 'skos_broader': 
      case 'skos_narrower': 
      case 'skos_related': 
      case 'skos_exactMatch':
      case 'skos_relatedMatch':
      case 'skos_broadMatch':
      case 'skos_broadMatch':
              $value=separate_namespace($namespaces,$o,'_');
              $found=true;
           break;
      case 'skosxl_altLabel':
      case 'skosxl_prefLabel':
      case 'skosxl_hiddenLabel':
              
            $_x = separate_namespace($namespaces,$x,'_');
            switch ($_x)
            {
              // The notation needs a better records fetching mechanism
              // since we do not neet a notation match we simply disregard it.
//              case 'skos_notation' :
//                      //modify the fieldname to fit a skos/solr agreement, where the attr. Label contains the language abbreviation.
//                      $fieldname=$_p.'_'.$o_lang;
//                      $value=separate_namespace($namespaces,$o,'_');
//                    break;
              case 'skosxl_literalForm':
                      //modify the fieldname to fit a skos/solr agreement, where the attr. Label contains the language abbreviation.
                      $fieldname=$_p.'_'.$o_lang;
                      $value=separate_namespace($namespaces,$o,'_');
                      $lang=$p_lang;
                      $found=true;
                    break;
              case 'thesozext_hasTranslation' :
                      //modify the fieldname to fit a skos/solr agreement, where the attr. Label contains the language abbreviation.
                      $fieldname=$_p.'_'.$v_lang; // it is a translation
                      $value=separate_namespace($namespaces,$v,'_');
                      $lang=$v_lang;
                      $found=true;
                    break;
               case 'thesozext_CompoundEquivalence' :
                    $_y=separate_namespace($namespaces,$y,'_');
                    switch($_y)
                    {
                      case 'thesozext_preferredTermComponent' :
                            $fieldname=$_p.'_ptc'; // name label with this suffix
                            $value=separate_namespace($namespaces,$v,'_');
                            $found=true;
                            break;
                      case 'thesozext_compoundNonPreferredTerm' :
                            $fieldname=$_p.'_cnpt'; // name label with this suffix
                            $value=separate_namespace($namespaces,$v,'_');
                            $found=true;
                            break;
                    } // switch $_y
                    break;
              case 'thesozext_EquivalenceRelationship' :
                    $_y=separate_namespace($namespaces,$y,'_');
                    switch($_y)
                    {
                      case 'thesozext_USE' :
                            $fieldname=$_p.'_use'; // name label with this suffix
                            $value=separate_namespace($namespaces,$v,'_');
                            $found=true;
                            break;
                      case 'thesozext_UF' :
                            $fieldname=$_p.'_uf'; // name label with this suffix
                            $value=separate_namespace($namespaces,$v,'_');
                            $found=true;
                            break;
                    } // switch $_y
                    break;
            } // switch $_x
            break;
    } // switch $_p
    
    if ($found)
    {
      if ($lang)
        $field_value_pairs[] = array($fieldname,$value,$lang);
      else
        $field_value_pairs[] = array($fieldname,$value);
    }
  } // foreach
  
  return $field_value_pairs;
}






?>
</body>
</html>