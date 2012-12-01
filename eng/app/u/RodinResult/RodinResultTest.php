<?php

require_once 'RodinResultManager.php';

switch ($_REQUEST['action']) {
	case 'load':
		echo "<h1>RodinResult OO Implementation Test (Load Action)</h1>";
		echo '<style>div.test{ border: 1px solid lightgray; padding: 12px; width: 800px; margin-left: auto; margin-right: auto;}</style>';
		
		$sid = 'TestOnlySID';
		$datasource = 'ARXIVichango';
		$someResults = RodinResultManager::getRodinResultsFromResultsTable($sid, $datasource);
		
		echo '<h2>First result as simple HTML</h2>';
		echo '<div class="test">';
		echo $someResults[0]->toBasicHtml();
		echo '</div>';
		
		echo '<h2>First result as pure text</h2>';
		echo '<div class="test">';
		echo $someResults[0];
		echo '</div>';
	break;
	
	case 'save':
		echo "<h1>RodinResult OO Implementation Test (Save Action)</h1>";
		echo '<style>div.test{ border: 1px solid lightgray; padding: 12px; width: 800px; margin-left: auto; margin-right: auto;}</style>';
		
		$someResults = createNTestResults(15);
		
		echo '<h2>All results MySQL insert</h2>';
		echo '<div class="test" style="font-family: monospace;">';
		echo RodinResultManager::saveRodinResultsInResultsTableMySQLCode($someResults, '20120322.170920.829.2', '/rodin/eng/app/w/RDW_Arxiv.rodin');
		echo '</div>';
		
		echo '<h2>Call to MySQL insert returned</h2>';
		echo '<div class="test" style="font-family: monospace;">';
		echo RodinResultManager::saveRodinResultsInResultsTable($someResults, '20120322.170920.829.2', '/rodin/eng/app/w/RDW_Arxiv.rodin');
		echo '</div>';
	break;
		
	default:
		echo "<h1>RodinResult OO Implementation Test (Default Action)</h1>";
		echo '<style>div.test{ border: 1px solid lightgray; padding: 12px; width: 800px; margin-left: auto; margin-right: auto;}</style>';
		
		$someResults = createNTestResults(15);
		
		echo '<h2>First result as simple HTML</h2>';
		echo '<div class="test">';
		echo $someResults[0]->toBasicHtml();
		echo '</div>';
		
		echo '<h2>First result as pure text</h2>';
		echo '<div class="test">';
		echo $someResults[0];
		echo '</div>';
		
		echo '<h2>All results MySQL insert</h2>';
		echo '<div class="test" style="font-family: monospace;">';
		echo RodinResultManager::saveRodinResultsInResultsTableMySQLCode($someResults, '20120322.170920.829.2', '/rodin/eng/app/w/RDW_Arxiv.rodin');
		echo '</div>';
		
	break;
}

function createNTestResults($n) {
	$allResults = array();
	
	for ($i = 0; $i < $n; $i++) {
		$articleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_ARTICLE);
		
		$articleResult->setTitle("Article number $i");
		$articleResult->setUrlPage('http://arxiv.org/abs/0812.0438v1');
		$articleResult->setAuthors('Sabu M. Thampi');
		$articleResult->setDate('2 Dec 2008');
		
		$articleResult->setProperty('abstract', 'Knowledge has been lately recognized as one of the most important assets of organizations. Managing knowledge has grown to be imperative for the success of a company. This paper presents an overview of Knowledge Management and various aspects of secure knowledge management. A case study of knowledge management activities at Tata Steel is also discussed.');
		$articleResult->setProperty('full-text', '');
		$articleResult->setProperty('keywords', 'Databases, Cryptography and Security');
		$articleResult->setProperty('review', '');
		
		$allResults[] = $articleResult;
	}
	
	return $allResults;
}
