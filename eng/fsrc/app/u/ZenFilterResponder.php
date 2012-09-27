<?php

include_once 'ZenFilter.php';

$filter = new ZenFilter(base64_decode($_REQUEST['textToFilter']),base64_decode($_REQUEST['query']),$_REQUEST['lang']);

$processStartTime = time();

$filteredTokens = $filter->getFilteredText();
$lastMethodUsed = $filter->getLastMethodUsed();

$totalTime = time() - $processStartTime;

$textToPrint = "<filtered time=\"$totalTime\" lastMethodUsed=\"$lastMethodUsed\">\n";

foreach ($filteredTokens as $token => $count) {
	$textToPrint .= "\t<term count=\"$count\">$token</term>\n";
}

$textToPrint .= '</filtered>';

header ("content-type: text/xml");
print $textToPrint;

?>