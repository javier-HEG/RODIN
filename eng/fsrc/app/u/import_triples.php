<?php
/*
 * import_triples
 * Author: Fabio Ricci
 * 
 */
//siehe http://arc.semsol.org/docs/v2/getting_started
include("../sroot.php");
require_once "../../../app/tests/Logger.php";

$PATH2U="../../../gen/u";
include_once("$PATH2U/arc/ARC2.php");
include_once("FRIutilities.php");

$object=$_GET['object'];
$method=$_GET['method']; //todo or load
$maxfiles=$_GET['maxfiles'];

//Possible uses:
$SYNOPSIS=<<<EOS
fsrc/u/import_triples.php?object=bnf_rameau_rameau&method=todo  -> stores filenames into DB for later
fsrc/u/import_triples.php?object=bnf_rameau_rameau&method=load  -> loads every
fsrc/u/import_triples.php?object=bnf_rameau_rameau&method=load&maxfiles=3  -> loads 3 from the remaining todos files into store
fsrc/u/import_triples.php?object=bnf_rameau_rameau&method=info  -> shows todos (still to load)
fsrc/u/import_triples.php?object=bnf_rameau_rameau&method=done  -> shows all done triple loads sorted by timestamp_statistics
fsrc/u/import_triples.php?object=bnf_rameau_rameau&method=delete  -> deletes all rows with storename (ATTENTION!!)
EOS;

/* LOAD will call the Web reader, which will call the
format detector, which in turn triggers the inclusion of an
appropriate parser, etc. until the triples end up in the store. */

//We need on the server at HEG to enhance php execution time limit, 
    //since this server is slowlee and need more time than the local power macs
    set_time_limit ( 1000000 ); // 250h -> Feature in 5.3.0 deprecated, in 5.4.0 deleted - but useful right now
    

$ZWB_STW_RDF_SOURCE="$PATH2U/data/SKOS/ZBW/stw.rdf";
$GESIS_THESOZ_RDF_SOURCE="$PATH2U/data/SKOS/GESIS/thesoz_0_8.xml";


$BNF_RAMEAU_RAMEAU="$PATH2U/data/SKOS/RAMEAU/databnf_rameau_xml_2012-11-15/";
$BNF_RAMEAU_VOCABULARIES="$PATH2U/data/SKOS/RAMEAU/vocabularies/";
$BNF_RAMEAU_AUTHORS="$PATH2U/data/SKOS/RAMEAU/databnf_authors_xml_2012-11-15/";
$BNF_RAMEAU_EXTREFS="$PATH2U/data/SKOS/RAMEAU/databnf_externalrefs_xml_2012-11-15/";
$BNF_RAMEAU_ORG="$PATH2U/data/SKOS/RAMEAU/databnf_org_xml_2012-11-15/";
$BNF_RAMEAU_WORKS="$PATH2U/data/SKOS/RAMEAU/databnf_works_xml_2012-11-15/";

$DNB_GND_TURTLE="$PATH2U/data/SKOS/GND/TURTLE/";



$LOC_SH_SKOS="$PATH2U/data/SKOS/LOC/loc_skos/";


$URL_IMPORT_LOG_FILE="$WEBROOT$RODINROOT/usabilityLogFile.txt";
$HREF_IMPORT_LOG_FILE="<a href='$URL_IMPORT_LOG_FILE' >SEE IMPORTLOGFILE</a>";

$URL_IMPORT_BASE= $_ENV['SCRIPT_NAME']."?&object=$object"; 
$URL_IMPORT_DONE="$URL_IMPORT_BASE&method=done";
$HREF_IMPORT_DONE="<a href='$URL_IMPORT_DONE' title='Show imported file statistics'> DONE </a>";

$URL_IMPORT_INFO="$URL_IMPORT_BASE&method=info";
$HREF_IMPORT_INFO="<a href='$URL_IMPORT_INFO' title='Show open triple file tasks'> INFO </a>";

$URL_IMPORT_TODO="$URL_IMPORT_BASE&method=todo";
$HREF_IMPORT_TODO="<a href='$URL_IMPORT_TODO' title='Load (once) triple file tasks into database'> TODO </a>";

$URL_IMPORT_LOAD="$URL_IMPORT_BASE&method=load&maxfile=-1";
$HREF_IMPORT_LOAD="<a href='$URL_IMPORT_LOAD' title='LOAD open triple file tasks - ARE YOU SURE ?'> LOAD </a>";

$COMMANDS="$HREF_IMPORT_LOG_FILE - $HREF_IMPORT_INFO / $HREF_IMPORT_DONE / $HREF_IMPORT_TODO / $HREF_IMPORT_LOAD TASKS";


$OBJECTS=array();

switch ($object)
{
  case 'loc_sh_skos':
    $OBJECTS=array('loc_sh'=>$LOC_SH_SKOS);
    print "Importing RDF object <b>$LOC_SH_SKOS</b> into ARC2 local store <b>'".(implode(',',array_keys($OBJECTS)))."'</b> ..."
            ."<br>$COMMANDS<br>";
    break;
	
	case 'dnb_gnd':
    $OBJECTS=array('dnb_gnd'=>$DNB_GND_TURTLE);
    print "Importing TURTLE object <b>$DNB_GND_TURTLE</b> into ARC2 local store <b>'".(implode(',',array_keys($OBJECTS)))."'</b> ..."
            ."<br>$COMMANDS<br>";
    break;

  case 'bnf_rameau_rameau':
    $OBJECTS=array(
    		'bnf_rameau'=>$BNF_RAMEAU_VOCABULARIES
    		//'bnf_rameau'=>$BNF_RAMEAU_RAMEAU
				); 
    print "Importing RDF object <br><b>$BNF_RAMEAU_VOCABULARIES<br>+$BNF_RAMEAU_RAMEAU</b> into ARC2 local store <b>'".(implode(',',array_keys($OBJECTS)))."'</b> ..."
            ."<br>$COMMANDS<br>";
    break;
  /*
  case 'bnf_rameau_authors':
    $OBJECTS=array('bnf_rameau'=>$BNF_RAMEAU_AUTHORS);
    print "Importing RDF files <b>$BNF_RAMEAU_AUTHORS</b> into ARC2 local store <b>'".(implode(',',array_keys($OBJECTS)))."'</b> ...<br>$HREF_IMPORT_LOG_FILE<br>";
    break;

  case 'bnf_rameau_extrefs':
    $OBJECTS=array('bnf_rameau'=>$BNF_RAMEAU_EXTREFS);
    print "Importing RDF files <b>$BNF_RAMEAU_EXTREFS</b> into ARC2 local store <b>'".(implode(',',array_keys($OBJECTS)))."'</b> ...<br>$HREF_IMPORT_LOG_FILE<br>";
    break;

  case 'bnf_rameau_org':
    $OBJECTS=array('bnf_rameau'=>$BNF_RAMEAU_ORG);
    print "Importing RDF files <b>$BNF_RAMEAU_ORG</b> into ARC2 local store <b>'".(implode(',',array_keys($OBJECTS)))."'</b> ...<br>$HREF_IMPORT_LOG_FILE<br>";
    break;

  case 'bnf_rameau_works':
    $OBJECTS=array('bnf_rameau'=>$BNF_RAMEAU_WORKS);
    print "Importing RDF files <b>$BNF_RAMEAU_WORKS</b> into ARC2 local store <b>'".(implode(',',array_keys($OBJECTS)))."'</b> ...<br>$HREF_IMPORT_LOG_FILE<br>";
    break;
  */
  case 'zbw_stw':
    $OBJECTS=array('zbw_stw'=>$ZWB_STW_RDF_SOURCE);
    print "Importing RDF object <b>$ZWB_STW_RDF_SOURCE</b> into ARC2 local store <b>'".(implode(',',array_keys($OBJECTS)))."'</b> ..."
            ."<br>$COMMANDS<br>";
    break;
  
  case 'gesis_thezoz':
    $OBJECTS=array('gesis_thesoz'=>$GESIS_THESOZ_RDF_SOURCE);
    print "Importing RDF object <b>$GESIS_THESOZ_RDF_SOURCE</b> into ARC2 local store <b>'".(implode(',',array_keys($OBJECTS)))."'</b> ..."
            ."<br>$COMMANDS<br>";
    break;
  default:
    print "What? do not know any '$object' (nothing imported)<br><br>SYNOPSIS:<br>$SYNOPSIS";
}




if ($method=='todo')
{ 
  //Store in table under each obj name its filenames to load in a second time
  foreach ($OBJECTS as $storename=>$filedesc)
  {
    if ($storename)
    {
      //Open dir and take each file
      //
      // if $fromfilename is set, skip files until this one is found.
      if (is_dir($filedesc))
      {
        if ($dh = opendir($filedesc)) {
          $i=0;
          while (($file = readdir($dh)) !== false) 
          {
            if ($maxfiles>0 && $i>=$maxfiles) break;
            else
            if ($file<>'.' && $file<>'..')
            { 
              $i++;
              register_triples_file($storename,$filedesc.$file);
             
              // echo "<br>$i filename: $file : filetype: " . filetype($obj . $file) . "\n";
            }
          }
          closedir($dh);
        }
      }
      else
        register_triples_file($storename,$filedesc);

    } // $storename
  } // foreach

  //Print an info on loaded tasks:
  print_info($storename,"have been added to ARC on storename '<b>$storename</b>' (Please load them in a second phase):",
                        "<br>NO further open triple load tasks RESULTING for storename '<b>$storename</b>'");
} //todo

else if ($method=='load')
// *************************************
// Consider the foreseen ...
// *************************************
{
  foreach ($OBJECTS as $storename=>$_)
  {
    $LOCALCONFIG=$ARCCONFIG;
    $LOCALCONFIG{'store_name'}=$storename;
    $store = ARC2::getStore($LOCALCONFIG);
    if (!$store->isSetUp()) {
       $store->setUp();
    }

    //Print an info on loaded tasks:
    $LOAD_TASK = print_info($storename,"will now be loaded in ARC on storename '<b>$storename</b>':",
                                       "<br>NO further triple load tasks RESULTING for storename '<b>$storename</b>'");
    
    if (count($LOAD_TASK))
    foreach ($LOAD_TASK as $arr)
    {   
      list($storename,$filepath) = $arr;
      $statistics = load_triplefile_into_ARC_store($store,$storename,$filepath);
      
      if ($statistics)
        store_statistics_for_this_loaded_triple_file($statistics,$filepath,$storename);
    } // foreach
  } // foreach $OBJECTS
} // method=load
else 
if ($method=='info')
{

  foreach ($OBJECTS as $storename=>$_)
  {
    
    print_info($storename,"are foreseen for ARC on storename <b>$storename</b>:",
                          "<br>NO further triple load tasks RESULTING for storename '<b>$storename</b>'");
  }
} // info 
else if ($method=='delete')
{
  
  delete_each_triple_load_task($storename);
  
}
else if ($method=='done')
{
  foreach ($OBJECTS as $storename=>$_)
  {
    print_info($storename,"were executed by ARC on storename <b>$storename</b>:",
                          "<br>NO triple load task executions RESULTING for storename '<b>$storename</b>'",true);
  }  
}




?>