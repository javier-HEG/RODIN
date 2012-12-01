<?php

require_once 'RodinResultManager.php';

$sid = $_POST['sid'];

//$allResults = RodinResultManager::getRodinResultsForASearch($sid);
//$allResults = RodinResultManager::getRodinResultsFromSOLRForASearch($sid);

$jsonAllResults = array();

$resultCounter = 0;
foreach ($allResults as $result) {
	$resultCounter++;
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

header('Content-type: application/json');
echo json_encode($jsonAllResults);

?>