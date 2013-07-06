<?php

$filenamex="u/SOLRinterface/solr_interface.php";
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

require_once("$SOLR_INTERFACE_URI/solr_interface.php");

$id=$_REQUEST['id'];

foreach(explode(',',$id) as $base64ids)
{
  $doc=$doc?', ':'';
  $doc.=base64_decode($base64ids);
}

$lang= $_REQUEST['lang'];
$path=$_REQUEST['path']; // i.e. rodin_result
$title=$_REQUEST['title']; // i.e. skos-context

$host   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['host'];
$port   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['port'];
$core   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['core'];
$timeout=$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['timeout'];
$path="/solr/$path/";

if ($doc=='' || $path==''|| $id=='')
{
  header("content-type: text/xml");
  print "<add_solr_doc>"
		."<error>Wrong parameters provided: (id=$id) (path=$path) (doc=$doc)</error>"
		."</add_solr_doc>";
  
 exit;
}  


if (($client = solr_client_init($host,$port,$path,$core,$timeout)))
{
  //print "<hr>"; var_dump($client);
  // create a new document for the data
  $document = new Solarium_Document_ReadWrite();
  $document->id = $id;
  $document->title = $title;
  $document->lang = $lang;
  $document->body = $doc;
  $documents= array($document);
  $result = solr_synch_update('add_solr_doc',$path,$client, $documents);
  header("content-type: text/xml");
  print "<add_solr_doc>"
		."<result>".$result->getStatus()."</result>"
		."</add_solr_doc>";
}
?>
