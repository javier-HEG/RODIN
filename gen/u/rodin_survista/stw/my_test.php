<pre><?php
include_once('../survista/rodinLoader.inc.php');
include_once('config.inc.php');
include_once(SURVISTA_PATH . 'test.inc.php');

function isType($string) {
	return !stripos($string, 'type');
}

function printRows($rows = array()) {
	if (count($rows) > 0) {
		$rowNames = array_filter(array_keys($rows[0]), "isType");
		
		echo '<table border=1><tr>';
		foreach ($rowNames as $singleRow) {
			echo "<th>$singleRow</th>";
		}
		
		foreach ($rows as $row) {
			echo '</tr><tr>';
			foreach ($rowNames as $singleRow) {
				echo '<td>' . htmlentities($row[$singleRow]) . '</td>';
			}
		}
		
		echo '</tr></table>';
	} else {
		echo '<p><i>No rows to print.</i></p>';
	}
}

function printSparql($query) {
	print '<p><u>Query:</u>' . htmlentities($query) . '</p>';
}

//var_dump($config);

$sparql = "SELECT * WHERE { GRAPH ?g { ?s ?p ?o . } } LIMIT 10";
//printRows($store->sparql($sparql));

$sparql = "
SELECT ?label WHERE {
	<http://zbw.eu/stw/descriptor/10156-5> <http://www.w3.org/2004/02/skos/core#prefLabel> ?label .
	FILTER (lang(?label) = \"en\")
} LIMIT 50";
printSparql($sparql);
printRows($store->sparql($sparql));

$sparql = "
SELECT ?label WHERE {
	<http://zbw.eu/stw/descriptor/10156-5> <http://www.w3.org/2004/02/skos/core#altLabel> ?label .
	FILTER (lang(?label) = \"en\")
} LIMIT 50";
printSparql($sparql);
printRows($store->sparql($sparql));

$sparql = "
SELECT ?label WHERE {
	{
		{ <http://zbw.eu/stw/descriptor/10156-5> <http://www.w3.org/2004/02/skos/core#prefLabel> ?label . }
		UNION
		{ <http://zbw.eu/stw/descriptor/10156-5> <http://www.w3.org/2004/02/skos/core#altLabel> ?label . }
	}
	FILTER (lang(?label) = \"en\")
} LIMIT 50";
printSparql($sparql);
printRows($store->sparql($sparql));

?></pre>