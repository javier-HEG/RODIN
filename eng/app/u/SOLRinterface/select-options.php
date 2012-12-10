<html><head><title>Select query with options</title></head><body>

<?php
require('../solarium/library/solarium/init.php');
Solarium_Autoloader::register();

// create a client instance
$client = new Solarium_Client();

// get a select query instance
$query = $client->createSelect();

// set start and rows param (comparable to SQL limit) using fluent interface
$query->setStart(1)->setRows(5);

// set fields to fetch (this overrides the default setting 'all fields')
$query->setFields(array('id','name','features','price'));

// set a query string (without a field, so the default search field will be used)
$query->setQuery('memory');

// create a filterquery and set options using the fluent interface
//$fq = $query->createFilterQuery('maxprice')->addTag('maxprice')->setQuery('price:[1 TO 400]');

// add a facet on a field
$facetSet = $query->getFacetSet();
$facet = $facetSet->createFacetField('stock')->setField('inStock')->addExclude('maxprice');

// enable the highlighting component
$hl = $query->getHighlighting();
$hl->setFields('features');
$hl->setSimplePrefix('<b>');
$hl->setSimplePostfix('</b>');

// this executes the query with default settings and returns the result
$resultset = $client->select($query);
$highlighting = $resultset->getHighlighting();

// display the total number of documents found by solr
echo 'NumFound: '.$resultset->getNumFound();

// display facet counts
echo '<hr/>Facet counts for field "inStock":<br/>';
$facet = $resultset->getFacetSet()->getFacet('stock');
foreach($facet as $value => $count) {
    echo $value . ' [' . $count . ']<br/>';
}

// show documents using the resultset iterator
foreach ($resultset as $document) {

    echo '<hr/><table>';

    // the documents are also iterable, to get all fields
    foreach($document AS $field => $value)
    {
        // this converts multivalue fields to a comma-separated string
        if(is_array($value)) $value = implode(', ', $value);

        echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
    }

    echo '</table><br/><b>Highlighting results:</b><br/>';

    // highlighting results can be fetched by document id (the field defined as uniquekey in this schema)
    $highlightedDoc = $highlighting->getResult($document->id);
    if($highlightedDoc){
        foreach($highlightedDoc as $field => $highlight) {
            echo implode(' (...) ', $highlight) . '<br/>';
        }
    }
}
?>
</body></html>