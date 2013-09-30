<?php

require_once 'RodinResultManager.php';

$sid = $_REQUEST['sid'];
$USER = $_REQUEST['user'];

// The suffix is used to distinguish the hearder anc
// content divs from results in different tabs
$suffix = $_POST['suffix'];

$fromResult = isset($_REQUEST['from']) ? $_REQUEST['from'] : 0;
header('Content-type: application/json; charset=utf-8');
echo RodinResultManager::get_json_searchresults($sid, true, true);

?>