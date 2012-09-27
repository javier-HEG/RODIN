<?php

include("../sroot.php");

$b=$_REQUEST['b'];

$base64decoded=(base64_decode($b));
$base64decoded_double = base64_decode($base64decoded);

print "<hr>input: <br><b>$b</b>";
print "<br><br>base64decoded: <br><b>$base64decoded</b>";

print "<br><br>base64decoded double: <br><b>$base64decoded_double</b>";

?>