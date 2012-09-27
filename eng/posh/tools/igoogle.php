<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>iGoogle modules </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php
if (isset($_POST["moduleurl"]))
{
	if (preg_match("/url\=([^ ]+)/i", $_POST["moduleurl"], $parts)){
		$url=urldecode($parts[1]);
		if (strpos($url,"http")===false) $url="http://".$url;
		if (strpos($url,"http")!=0) $url="http://".$url;
		require_once('../includes/http.inc.php');

		echo '<script type="text/javascript">';
		echo 'parent.window.location=parent.$p.url.simpleUrl(parent.window.location.href)+"?url="+parent.$p.string.esc("'.$url.'")';
		echo '</script>';
	} else echo "<b>error [no moduleurl in URL provided] ! Please try again</b><br />";
}
else
{
?>
<form method="post">
To get a iGoogle gadget :<br /><br />
- Select the gadget in the iGoogle directory : <a href="http://www.google.com/ig/directory" target="_blank">http://www.google.com/ig/directory</a><br />
- Once selected, copy the URL of the page containing the detail of the gadget, and past it below. Then click on GET button<br />
<input type="text" name="moduleurl" style="width: 450px;" /> <input type="submit" value="GET" />
</form>
<?php
}
?>
</body>
</html>