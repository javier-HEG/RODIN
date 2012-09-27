<?php
/**
 * Script implementing a very simple AJAX responder for the AutoComplete plugin
 * used for the meta-search input field. It receives a single GET parameter: 'query'
 * and returns a JSON object.
 * 
 * The search for suggestions is done using the URI's saved in the 's2val' table
 * created by ARC when importing the local copy we made of the DBPedia ontology.
 */

include_once '../../fsrc/app/sroot.php';

$query = $_REQUEST['query'];

$mySqlQuery = "SELECT val FROM test_s2val WHERE val LIKE 'http://dbpedia.org/resource/" . str_replace(' ', '_', $query) . "%' LIMIT 0, 30";

$suggestions = array();

try {
	$conn = mysql_connect($SRCDB_DBHOST, $ARCDB_USERNAME, $ARCDB_USERPASS)
		or die("unable to connect to msql server: " . mysql_error());
	mysql_select_db($LOCAL_DBPEDIA_DB_NAME, $conn)
		or die("unable to select database 'db': " . mysql_error());
		
	$resultset = mysql_query($mySqlQuery, $conn);
	
	if (!$resultset) {
   		die("query failed: " . mysql_error());
	} else {
		while ($row = mysql_fetch_assoc($resultset)) {
			$suggestions[] = cleanupDBPediaSyntax(urldecode(substr($row['val'], 28)));
		}
	}
} catch (Exception $e) {}

header('Content-type: application/json');

echo json_encode(array('query' => $query, 'suggestions' => $suggestions));

function cleanupDBPediaSyntax($label) {
	// find ',_' occurrences, meaning that label parts should be inversed
	if (stripos($label, ',_')) {
		$label = implode('_', array_reverse(explode(',_', $label)));
	}
	
	$label = str_replace('_', ' ', $label);
	
	return $label;
}
