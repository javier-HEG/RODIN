
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>RODIN TAG CLOUD </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="language" content="<?php echo __LANG;?>" />
<link rel="stylesheet" type="text/css" href="../styles/main.css?v=<?php echo __POSHVERSION;?>" />
<!--FRI-->
<script type='text/javascript' src='../u/querystring.js'></script>
<script type='text/javascript' src='../u/RODINutilities.js.php'></script>
<!--FRI-->
</head>
<body>
<?php
		include_once('../u/tagcloud_styles.php');
 	  include_once("../u/tagcloud.php");
	  include_once("../u/FRIdbUtilities.php");

		print $TAGCLOUDSTYLE;	
		$tags = collect_queries_tag();

    $cloud = new wordCloud($tags);
    print $cloud->showCloud('href');


?>

		</body>
		</html>
		