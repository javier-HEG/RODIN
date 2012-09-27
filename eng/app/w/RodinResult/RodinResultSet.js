function RodinResultSet() {
	this.results = new Array();
	
	this.askResulsToRender = function (textZoom) {
		for (var i = 0; i < this.results.length; i++) {
			this.results[i].render(textZoom);
		}

		// Hide open Zen Filter box
		jQuery('.spotlightbox').html('');
		jQuery('.spotlightbox').css('visibility', 'hidden');

		// After rendering, context-menu needs to be re-attached
		setContextMenu();
	}
	
}

function RodinResult(resultId) {
	this.resultId = resultId;
	
	this.headerDivId = "header-" + this.resultId;
	this.contentDivId = "content-" + this.resultId;
	
	this.header = null;
	this.minHeader = null;
	this.minContent = null;
	this.tokenContent = null;
	this.allContent = null;
	
	/**
	* This function 
	*/
	this.render = function (textZoom) {
		// Header
		switch (textZoom) {
		case 'min':
			if (this.minHeader == null) {
				jQuery('#' + this.headerDivId).html();
				jQuery('#' + this.headerDivId).hide();
			} else {
				jQuery('#' + this.headerDivId).show();
				jQuery('#' + this.headerDivId).html(this.minHeader);
			}
			break;
		case 'token':
		case 'all':
		default:
			jQuery('#' + this.headerDivId).show();
			jQuery('#' + this.headerDivId).html(this.header);
			break;
		}
		
		// Content
		var spotlightDivHtml = '<div class="spotlightbox" style="visibility:hidden;" id="spotlight-box-' + this.resultId + '" title=""></div>';
		switch (textZoom) {
		case 'min':
			if (this.minHeader == null) {
				jQuery('#' + this.contentDivId).html(this.minContent);
				jQuery('#' + this.contentDivId).parent().css('float', 'left');
				jQuery('#' + this.contentDivId).parent().css('width', 'auto');
				jQuery('#' + this.contentDivId).parent().css('border-bottom', 'none');

				jQuery('#' + this.contentDivId).hover(function() {
					var filterButtonDiv = jQuery('#' + jQuery(this).attr('id') + ' .widgetImageGridDiv .widgetImageGridFilter');
					filterButtonDiv.show();
				}, function() {
					var filterButtonDiv = jQuery('#' + jQuery(this).attr('id') + ' .widgetImageGridDiv .widgetImageGridFilter');
					filterButtonDiv.hide();
				});
			} else
				jQuery('#' + this.contentDivId).html(spotlightDivHtml + this.minContent);
			break;
		case 'token':
			jQuery('#' + this.contentDivId).parent().css('float', '');
			jQuery('#' + this.contentDivId).parent().css('width', '100%');
			jQuery('#' + this.contentDivId).parent().css('border-bottom', '1px solid gray');
			jQuery('#' + this.contentDivId).html(spotlightDivHtml + this.tokenContent);
			break;
		case 'all':
			jQuery('#' + this.contentDivId).html(spotlightDivHtml + this.allContent);
			break;
		default:
			break;
		}
    };
}