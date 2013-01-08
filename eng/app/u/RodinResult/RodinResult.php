<?php

/**
 * An basic implementation of results, no particular
 * properties expected.
 *
 * @author Javier Belmonte
 */
class BasicRodinResult {
	private $urlPage;
	private $title;
	private $score; //SOLR score on morelikethis queries
	private $authors;
	private $date;
	protected $id; // SOLR ID or DB RECORD ID
	protected $sid;
	
	private $resultProperties;
	private $validProperties;
	
	private $resultType;

	public function __construct($resultType = RodinResultManager::RESULT_TYPE_BASIC) {
		$this->resultProperties = array();
		$this->validProperties = array();
		
		$this->resultType = $resultType;
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
		$html .= "<h1>{$this->title}</h1>";
		$html .= "<p><b>Author(s) : </b> {$this->authors}<br />";
		$html .= "<b>Date : </b> {$this->date}<br />";
		$html .= "<b>URL : </b> <a href=\"{$this->urlPage}\">{$this->urlPage}</a></p>";

		return $html;
	}
	
	public function toInOwnTabHtml() {
		return $this->toInWidgetHtml('all');
	}
	
	/**
	 * Produces the in-Widget represetation of the result
	 */
	public function toInWidgetHtml($textZoom = 'token') {
		$html = '<div class="oo-result">';
		
		switch ($textZoom) {
			case 'min':
				$html .= '<p>"' . $this->separateWordsInSpans($this->title) . '" by ' . $this->separateWordsInSpans($this->authors) . '</p>';
			break;
			
			default:
				$html .= '<h1>' . $this->separateWordsInSpans($this->title) . '</h1>';
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

	/**
	 * Will put each single word in a span to which an action can be added
	 * later on using jQuery.
	 */
	protected function separateWordsInSpans($text) {

		$title = lg("lblActionsOnWord");
		
		$pattern = '/\w+/u';
		$replace = '<span class="result-word" title="' . $title . '">$0</span>';

		return preg_replace($pattern, $replace, $text);
		
		return implode(' ', $enhancedWords);
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
		$sql .= ", ('$sid','$datasource','$resultNumber.0','','','type','int','" . mysql_real_escape_string($this->resultType, $conn) . "', '','')";
		$sql .= ", ('$sid','$datasource','$resultNumber.1','','','title','string','" . mysql_real_escape_string($this->title, $conn) . "', '','')";
		$sql .= ", ('$sid','$datasource','$resultNumber.2','','','authors','string','" .mysql_real_escape_string($this->authors, $conn) . "', '','')";
		$sql .= ", ('$sid','$datasource','$resultNumber.3','','','date','string','" . mysql_real_escape_string($this->date, $conn) . "', '','')";
		$sql .= ", ('$sid','$datasource','$resultNumber.4','','','urlPage','string','" . mysql_real_escape_string($this->urlPage, $conn) . "', '','')";
		
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
	 * @param $sid
	 * @param $datasource
	 * @param $resultNumber
	 */
	public function toInsertSOLRdocument(&$client, $sid, $datasource, $resultNumber, $seg, $user) {
    
    //print "<br>toInsertSOLRdocument ($datasource)<br>";

    // create a new document for the data
    $result_doc = new Solarium_Document_ReadWrite();

    $result_doc->sid        = $sid;
    $result_doc->seg        = $seg;
    $result_doc->user       = $user;
    $result_doc->id         = $result_doc->sid.'-'.$resultNumber.'-'.uniqid(); //SOLR ID unique!!!
    $result_doc->type       = $this->resultType;
    $result_doc->title      = encode4solr($this->title);
    $result_doc->authors    = encode4solr($this->authors);
    $result_doc->date       = $this->date;
    $result_doc->urlPage    = encode4solr($this->urlPage);
    $result_doc->wdatasource = $datasource; //rodin datasource id (the widget url)

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
		$color = RodinResultManager::getRodinResultTypeColor($this->resultType);
		$title = RodinResultManager::getRodinResultTypeName($this->resultType);
		$html = '<div id="header-' . $resultIdentifier . '" class="oo-result-header" style="border-left: 2px solid ' . $color . ';" title="' . $title . '"></div>';
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
	
	public function htmlHeader($resultIdentifier, $resultCounter, $sid) {
		global $widgetresultdivid;
		global $datasource;

		$html = $resultCounter . '<br />';
		
		if ($this->getUrlPage() != '') {
			$html .= '<img src="/rodin/gen/u/images/link_go.png" title="' . lg('titleOpenResult') . '" onclick="window.open(\'' . $this->getUrlPage() . '\',\'_blank\');" /><br />';
		}
		
		$html .= $this->htmlHeaderZenFilter($sid, $resultIdentifier) . '<br />';
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
		$string = '"' . $this->title . '"';
		$string .= ' by ' . $this->authors .'.';
		$string .= ' (' . $this->date . ').';
		
		return $string;
	}
	
	public function __toString() {
		$string = '[' . strtoupper(RodinResultManager::getRodinResultTypeName($this->resultType)) . ' ';
		$string .= '[Title: "' . $this->title . '" ] ';
		$string .= '[Score: "' . $this->score . '" ] ';
		$string .= '[Authors: "' . $this->authors . '" ] ';
		$string .= '[Date: "' . $this->date . '" ] ';
		$string .= '[URL: "' . $this->urlPage . '" ] ';
		$string .= '[Properties: ' . var_export($this->resultProperties, true) . ']]';
		
		return $string;
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
		if ($date == '')
			$tempDate = new DateTime();
		else 
			$tempDate = new DateTime($date);

		$this->date = $tempDate->format('d.m.Y');
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
}
