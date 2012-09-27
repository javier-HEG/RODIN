<?php
	  include_once("FRIdbUtilities.php");
	  //print "RODINSKIN: $RODINSKIN";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>RODIN TAG CLOUD </title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="language" content="<?php echo __LANG;?>" />
	<link rel="stylesheet" type="text/css" href="../css/tagcloud_connected.css" />
	
	<script type='text/javascript' src='querystring.js'></script>
	<script type='text/javascript' src='RODINutilities.js.php?skin=<?php print $RODINSKIN; ?>'></script>
</head>
<body>
<?php
	//include_once('tagcloud_styles.php');
	include_once("tagcloud.php");

	//print $TAGCLOUDSTYLECONNECTED;	
	$USER=$_REQUEST['pid']; 
	$ERASE=$_REQUEST['erase']; 
	
	//print "<br>USer=$USER";
	//print "<br>ERASE=$ERASE";
	
	if ($ERASE)
	{
		//print "erasing...";
		eraseTagCloud($USER);
		$WORDS='';
	}
	else
	{
		$tags = collect_queries_tag($USER);
	
	    $cloud = new wordCloud($tags);
	    $WORDS= $cloud->showCloud('href_connected');
	}
	
    print $WORDS;
    
?>

</body>
</html>
		