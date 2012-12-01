<html><head><title>Delete docs</title></head><body>

<?php
require('../solarium/library/solarium/init.php');
Solarium_Autoloader::register();

// create a client instance
$client = new Solarium_Client();

// create a new document for the data
$doc1 = new Solarium_Document_ReadWrite();
$doc1->id = 123;
$doc1->name = 'testdoc-1';
$doc1->price = 364;

// and a second one
$doc2 = new Solarium_Document_ReadWrite();
$doc2->id = 124;
$doc2->name = 'testdoc-2';
$doc2->price = 340;

// get an update query instance
$update = $client->createUpdate();

// add the delete query and a commit command to the update query
$update->addDeleteQuery('name:testdoc*');
$update->addCommit();

// this executes the query and returns the result
$result = $client->update($update);

echo '<b>Update query executed<b><br/>';
echo 'Query status: ' . $result->getStatus(). '<br/>';
echo 'Query time: ' . $result->getQueryTime();

?>
</body></html>