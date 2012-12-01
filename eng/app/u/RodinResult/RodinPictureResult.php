<?php 

require_once 'RodinResult.php';

/**
 * The Picture kind of result, it accepts the following properties:
 * {description, subject(s), publisher, review(s), cover(s), isbn}
 * 
 * @author Javier Belmonte
 */
class RodinPictureResult extends BasicRodinResult {
	public function __construct() {
		parent::__construct(RodinResultManager::RESULT_TYPE_PICTURE);
		$this->setValidProperties(array('pictureUrl', 'description', 'tags', 'geoloc'));
	}
	
	public function toBasicHtml() {
		$html = parent::toBasicHtml();
		
		$html .= "<p><b>Description : </b> {$this->getProperty('description')}<br />";
		$html .= "<b>Subjects: </b> {$this->getProperty('subject')}<br />";
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
				
				if ($this->getProperty('description') != '')
					$addToAll .= '<p style="margin: 6px 6px;"><em>' . $this->separateWordsInSpans($this->getProperty('description')) . '</em></p>';

				$addToAll .= $this->valueAsHtmlParagraph('Tags:', $this->getProperty('tags'), true);
				$addToAll .= $this->valueAsHtmlParagraph('GeoLoc:', $this->getProperty('geoloc'), true);
			case 'token':
				$html .= '<div class="oo-result">';
				
				if ($this->getProperty('pictureUrl') != '')
					$html .= '<img style="width: 50px; float: right; margin-right: 2px; padding: 2px; border: 1px solid lightgray;"'
						.' src="' . $this->getProperty('pictureUrl') . '" />';

				$html .= '<h1>' . $this->separateWordsInSpans($this->getTitle()) . '</h1>';
				$html .= $this->valueAsHtmlParagraph('By', $this->getAuthors(), true);
				$html .= $this->valueAsHtmlParagraph('Publication date:', $this->getDate(), false);
				
				if (isset($addToAll)) {
					$html .= $addToAll;
				}
				
				$html .= '</div>';
				
				return $html;
				break;
			case 'min':
				$html .= '<div class="widgetImageGridDiv">';
				
				if ($this->getProperty('pictureUrl') != '')
					$html .= '<img class="widgetImageGridImg" src="' . $this->getProperty('pictureUrl') . '" />';
				else
					$html .= '<img class="widgetImageGridImg" src="" />';

				$html .= '<div class="widgetImageGridFilter" style="display: none;">';
				$html .= $this->htmlHeaderZenFilter($this->getSid(), null);
				$html .= '</div>';

				$html .= '</div>';
				
				return $html;
				break;
			default:
				return parent::toInWidgetHtml($textZoom);
		}
	}

	/**
	* Because the presentation of the pictures in 'min' render mode doesn't
	* allow for the header div holding the result counter, we need to hide it.
	*/
	public function jsScriptAndRender($resultIdentifier, $resultCounter, $sid) {
		$html = parent::jsScriptAndRender($resultIdentifier, $resultCounter, $sid);
		
		// set the result min header to null
		$html = preg_replace("/result\.minHeader\s=.*;/", 'result.minHeader = null;', $html);
		
		return $html;
	}
	
	/**
	* @overrides to include valid properties
	*/
	public function toPureContentText() {
		$string = parent::toPureContentText();
		
		foreach ($this->getValidProperties() as $property) {
			$string .= ' ' . $this->getProperty($property);
		}
		
		return $string; 
	}
}
