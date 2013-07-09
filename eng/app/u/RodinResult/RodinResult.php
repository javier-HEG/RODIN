<?php

/**
 * An basic implementation of results, no particular
 * properties expected.
 *
 * @author Javier Belmonte
 * @author Fabio Ricci (modifications) fabio.ricci@ggaweb.ch
 *  */
class BasicRodinResult {
	private $wdsctimestamp; // timestamp of the result information (cached)
	private $urlPage;
	private $title;
	private $score; //SOLR score on morelikethis queries
	private $rank;  //RDF rank for this result
	private $lod;  //states result is taken from an LOD (external) source
	private $authors;
	private $date;
	private $is_rdfized; //triples from this result were generated and inserted into store
	private $subjects; //subjects calculated/added to this object
	public  $RDFenhancement; //USED?
	protected $id; // SOLR ID or DB RECORD ID
	protected $sid;
	
	private $resultProperties;
	private $validProperties;
	
	private $resultType;

	public function __construct($resultType = RodinResultManager::RESULT_TYPE_BASIC) {
		$this->resultProperties = array();
		$this->validProperties = array();
		$this->resultType = $resultType;
		$this->is_rdfized=false;
		$this->RDFenhancement = null;
	}
	
	/**
	 * Affects an array to the valid properties variable. If the
	 * actual parameter is not an array, an array will be created
	 * with the actual parameter as only element.
	 * 
	 * @param $properties
	 */
	public function setValidProperties($properties) {
		if (is_array($properties)) {
			$this->validProperties = $properties;
		} else {
			$this->validProperties = array($properties);
		}
	}
	
	public function getValidProperties() {
		return $this->validProperties;
	}
	
	/**
	 * Produces a very simple HTML representation of the result.
	 */
	public function toBasicHtml() {
		$html = '<p>' . ucfirst(RodinResultManager::getRodinResultTypeName($this->resultType)) . '</p>';
		$html .= "<h1>".$this->getTitle()."</h1>";
		$html .= "<p><b>Author(s) : </b> ".$this->getAuthors()."<br />";
		$html .= "<b>Date : </b> ".$this->getDate()."<br />";
		$html .= "<b>URL : </b> <a href=\"".$this->getUrlPage()."\">{$this->urlPage}</a></p>";

		return $html;
	}
	
	public function toInOwnTabHtml() {
		return $this->toInWidgetHtml('all');
	}
	
	/**
	 * Produces the in-Widget represetation of the result
	 */
	public function toInWidgetHtml($textZoom = 'token') 
	{
		if ($this->getLod()) 
		{
			$CLASS='oo-result-lod';
			$TITLE="title='Document taken from an external resource'";
		}
		else {
			$CLASS='oo-result';
		}
		
		$html = '<div class="'.$CLASS.'"'.$TITLE.'>';
		
		switch ($textZoom) {
			case 'min':
				$html .= '<p>"' . $this->separateWordsInSpans($this->getTitle()) . '" by ' . $this->separateWordsInSpans($this->authors) . '</p>';
			break;
			
			default:
				$html .= '<h1>' . $this->separateWordsInSpans($this->getTitle()) . '</h1>';
  			$html .= $this->valueAsHtmlParagraphNumber('Score:', $this->getScore());
  			$html .= $this->valueAsHtmlParagraph('By', $this->getAuthors(), true);
				$html .= $this->valueAsHtmlParagraph('Publication date:', $this->getDate(), false);
			break;
		}

		$html .= '</div>';
		
		return $html;
	}
	
  
  
  /**
	* Checks that value is not an empty string and return a paragraph HTML element
	* containing the value, preceeded by the value name given
	*/
	protected function valueAsHtmlParagraphNumber($valueName, $value) {
    if ($value<>null) 
    {
			$html = '<p><u>' . $valueName . '</u>&nbsp;'. ($value) . '</p>';
		}

    return $html;
	}
  
  
	/**
	* Checks that value is not an empty string and return a paragraph HTML element
	* containing the value, preceeded by the value name given
	*/
	protected function valueAsHtmlParagraph($valueName, $value, $putWordsInSpan) {
		$html = '';
		
		if ($value != '') {
			if (strrpos($valueName, ':') === false) {
				$html .= '<p><u>' . $valueName . '</u>&nbsp;';
			} else {
				$html .= '<p><u>' . substr($valueName, 0, strrpos($valueName, ':')) . '</u>' . substr($valueName, strrpos($valueName, ':')) . '&nbsp;';
			}

			if ($putWordsInSpan) {
				$html .= $this->separateWordsInSpans($value) . '</p>';
			} else {
				$html .= $value . '</p>';
			}
		}
		
		return $html;
	}


	/*
	 * Separate each word by introducing a span and a hovering/clicking behaviour
	 * JB + FRI 
	 */
	protected function separateWordsInSpans($text) 
	{
		//print "<br><b>enter.separateWordsInSpans</b>($text) ";
		//Filter bad chars as nl or tabs
		$pattern = '/[\n\t]/';
		$text = (trim(preg_replace($pattern, '', $text)));
		
		$title = lg("lblActionsOnWord");
		$language_specialchars='ßÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
		
		$pattern = '/[A-Za-z0-9'.$language_specialchars.'\-_]+/u';
		
		$replace = '<span class="result-word" title="' . $title . '"'
		." onmouseover=\"phf('$0')\" "
		." onmouseout=\"puh('$0')\" "
		." onclick=\"prr('$0')\" "
		.'>$0</span>';

		$result= preg_replace($pattern, $replace, $text);
		//print "<br><b>exit.separateWordsInSpans</b>($text):<br>(((".htmlentities($result).')))';
		return $result;
	}




	
	/**
	 * Produces MySQL code that has to be added to the one inserting the number
	 * of results at xPointer -1.
	 * 
	 * @param $sid
	 * @param $datasource
	 * @param $resultNumber
	 * @param $conn
	 */
	public function toInsertSqlCommand($sid, $datasource, $resultNumber, $conn) {
		$sql .= ", ('$sid','$datasource','$resultNumber.0','','','type','int','" . mysql_real_escape_string($this->getResultType(), $conn) . "', '','')";
		$sql .= ", ('$sid','$datasource','$resultNumber.1','','','title','string','" . mysql_real_escape_string($this->getTitle(), $conn) . "', '','')";
		$sql .= ", ('$sid','$datasource','$resultNumber.2','','','authors','string','" .mysql_real_escape_string($this->getAuthors(), $conn) . "', '','')";
		$sql .= ", ('$sid','$datasource','$resultNumber.3','','','date','string','" . mysql_real_escape_string($this->getDate(), $conn) . "', '','')";
		$sql .= ", ('$sid','$datasource','$resultNumber.4','','','urlPage','string','" . mysql_real_escape_string($this->getUrlPage(), $conn) . "', '','')";
		
		$pointer = 5;
		foreach ($this->resultProperties as $propertyName=>$propertyValue) {
			if ($propertyValue != '') {
				$sql .= ", ('$sid','$datasource','$resultNumber.$pointer','','','$propertyName','string','" . mysql_real_escape_string($propertyValue, $conn) . "', '','')";
				$pointer += 1;
			}
		}
		
		return $sql;
	}






/**
	 * Produces SOLR documents that has to be added in SOLR
	 * Method built following toInsertSqlCommand()
	 *
	 * @param $client - an instance of solarium
	 * @param $sid    - the search id
	 * @param $query - the user query
	 */
	public static function toInsertSOLRsearch(&$client, $sid, $query, $seg, $user) {

    //print "<br>toInsertSOLRdocument ($datasource)<br>";

    // create a new document for the data
    $result_doc = new Solarium_Document_ReadWrite();

    $result_doc->sid        = $sid;
    $result_doc->id         = $result_doc->sid.'-'.uniqid(); //SOLR ID unique!!!
    $result_doc->query      = $query;
    $result_doc->user       = $user;
    $result_doc->seg        = $seg;

    //Add specific attr/value fields:


    #Put everything together in order to find per search the value: TBD
    $result_doc->body       = $query;

		return $result_doc;
	}



/**
	 * Produces a document that can be added in SOLR
	 * Method built following toInsertSqlCommand()
	 *
	 * @param $client - an instance of solarium
	 * @param $id - the id of the result (in case result was already SOLR stored, and now here only updated)
	 * @param $sid
 	 * @param $datasource
	 * @param $resultNumber
 	 * @param $seg
 	 * @param $user
 	 * @param $dscachetimestamp
 *  * 
	 */
	public function toInsertSOLRdocument(&$client, $id='', $sid, $datasource, $resultNumber, $seg, $user, $dscachetimestamp) {
    
    //print "<br>toInsertSOLRdocument ($datasource)<br>";

    // create a new document for the data
    $result_doc = new Solarium_Document_ReadWrite();

    $result_doc->sid        = $sid;
    $result_doc->seg        = $seg;
    $result_doc->lod        = $this->getLod()?1:0;
    $result_doc->rank    		= $this->getRank();
		
		
		if ($result_doc->rank == 0)
		{
			//print "<br>NULL RANK... stimmtes? ";var_dump($this);
		}
		
		
    $result_doc->user       = $user;
    $result_doc->id         = $id // do we have already one id?
    												? $id // yes: take old id
														: $result_doc->sid.'-'.$resultNumber.'-'.uniqid(); //SOLR ID unique!!!
    $result_doc->type       = $this->getResultType();
    $result_doc->title      = encode4solr($this->getTitle());
    $result_doc->authors    = encode4solr($this->getAuthors());
    $result_doc->date       = $this->getDate();
    $result_doc->urlPage    = encode4solr($this->getUrlPage());
    $result_doc->wdatasource = $datasource; //rodin datasource id (the widget url)
    $result_doc->wdscachetimestamp = $dscachetimestamp; //rodin datasource generation time of the current record
    
    
    //print "<hr>inserting result: ";var_dump($result_doc);
    
    //Add specific attr/value fields:

    $fulltext=$result_doc->title;

    foreach ($this->resultProperties as $propertyName=>$propertyValue) {
			if ($propertyValue != '') {

        $result_doc->$propertyName  = $propertyValue;

        $fields[]   = array($propertyName,'string',$propertyValue);
        $fulltext.=($fulltext<>'')?' ':'';
			}
		}
    #Put everything together in order to find per search the value: TBD 
    $result_doc->body = encode4solr($fulltext);
      
		return $result_doc;
	}

    

	public function headerDiv($resultIdentifier) {
		if ($this->getLod()) 
		{
			$EVTL_SOURCE='EXTERNAL ';
			$HEADERCLASS='oo-result-header-lod';
		}
		else {
			$HEADERCLASS='oo-result-header';
		}
		$color = RodinResultManager::getRodinResultTypeColor($this->resultType,$this->getLod());
		$title = $EVTL_SOURCE . RodinResultManager::getRodinResultTypeName($this->resultType);
		if ($this->getRank()) $title.= ' ranked with ' . $this->getRank() . ' points';
		$html = '<div id="header-' . $resultIdentifier . '" class="'.$HEADERCLASS.'" style="border-left: 2px solid ' . $color . ';" title="' . $title . '"></div>';
		return $html;
	}

	public function htmlHeaderZenFilter($sid, $resultIdentifier = null) {
		global $ZEN_FILTER_ICON;
		global $widgetresultdivid;

		$text64 = base64_encode($this->toPureContentText());
		$query64 = base64_encode(get_query($sid));

		if ($resultIdentifier != null) {
			$jsZenFilter = "rodin_zen_filter('$text64', '$query64', document.getElementById('spotlight-box-$resultIdentifier'));";
		} else {
			$jsZenFilter = "rodin_zen_filter('$text64', '$query64', document.getElementById('spotlight-box-$widgetresultdivid'));";
		}

		return '<img src="' . $ZEN_FILTER_ICON . '" title="' . lg('titleLaunchZenFilter') . '" onclick="' . $jsZenFilter . '" />';
	}
  
  public function htmlHeaderMLT($id,$sid) {
		global $MLT_ICON;
		global $widgetresultdivid;
    global $SOLR_RODIN_CONFIG;

		$solr_user= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['user'];
    $solr_host= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['host']; //=$HOST;
    $solr_port= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['port']; //=$SOLR_PORT;
    $solr_path= $SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['path']; //='/solr/rodin_result/';
    $solr_mlt= "http://$solr_host:$solr_port$solr_path";

		$jsMLT = "widget_morelikethis('$id','$sid','$solr_mlt');";

		return '<img src="' . $MLT_ICON . '" title="' . lg('titleLaunchWidgetMLT') . '" onclick="' . $jsMLT . '" />';
	}
	
	public function htmlHeader($resultIdentifier, $resultCounter, $sid, $aggView=false) {
		global $widgetresultdivid;
		global $datasource;
		global $WEBROOT,$RODINROOT;

		$html = $resultCounter . '<br />';
		
		if ($this->getUrlPage() != '') {
			$html .= '<img src="'."$WEBROOT$RODINROOT".'/gen/u/images/link_go.png" title="' . lg('titleOpenResult') . '" onclick="window.open(\'' . $this->getUrlPage() . '\',\'_blank\');" /><br />';
		}
		
		// $html .= $this->htmlHeaderZenFilter($sid, $resultIdentifier) . '<br />';
		
		if (!$aggView)
			$html .= $this->htmlHeaderMLT($this->id, $this->sid, $datasource, $resultIdentifier) . '<br />';
		
		return $html;
	}
	
	public function contentDiv($resultIdentifier) {
		$html = '<div id="content-' . $resultIdentifier . '" class="oo-result-content"></div>';
		return $html;
	}
	
	public function jsScriptAndRender($resultIdentifier, $resultCounter, $sid) {

		$html = '<script type="text/javascript">';
		$html .= 'var result = new RodinResult("' . $resultIdentifier . '")' . ";\n";
		$html .= 'result.header = ' . json_encode($this->htmlHeader($resultIdentifier, $resultCounter, $sid)) . ";\n";
		$html .= 'result.minHeader = ' . json_encode($resultCounter . '<br />') . ";\n";
		$html .= 'result.minContent = ' . json_encode($this->toInWidgetHtml('min')) . ";\n";
		$html .= 'result.tokenContent = ' . json_encode($this->toInWidgetHtml('token')) . ";\n";
		$html .= 'result.allContent = ' . json_encode($this->toInWidgetHtml('all')) . ";\n";
		$html .= 'widgetResultSet.results.push(result)' . ";\n";
		$html .= '</script>';
		
		return $html;
	}
	
	/**
	 * Produces a pure content representation of the result,
	 * used mainly by the function that sends text to the
	 * ZenFilter for concept/entity recognition.
	 */
	public function toPureContentText() {
		$string = '"' . $this->getTitle() . '"';
		$string .= ' by ' . $this->getAuthors() .'.';
		$string .= ' (' . $this->getDate() . ').';
		
		return $string;
	}
	
	public function __toString() {
		$string = '[' . strtoupper(RodinResultManager::getRodinResultTypeName($this->resultType)) . ' ';
		$string .= '[Title: "' . $this->getTitle() . '" ] ';
		$string .= '[Score: "' . $this->getScore() . '" ] ';
		$string .= '[Authors: "' . $this->getAuthors() . '" ] ';
		$string .= '[Date: "' . $this->getDate() . '" ] ';
		$string .= '[URL: "' . $this->getUrlPage() . '" ] ';
		$string .= '[Properties: ' . var_export($this->getResultProperties(), true) . ']]';
		
		return $string;
	}

	
	
	/**
	 * Generate triples into local store 'rodin'
	 * which will be taken as a basis for further 
	 * semantic expansions (meshups)
	 */
	public function rdfize($sid,$datasource,$searchterm,$USER_ID,$timestamp)
	{
		
		include_once("RodinRDFResult.php");
		global $RDFLOG;
		//Sets as class var the RDF helper
		if (! $this->RDFenhancement)
			$this->RDFenhancement = new RodinRDFResult($this,$datasource,$searchterm,$USER_ID,$sid);
		$RDF = $this->RDFenhancement;
		$C=get_class($RDF);

		$this->is_rdfized = true;
		
		$count_triples_before=count_ARC_triples($C::$store);
		
		$RDF->rdfize($sid,$timestamp);

		$count_triples_after=count_ARC_triples($C::$store);
		
		$count_triples_added = $count_triples_after - $count_triples_before;
		
		return array($C::$store,$count_triples_added);
	} // rdfize 
	
	
	/**
	 * Using the attached LOD sources
	 * Using the related-to subject won by rdfize
	 * Calculate the documents related to these subjects 
	 * LOD-Link these documents inside the local result rdf store
	 * Store these documents in the local store for 
	 * Suggestions
	 * 
	 * CACHE CALLS
	 */
	public function rdfLODfetchDocumentsOnSubjects($sid,$datasource,$searchterm,$USER_ID)
	{
		$C=get_class($this->RDFenhancement);
		
		$count_triples_before=count_ARC_triples($C::$store);
		
		$ok = $this->RDFenhancement->rdfLODfetchDocumentsOnSubjects($sid,$datasource,$searchterm,$USER_ID);
		
		$count_triples_after=count_ARC_triples($C::$store);
		
		$count_triples_added = $count_triples_after - $count_triples_before;
		
		return array($ok,$count_triples_added);
		
	} // rdfLODfetchDocumentsOnSubjects
	
	
	public function rerankadd_rdf_documents_related_to_search($sid,$datasource,$searchterm,$USER_ID)
	{
		return $this->RDFenhancement->rerankadd_rdf_documents_related_to_search($sid,$datasource,$searchterm,$USER_ID);
	}

	
	/**
	 * This method sets a property of the result. However, since
	 * different kinds of results accept only some properties, if
	 * defined, it will check if the $propertyName is among the
	 * $validProperties.
	 * 
	 * @param $propertyName
	 * @param $propertyValue
	 */
	public function setProperty($propertyName, $propertyValue) {
		if ($this->validProperties != null) {
			if (in_array($propertyName, $this->validProperties)) {
				$this->resultProperties[$propertyName] = $propertyValue;
			}
		} else {
			$this->resultProperties[$propertyName] = $propertyValue;
		}
	}
	
	public function getProperty($propertyName) {
		if (isset($this->resultProperties[$propertyName])) {
			return $this->resultProperties[$propertyName];
		} else {
			return '';
		}
	}
	
	
	public function setSubjects(&$subjects)
	{
		$this->subjects=$subjects;
	}
	
	public function getSubjects()
	{
		return $this->subjects;
	}

	public function setCacheTimeStamp($timestamp) {
		$this->wdsctimestamp = $timestamp;
	}
	
	public function getCacheTimeStamp() {
		return $this->wdsctimestamp;
	}
	
	public function setUrlPage($urlPage) {
		$this->urlPage = $urlPage;
	}
	
	public function getUrlPage() {
		return $this->urlPage;
	}
	
	public function setScore($score) {
		$this->score = $score;
	}
	
	public function getScore() {
		return $this->score;
	}

	public function setRank($rank) {
		$this->rank = $rank;
	}
	
	public function getRank() {
		return $this->rank;
	}

	public function setLod($lod) {
		$this->lod = $lod;
	}
	
	public function getLod() {
		return $this->lod;
	}

	public function setTitle($title) {
		$this->title = $title;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setAuthors($authors) {
		$this->authors = $authors;
	}
	
	public function getAuthors() {
		return $this->authors;
	}
	
	public function setDate($date = '') {
		try {
					if ($date == '')
						$tempDate = new DateTime();
					else 
						$tempDate = new DateTime($date);
					$this->date = $tempDate->format('d.m.Y');
		}
		catch (Exception $e)
		{
			print "<br>ERROR in RodinResult->setDate($date)";
			$this->date = null;
		}
	}
	
	public function getDate() {
		return $this->date;
	}

	public function setSid($sid) {
		$this->sid = $sid;
	}

	public function getSid() {
		return $this->sid;
	}

  public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}
	
	public function getResultType() {
		return $this->resultType;
	}
	
	public function setResultType($value) {
		$this->resultType = $value;
	}
	
	
}
