<?php
	include("../../../file.inc.php");

	if (is_file("maintenance_message.txt"))
	{
		$infile=new file("maintenance_message.txt");
		$message=$infile->read();
	}
	else
	{
		$message="Site under maintenance. Please try again later ...";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Maintenance</title>
<link rel="stylesheet" type="text/css" href="../../../../styles/main.css" />
</head>
<body style="background: #dddddd">
<div class="noportal">
<table width="100%">
	<tr>
	<td align="center" style="padding-top:150px">
		<div class="raised" style="width: 500px">
		<b class="b1"></b><b class="b2"></b><b class="b3"></b><b class="b4"></b>
			<div class="boxcontent" style='height: 200px;padding-top: 40px;text-align: center;font-size: 12pt;'>
			<?php echo $message;?>
			</div>
		<b class="b4b"></b><b class="b3b"></b><b class="b2b"></b><b class="b1b"></b>
		</div>
	</td>
	</tr>
</table>
</div>
</body>