<?php 

require_once 'RodinResult.php';

/**
 * The Book kind of result, it accepts the following properties:
 * {description, subject(s), publisher, review(s), cover(s), isbn}
 * 
 * @author Javier Belmonte
 */
class RodinBookResult extends BasicRodinResult {
	public function __construct() {
		parent::__construct(RodinResultManager::RESULT_TYPE_BOOK);
		$this->setValidProperties(array('description', 'subjects', 'publisher', 'review', 'cover', 'isbn'));
	}
	
	public function toBasicHtml() {
		$html = parent::toBasicHtml();
		
		$html .= "<p><b>Description : </b> {$this->getProperty('description')}<br />";
		$html .= "<b>Subjects: </b> {$this->getProperty('subjects')}<br />";
		$html .= "<b>Publisher: </b> {$this->getProperty('publisher')}<br />";
		$html .= "<b>Review: </b> {$this->getProperty('review')}<br />";
		$html .= "<b>Cover URL : </b> {$this->getProperty('cover')}</p>";
		
		return $html;
	}

	/**
	 * Produces the in-Widget represetation of the result
	 */
	public function toInWidgetHtml($textZoom = 'token') {
		switch ($textZoom) {
			case 'all':
				$addToAll = '';
				
				if ($this->getProperty('description') != '') {
					$addToAll .= '<p style="margin: 6px 6px;"><em>' . $this->separateWordsInSpans($this->getProperty('description')) . '</em></p>';
				}
				$addToAll .= $this->valueAsHtmlParagraph('ISBN:', $this->getProperty('isbn'), false);
				$addToAll .= $this->valueAsHtmlParagraph('Publisher:', $this->getProperty('publisher'), false);
				$addToAll .= $this->valueAsHtmlParagraph('Subjects:', $this->getProperty('subjects'), true);
				$addToAll .= $this->valueAsHtmlParagraph('Reviews:', $this->getProperty('review'), true);
			case 'token':
				$html .= '<div class="oo-result">';
				
				if ($this->getProperty('cover') != '')
					$html .= '<img style="width: 40px; float: right; margin-right: 2px; padding: 2px; border: 1px solid lightgray;" src="' . $this->getProperty('cover') . '" />';

				$html .= '<h1>' . $this->separateWordsInSpans($this->getTitle()) . '</h1>';
        $html .= $this->valueAsHtmlParagraphNumber('Score:', $this->getScore());
        $html .= $this->valueAsHtmlParagraph('By', $this->getAuthors(), true);
				$html .= $this->valueAsHtmlParagraph('Publication date:', $this->getDate(), false);
				
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
			$string .= ' ' . $this->getProperty($property);
		}
		
		return $string; 
	}
}
