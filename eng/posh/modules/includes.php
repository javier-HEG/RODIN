<?php
/*
 * Do the includes in the good order
 */

 if(get_magic_quotes_gpc()) {
        $_POST = array_map('stripslashes', $_POST);
        $_GET = array_map('stripslashes', $_GET);
        $_COOKIE = array_map('stripslashes', $_COOKIE);
}
require_once('../includes/config.inc.php');
require_once('../includes/connection_'.__DBTYPE.'.inc.php');
require_once('../includes/session.inc.php');
require_once('../db_layer/'.__DBTYPE.'/widget.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');

global $DB;
$DB = new connection(__SERVER,__LOGIN,__PASS,__DB);

?>