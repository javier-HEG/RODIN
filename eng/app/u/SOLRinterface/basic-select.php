<html><head><title>Basic select</title></head><body>

<?php
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
$host   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['host'];
$port   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['port'];
$path   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['path'];
$core   =$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['core'];
$timeout=$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['timeout'];



if (solr_client_init($host,$port,$path,$core,$timeout))
{

  // create a client instance
  $client = new Solarium_Client();

  // get a select query instance
  $query = $client->createSelect();

  // override the default row limit of 10 by setting rows to 30
  $query->setRows(50);

  // this executes the query with default settings and returns the result
  $resultset = $client->select($query);

  // display the total number of documents found by solr
  echo 'NumFound: '.$resultset->getNumFound();

  // show documents using the resultset iterator
  foreach ($resultset as $document) {

      echo '<hr/><table>';

      // the documents are also iterable, to get all fields
      foreach($document AS $field => $value)
      {
          // this converts multivalue fields to a comma-separated string
          if(is_array($value)) $value = implode(', ', $value);

          echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
      }

      echo '</table>';
  }

}
?>
</body></html>