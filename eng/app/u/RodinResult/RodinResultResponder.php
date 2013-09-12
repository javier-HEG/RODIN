<?php

require_once 'RodinResultManager.php';

$sid = $_POST['sid'];
$USER = $_POST['user'];

// The suffix is used to distinguish the hearder anc
// content divs from results in different tabs
$suffix = $_POST['suffix'];

$fromResult = isset($_POST['from']) ? $_POST['from'] : 0;
header('Content-type: application/json; charset=utf-8');
echo RodinResultManager::get_json_searchresults($sid, true, true);

?>