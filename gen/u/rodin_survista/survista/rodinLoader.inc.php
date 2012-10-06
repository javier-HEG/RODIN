<?php
// Get segment
if (isset($_GET['rodinsegment'])) {
	$rodinsegment = $_GET['rodinsegment'];
} else {
	$rodinsegment = 'eng';
}

// Get query language
if (isset($_GET['l'])) {
	$lang = $_GET['l'];
} else {
	$lang = 'en';
}

// Get application language
if (isset($_GET['l10n'])) {
	$l10n = $_GET['l10n'];
} else {
	$l10n = 'en';
}

// Get language labels
$max=10;
$filenamex = $rodinsegment . '/app/exposh/l10n/' . $l10n . '/lang.php';
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../") { 
	if (file_exists("$updir$filenamex")) {
//		print "<p>Found $updir$filenamex, including!</p>";
		require_once("$updir$filenamex");
		break;
	}
}
?>