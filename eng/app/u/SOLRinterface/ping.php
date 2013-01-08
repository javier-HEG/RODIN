<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
htmlHeader();

$filenamex="app/root.php";
#######################################
$max=10;
//print "<br>FRIutilities: try to require $filenamex at cwd=".getcwd()."<br>";
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{
	//print "<br>try to require $updir$filenamex";
	if (file_exists("$updir$filenamex"))
	{
		//print "<br>REQUIRE $updir$filenamex";
		require_once("$updir$filenamex"); break;
	}
}


require_once("$SOLR_INTERFACE_URI/solr_interface.php");


print "<br>SOLARIUMURL: <a href='$SOLARIUMURL' target='_blank'>$SOLARIUMURL</a>";
print "<br>SOLARIUMDIR: $SOLARIUMDIR";


// check solarium version available
echo '<br><br>Solarium library version: ' . Solarium_Version::VERSION . ' - ';

$host   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['host'];
$port   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['port'];
$path   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['path'];
$core   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['core'];
$timeout=$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['timeout'];

//$URL="http://$host:$port$path";
//print "CONFIG: <a href='$URL' target='_blank'>$URL</a> <br>";
//var_dump($config);
//print "<br>";


if (solr_client_init($host,$port,$path,$core,$timeout))
{
  // create a client instance
  $client = new Solarium_Client();

  // create a ping query
  $ping = $client->createPing();

  // execute the ping query
  try{
      $client->ping($ping);
      echo 'Ping query <b>succesful</b>';
  }catch(Solarium_Exception $e){
      echo "Ping query <b>failed</b> (".str_replace("\n",'<br>',$e).")";

  }
}

htmlFooter();

##########################






function htmlHeader(){
  $FILEPATH=__FILE__;
  $path_parts=pathinfo($FILEPATH);
	$filename=$path_parts['filename'];
    echo '<html><head><title>'.$filename.'</title></head><body>';
}

function htmlFooter(){
    echo '</body></html>';
}


?>
