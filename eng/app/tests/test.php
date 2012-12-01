<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("../u/RodinResult/RodinResultManager.php");
require_once("../u/SOLRinterface/solr_init.php");
//    global $SOLR_RODIN_CONFIG;
//    global $SOLARIUMDIR;
//    global $USER;
    $resultNumber=0;
    #USE SOLR COLLECTION 'rodin_result':
    $solr_host   =$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['host'];
    $solr_port   =$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['port'];
    $solr_path   =$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['path'];
    $solr_core   =$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['core'];
    $solr_timeout=$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['timeout'];

print "SOLR_INTERFACE_URI: $SOLR_INTERFACE_URI";    
    
    
$sid='hshshshshshs';
$query='ma perche';
$USER=2;


 if (($client=solr_client_init($solr_host,$solr_port,$solr_path,$solr_core,$solr_timeout)))
    {

       $document = BasicRodinResult::toInsertSOLRsearch($client, $sid, $query, $USER);

        print "<hr>SAVING DOCUMENTS:<br>"; var_dump($document);
        $documents= array($document);
        solr_synch_update($sid,"search",$client, $documents);
    }

?>
