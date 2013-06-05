<?php
/**
 * Script implementing a very simple AJAX responder for the AutoComplete plugin
 * used for the meta-search input field. It receives a single GET parameter: 'query'
 * and returns a JSON object.
 * 
 * The search for suggestions is done using the URI's saved in the 's2val' table
 * created by ARC when importing the local copy we made of the DBPedia ontology.
 */

 	include_once('../root.php');
	include_once("../../../$RODINSEGMENT/fsrc/app/sroot.php");
	include_once("../../../$RODINSEGMENT/app/u/arcUtilities.php");
	
	$suggestions = array();
	$USER_ID=$_GET['user_id'];
	$setversion=$_GET['setversion'];
	$DEBUG=0;

	$query = $_REQUEST['query'];
	
	$suggestions=$data=$descriptions=$properties=array();
	
	if (intval($setversion)<=2012)
	{
		if ($DEBUG) print " 1 setversion: $setversion";
		$suggestions = get_javiers_dbpedia_suggestions($query);
	}else if (intval($setversion)>2012)
	{
		if ($DEBUG) print " 2 setversion: $setversion";
		list($suggestions,$data,$descriptions,$properties) = get_rodin_src_suggestions($query,$USER_ID,$max_suggestions=30);
	}	
if ($DEBUG)
{
	foreach($suggestions as $o=>$sugg) print "\n<br>SUGGESTION: $o $sugg";
}

##########################################################################
header('Content-type: application/json');
##########################################################################
echo json_encode(array(	'query' => $query, 
												'suggestions' => $suggestions, 
												'data'=> ($data?$data:$suggestions), 
												'descriptions'=>($descriptions?$descriptions:null),
												'properties'=>$properties ));
##########################################################################


function get_javiers_dbpedia_suggestions($query)
{
	global $SRCDB_DBHOST, $SRCDB_USERNAME, $SRCDB_USERPASS, $LOCAL_DBPEDIA_DB_NAME;
	$mySqlQuery = "SELECT val FROM dbpedialocal_s2val WHERE val LIKE 'http://dbpedia.org/resource/" . str_replace(' ', '_', $query) . "%' LIMIT 0, 30";
	
	$suggestions = array();
	
	try {
		$conn = mysql_connect($SRCDB_DBHOST, $SRCDB_USERNAME, $SRCDB_USERPASS)
			or die("unable to connect to msql server ($SRCDB_DBHOST, $ARCDB_USERNAME, $ARCDB_USERPASS): " . mysql_error());
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
	return $suggestions;
} // javiers_dbpedia_suggestions




function cleanupDBPediaSyntax($label) {
	// find ',_' occurrences, meaning that label parts should be inversed
	if (stripos($label, ',_')) {
		$label = implode('_', array_reverse(explode(',_', $label)));
	}
	
	$label = str_replace('_', ' ', $label);
	
	return $label;
}
