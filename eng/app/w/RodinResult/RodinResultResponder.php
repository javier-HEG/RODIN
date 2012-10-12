<?php

require_once 'RodinResultManager.php';

$sid = $_POST['sid'];

$fromResult = isset($_POST['from']) ? $_POST['from'] : 0;

$jsonAllResults = array();

$allResults = RodinResultManager::getRodinResultsForASearch($sid);
$resultCount = count($allResults);

$resultMaxSetSize = 7;
$uptoResult = min($resultCount, $fromResult + $resultMaxSetSize);

for ($i = $fromResult; $i < $uptoResult; $i++) { 
	$result = $allResults[$i];
	$resultCounter = $i + 1;

	$resultIdentifier = 'aggregatedResult-' . $resultCounter;

	$jsonSingleResult = array();

	$jsonSingleResult['count'] = $resultCounter;
	$jsonSingleResult['url'] = $result->getUrlPage();
	$jsonSingleResult['resultIdentifier'] = $resultIdentifier;

	$jsonSingleResult['headerDiv'] = json_encode($result->headerDiv($resultIdentifier));
	$jsonSingleResult['contentDiv'] = json_encode($result->contentDiv($resultIdentifier));

	$jsonSingleResult['header'] = json_encode($result->htmlHeader($jsonSingleResult['resultIdentifier'], $resultCounter, $sid));
	$jsonSingleResult['minHeader'] = json_encode($resultCounter . '<br />');

	$jsonSingleResult['minContent'] = json_encode($result->toInWidgetHtml('min'));
	$jsonSingleResult['tokenContent'] = json_encode($result->toInWidgetHtml('token'));
	$jsonSingleResult['allContent'] = json_encode($result->toInWidgetHtml('all'));

	$jsonAllResults[] = $jsonSingleResult;
}

header('Content-type: application/json; charset=utf-8');
echo json_encode(array('sid' => $sid, 'count' => $resultCount, 'upto' => $uptoResult, 'results' => $jsonAllResults));

?>