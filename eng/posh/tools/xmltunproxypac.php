<?php
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
$tp=$_GET["ptyp"];
$t=$_SERVER['QUERY_STRING'];
$t=substr($t,strpos($t,"url")+4);
$t=substr($t,0,strpos($t,"&proxy"));

if ($tp=="xml") header("Content-type: text/xml; charset=UTF-8");
if ($t=="") exit();

$x = '';

$proxy=$_GET["proxy"];
$pos=strpos($proxy,":");
$proxyserver=substr($proxy,0,$pos);
$proxyport=substr($proxy,$pos+1);

$proxy_fp = fsockopen($proxyserver, $proxyport);
if (!$proxy_fp)    {return false;}
fputs($proxy_fp, "GET $t HTTP/1.0\r\n");
fputs($proxy_fp, "Proxy-Authorization: Basic ".__PROXYCONNECTION."\r\n\r\n");
//while(!feof($proxy_fp)) {$x .= fread($proxy_fp,4092);}
while(!feof($proxy_fp)) {$x .= fgets($proxy_fp,4092);}
fclose($proxy_fp);
//suppress http header
$x = substr($x, strpos($x,"\r\n\r\n")+4);
echo $x;
?>