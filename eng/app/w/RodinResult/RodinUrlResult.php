<?php 

require_once 'RodinResult.php';

/**
 * The URL kind of result, it accepts the following properties:
 * {description, url, tags}
 * 
 * @author Javier Belmonte
 */
class RodinUrlResult extends BasicRodinResult {
	public function __construct() {
		parent::__construct(RodinResultManager::RESULT_TYPE_URL);
		$this->setValidProperties(array('description', 'url', 'tags'));
	}
	
	public function toBasicHtml() {
		$html = parent::toBasicHtml();
		
		$html .= "<b>URL: </b> {$this->getProperty('url')}<br />";
		$html .= "<p><b>Description : </b> {$this->getProperty('description')}<br />";
		$html .= "<b>Tags: </b> {$this->getProperty('tags')}<br />";
		
		return $html;
	}

	
	public function toInWidgetHtml($textZoom = 'token') {
	switch ($textZoom) {
			case 'all':
				$addToAll = '';
				
				if ($this->getProperty('description') != '') {
					$addToAll .= '<p style="margin: 6px 6px;"><em>' . $this->separateWordsInSpans($this->getProperty('description')) . '</em></p>';
				}
				
				$addToAll .= $this->valueAsHtmlParagraph('Tags:', $this->getProperty('tags'), true);
			case 'token':
				$html = '<div class="oo-result">';
				$html .= '<h1>' . $this->separateWordsInSpans($this->getTitle()) . '</h1>';
				$html .= $this->valueAsHtmlParagraph('By', $this->getAuthors(), true);
				$html .= $this->valueAsHtmlParagraph('Publication date:', $this->getDate(), false);
				$html .= $this->valueAsHtmlParagraph('URL:', $this->getProperty('url'), false);
				
				if (isset($addToAll)) {
					$html .= $addToAll;
				}
				
				$html .= '</div>';
			
				return $html;
				break;
			case 'min':
			default:
				return parent::toInWidgetHtml($textZoom);
		}
	}
		
	public function toPureContentText() {
		$string = parent::toPureContentText();
		
		foreach ($this->getValidProperties() as $property) {
			if ($property != 'url')
				$string .= ' ' . $this->getProperty($property);
		}
		
		return $string; 
	}
}
