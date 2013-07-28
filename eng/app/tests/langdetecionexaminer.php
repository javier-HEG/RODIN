<?php
require_once("../u/FRIdbUtilities.php");
require_once("../u/FRIutilities.php");
require_once("../u/RodinResult/RodinResultManager.php");

$sid=$_GET['sid'];
$what=$_GET['what']; // good, strange, empty
if (!$what) $what='good';
else $what=strtolower($what);
$SEG=$RODINSEGMENT;
	
$allpercent=0; // cumulated percent value

$TITLEPAGE="LANGUAGE DETECTION EXAMINER";

?>
<html>
	<head>
		<title><?php print $TITLEPAGE; ?></title>
		<link rel="stylesheet" type="text/css" href="../css/rodin.css.php?" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script type='text/javascript' src='../u/RODINutilities.js.php?skin=<?php print $RODINSKIN;?>'></script>
	</head>	
	<body bgcolor='<?php print $COLOR_PAGE_BACKGROUND;?>' >

<?php

if($sid)
{
$SQL=<<<EOS
SELECT msg FROM `logger` 
WHERE sid='$sid'
AND msg like "%detectLanguageAndLog%exit%" 
EOS;
}
else
{
$SQL=<<<EOS
SELECT msg FROM `logger` 
WHERE msg like "%detectLanguageAndLog%exit%" 
EOS;
}
	if ($sid)
		$CONSTRAINTTOSIDTEXT="<br>concerning one single search (sid=$sid)";
	print<<<EOP
	<h3> LANGUAGE DETECTOR EXAMINER for '$what' responces $CONSTRAINTTOSIDTEXT</h3>
EOP;

	try {
		$DB = new RODIN_DB('rodin');
		$resultset = mysqli_query($DB->DBconn,$SQL);
		$DB->close();

		if ($resultset)
		while ($row = mysqli_fetch_assoc($resultset))
		{
			$dorecord=false;
			if (preg_match("/exit\((.*)\)\Wwas\((.*),(.*)\)/",$row['msg'],$match))
			{
				$responce=$match[1];
				$text=$match[2];
				$who=$match[3];

				if ($responce=='un')
				{
					$strange[] = array($text,$responce,$who);
				}
				else if ($responce=='')
				{
					$bad[] = array($text,$responce,$who);
				}
				else if ($responce<>'' && $responce <>'un')
				{
					$good[] = array($text,$responce,$who);
				}	
			}
		}

		}
	catch (Exception $e)
		{
			inform_bad_db($e);
		}

	$count_good=count($good);
	$count_strange=count($strange);
	$count_bad=count($bad);
	
	if ($count_good) foreach($good as $R) {
		list($text,$responce,$who) = $R;
		$GOOD_ONES.=$GOOD_ONES?'<br>':'';
		$GOOD_ONES.="<span title='called from $who'>($text)=($responce)</span>";
	}
	if ($count_strange) foreach($strange as $R) {
		list($text,$responce,$who) = $R;
		$STRANGE_ONES.=$STRANGE_ONES?'<br>':'';
		$STRANGE_ONES.="<span title='called from $who'>($text)=($responce)</span>";
	}
	if ($count_bad) foreach($bad as $R) {
		list($text,$responce,$who) = $R;
		$BAD_ONES.=$BAD_ONES?'<br>':'';
		$BAD_ONES.="<span title='called from $who'>($text)=($responce)</span>";
	}


print <<<EOP
<table cellspacing=0 cellpadding=0 border=1>
<tr> <th align=center> good </th><th align=center> strange </th><th align=center> bad </th> </tr> 
<tr><td valign=top>$GOOD_ONES</td><td valign=top>$STRANGE_ONES</td><td valign=top>$BAD_ONES</td></tr>
</table>
EOP;
?>
</body>
</html>
