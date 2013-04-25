<?php 

require_once 'RodinResult.php';

/**
 * The Article kind of result, it accepts the following properties:
 * {abstract, full-text, keywords, review, doi}
 * 
 * @author Javier Belmonte
 */
class RodinArticleResult extends BasicRodinResult {
	public function __construct() {
		parent::__construct(RodinResultManager::RESULT_TYPE_ARTICLE);
		$this->setValidProperties(array('datasource','abstract', 'full-text', 'keywords', 'review', 'doi'));
	}
	
	public function toBasicHtml() {
		$html = parent::toBasicHtml();
		
		$html .= "<p><b>Abstract : </b> {$this->getProperty('abstract')}<br />";
		$html .= "<b>Full-text: </b> {$this->getProperty('full-text')}<br />";
		$html .= "<b>Keywords: </b> {$this->getProperty('keywords')}<br />";
		$html .= "<b>Review : </b> {$this->getProperty('review')}</p>";
		
		return $html;
	}

	/**
	 * Produces the in-Widget represetation of the result
	 */
	public function toInWidgetHtml($textZoom = 'token') {
		if ($textZoom != 'all') {
			return parent::toInWidgetHtml($textZoom);
		} else {
			$html = '<div class="oo-result">';
			$html .= '<h1>' . $this->separateWordsInSpans($this->getTitle()) . '</h1>';
      $html .= $this->valueAsHtmlParagraphNumber('Score:', $this->getScore());
      $html .= $this->valueAsHtmlParagraph('By', $this->getAuthors(), true);
			$html .= $this->valueAsHtmlParagraph('Publication date:', $this->getDate(), false);
			
			if ($this->getProperty('abstract') != '') {
				$html .= '<p style="margin: 6px 6px;"><em>' . $this->separateWordsInSpans($this->getProperty('abstract')) . '</em></p>';
			}
			
			$html .= $this->valueAsHtmlParagraph('Full text:', $this->getProperty('full-text'), true);
			$html .= $this->valueAsHtmlParagraph('Keywords:', $this->getProperty('keywords'), true);
			$html .= $this->valueAsHtmlParagraph('DOI:', $this->getProperty('doi'), false);
			$html .= $this->valueAsHtmlParagraph('Reviews:', $this->getProperty('review'), true);
			
			$html .= '</div>';
			
			return $html;
		}
	}
	
	public function toPureContentText() {
		$string = parent::toPureContentText();
		
		foreach ($this->getValidProperties() as $property) {
			$string .= ' ' . $this->getProperty($property);
		}
		
		return $string; 
	}
}
