<?php

//Widget Precalculations for every uc3 widget
//Should use UC3 resources instead of RODIN RESOURCES
//Calculate $RODINSEGMENT

$RODINSEGMENT=$_REQUEST['seg'];
if (!$RODINSEGMENT)
{
	$PATTERNRODINSEGMENT="/\/(eng|xxl|x|p|st|x)\//";
	if (preg_match($PATTERNRODINSEGMENT,$_SERVER['SCRIPT_NAME'],$match))
	{$RODINSEGMENT=$match[1];} else {print "root.php: Could not load RODINSEGMENT"; exit;}
}
if (!$RODINSEGMENT) {
	if ($DEBUG) print $WIDGET.': NO RODINSEGMENT AVAILABLE (EXIT)'; // DEBUG
	exit;
}

?>