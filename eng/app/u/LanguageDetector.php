<?php
include_once '../root.php';

header ("content-type: text/xml");

print '<detection>';

$text = $_REQUEST['text'];

print "<text>$text</text>";

$script = dirname($DOCROOT) . '/cgi-bin/cld.exe "' . $text . '"';
exec($script, $resp);

$lang = $resp[0];

print "<language>$lang</language>";

print '</detection>';

?>