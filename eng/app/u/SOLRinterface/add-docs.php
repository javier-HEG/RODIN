<html><head><title>Add docs</title></head><body>

<?php

$filenamex="u/SOLRinterface/solr_init.php";
#######################################
$max=10;
//print "<br>FRIutilities: try to require $filenamex at cwd=".getcwd()."<br>";
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{
	//print "<br>try to require $updir$filenamex";
	if (file_exists("$updir$filenamex"))
	{
		//print "<br>REQUIRE $updir$filenamex";
		require_once("$updir$filenamex"); break;
	}
}

print "wanr to require "."$SOLR_INTERFACE_URI/solr_init.php". " from ".getcwd();



require_once("$SOLR_INTERFACE_URI/solr_init.php");

$host   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['host'];
$port   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['port'];
$path   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['path'];
$core   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['core'];
$timeout=$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['timeout'];

$sid=1111111;

if (($client = solr_client_init($host,$port,$path,$core,$timeout)))
{
  // create a new document for the data
  $doc1 = new Solarium_Document_ReadWrite();
  $doc1->id = 123;
  $doc1->name = 'testdoc-1';
  $doc1->price = 364;
  $doc1->body = $doc1->id.' '.$doc1->name.' '.$doc1->price;

  // and a second one
  $doc2 = new Solarium_Document_ReadWrite();
  $doc2->id = 124;
  $doc2->name = 'testdoc-2';
  $doc2->price = 340;
  $doc2->body = $doc2->id.' '.$doc2->name.' '.$doc2->price;

  $documents= array($doc1,$doc2);
  $result = solr_synch_update($sid,"search",$client, $documents);
  
  
  echo '<b>Update query executed<b><br/>';
  echo 'Query status: ' . $result->getStatus(). '<br/>';
  echo 'Query time: ' . $result->getQueryTime();
}
?>
</body></html>