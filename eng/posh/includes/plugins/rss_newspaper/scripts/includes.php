<?php
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
require_once('../../../config.inc.php');
require_once('../../../connection_'.__DBTYPE.'.inc.php');
require_once('../../../session.inc.php');
require_once('../../../xml.inc.php');

global $DB;
$DB = new connection(__SERVER,__LOGIN,__PASS,__DB);

?>