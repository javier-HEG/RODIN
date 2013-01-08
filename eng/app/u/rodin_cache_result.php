<?php
/**
 * Web Script called by javascript.makeRequest()
 * update SOLR using content under cacheid
 */

include_once 'FRIutilities.php';

$cacheid = base64_decode($_REQUEST['cacheid']); 
$content = $_REQUEST['content']; 
$USER = $_REQUEST['user']; 

if (trim($content)<>'')
    cache_response($cacheid, $content);

header('Content-Type: text/html; charset=utf-8');
print 1;

?>