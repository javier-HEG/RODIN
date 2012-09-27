<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Netvibes modules </title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<?php
if (isset($_POST["moduleurl"]))
{
	echo '<script type="text/javascript">';
	echo 'parent.p_tutorial.expert.displayNetvibesSource("'.$_POST["moduleurl"].'");';
	echo '</script>';
}
else
{
?>
<form method="post">
To get a Netvibes widget :<br /><br />
- Select a widget in the Netvibes ecosystem : <a href="http://eco.netvibes.com" target="_blank">http://eco.netvibes.com</a><br />
- Click on "source" link.<br />
- Type the URL of the page displaying the widget<br />
<br />
<center><input type="text" name="moduleurl" style="width: 450px;" /> <input type="submit" value="GET" /></center>
</form>
<?php
}
?>
</body>
</html>