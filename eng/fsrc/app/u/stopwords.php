<?php
include_once("FRIdbUtilities.php");

$getStopwords = $_GET['getStopwords'];
$add_stopword = $_GET['add_stopword'];

$lang = $_GET['lang'];

if ($add_stopword)
	add_stopword($add_stopword, $lang);

if ($getStopwords) {
	$stopWords = get_stopwords();
	foreach ($stopWords as $stopWord)
		$output .= "<w>$stopWord</w>";
		
	$output = "<stopwords>$output</stopwords>";
	
	header("content-type: text/xml");
	print $output;	
}
	
/**
 * Cleans the stopwords from the array passed as parameter,
 * it returns the clean array.
 */
function cleanup_stopwords($words) {
	$stopWords = get_stopwords();
	
	foreach($words as $word) {
    if (!in_array($word, $stopWords))
			$cleaned_words[]=$word;
	}

	return $cleaned_words;
}


/**
 * Returns an array containing the stop words, preferably those that
 * are saved in the session, otherwise it will load them from a table
 * in the database.
 */
function get_stopwords() {
	$stopWords = array();
	
	if (isset($_SESSION['stopwords'])) {
		$stopWords = $_SESSION['stopwords'];
	} else {	
		$stopWords = get_stopwords_from_db();
		$_SESSION['stopwords'] = $stopWords;
	}
	
	return $stopWords;
}

/**
 * Loads stop words from the database.
 */
function get_stopwords_from_db() {
	$stopWords = array();
	
	try {
		$DB = new SRC_DB();
		$DBconn = $DB->DBconn;

		$query = "SELECT word FROM stopwords";
		$resultset = mysql_query($query, $DBconn);

		while ($row = mysql_fetch_array($resultset)) {
			$stopWords[] = strtolower($row["word"]);
		}
		
		$DB->close();
	} catch (Exception $e) {
		inform_bad_db($e);
	}
	
	return $stopWords;
}


function cleanup_stopwords_str($words_str)
{
	$arr= explode(" ",$words_str);
	$stop_cleaned_words_arr= cleanup_stopwords($arr);
	$stop_cleaned_words_str= trim(implode(' ',$stop_cleaned_words_arr));
  
  if ($stop_cleaned_words_str=='')
    $stop_cleaned_words_str=$words_str;
  
	return $stop_cleaned_words_str;
}


function add_stopword($stopword,$lang='?')
##################################################
{
	try {
		$DB = new SRC_DB();
		$DBconn=$DB->DBconn;

    $Q=<<<EOQ
SELECT count(*)
from stopwords
where word = '$stopword'
EOQ;
    $resultset = mysql_query($Q);
		if ($resultset) $word_exists = (mysql_result($resultset,0) > 0);

		if (!$word_exists)
		{
		
			$Q=<<<EOQ
	INSERT INTO stopwords 
				(`word`,`lang`)
				values 
				('$stopword','$lang')
EOQ;
	
			$resultset = mysql_query($Q);
			if (mysql_affected_rows()<1)
					throw(New Exception(mysql_error($DBconn)."<hr>Query:".$Q."<br><br>"));
		
			else print "<br>Stopword added in DB: $stopword/$lang";
			$DB->close();
		}
	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}
	
	return $STOPW;
}







function srv_training_remaining_stopwords($text,$lang)
##################################################
{
	//print "<br>srv_training_remaining_stopwords($text,$lang)";

	global $BASISURL;
	$words=explode(' ',$text);


	foreach($words as $word)
		$training_word.=
			" <a href=$BASISURL/app/u/stopwords.php?add_stopword=$word&lang=$lang title='add as stopword' target=blank>$word</a>";
	
	return $training_word;
}





?>
