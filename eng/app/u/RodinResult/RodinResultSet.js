function RodinResultSet() {
	this.results = new Array();
	this.containerDivId = null;
	
	this.askResulsToRender = function (textZoom) {
		for (var i = 0; i < this.results.length; i++) {
			this.results[i].render(textZoom);
		}

		// Hide open Zen Filter box
		jQuery('.spotlightbox').html('');
		jQuery('.spotlightbox').css('visibility', 'hidden');

		// After rendering, context-menu needs to be re-attached
		setContextMenu('widgetContextMenu');
	}

	/**
	 * Adds a RodinResult instance to the set, prepares the div
	 * to contain it and asks it to render.
	 */
	this.addResultAndRender = function (result, textZoom) {
		this.results.push(result);

		var resultDiv = jQuery('<div class="oo-result-container"></div>');
		resultDiv.append(jQuery(result.headerDiv));
		resultDiv.append(jQuery(result.contentDiv));

		var resultsContainer = jQuery('#' + this.containerDivId);
		resultsContainer.append(resultDiv);

		result.render(textZoom);

		// After rendering, context-menu needs to be re-attached
		setContextMenu('widgetContextMenu');
	}
}

function RodinResult(resultId) {
	this.resultId = resultId;
	
	this.headerDivId = "header-" + this.resultId;
	this.contentDivId = "content-" + this.resultId;
	
	this.headerDiv = null;
	this.contentDiv = null;

	this.header = null;
	this.minHeader = null;
	this.minContent = null;
	this.tokenContent = null;
	this.allContent = null;
	
	/*
	 * Checks whether parent.RESULTFILTEREXPR is contained
	 * in the intended text portion
	 */
	this.shouldBeDisplayed = function(textZoom,txt)
	{
		var visible = (txt=='');
		if (!visible)
		{
			var content='';
			switch (textZoom) {
		      case 'min':
		      	content=this.minContent;
		      	break;
		      case 'token':
		      	content=this.tokenContent;
		      	break;
		      case 'all':
		      	content=this.allContent;
		      	break;
			}
			if (content)
				visible=parent.use_morpho_filter(content.replace(/"/g,''), txt);
		}
		return visible;
	};
	
	
	/**
	 * Discriminate token and
	 * Transforms the onclick portion of the right occurences of 
	 * in order to force undo-display of this txt 
	 **/
	this.transformContentToBeReDisplayed = function(textZoom,txt)
	{
		var content='';
		
		switch (textZoom) {
	      case 'min':
	      	this.minContent = this.transform2click4display( this.minContent, txt );
	      	break;
	      case 'token':
	      	this.tokenContent = this.transform2click4display( this.tokenContent, txt );
	      	break;
	      case 'all':
	      	this.allContent = this.transform2click4display( this.allContent, txt );
	      	break;
		}
	};
	
	
	
	/*
	 * Transforms the onclick portion of the right occurences of 
	 * in order to force undo-display of this txt 
	 */
	this.transform2click4display = function(content,txt)
	{
		// replace every other (non txt) undo_ by ''
		content=content?content.replace(/purr\(/gi,'prr('):content;
			
		if (txt)
		{
			var pattern=""; 
			var replace="";

			// replace restrict_render('txt')=rr() by undo_restrict_render() = urr()
			pattern="/rr\\\('"+txt+"'\\\)/gi"; 
			replace="urr('"+txt+"')"; 
			
			var expression='content=content.replace('+pattern+',"'+replace+'")';
			eval(expression); // sideeffect on content			
		}
		return content;
	};
	
	/**
	* Renders results whose words contains parent.RESULTFILTEREXPR (JB+FRI)
	*/
	this.render = function (textZoom) {
		if(this)
		{
			//Filter allows showing result?
			var visible=this.shouldBeDisplayed(textZoom, parent.RESULTFILTEREXPR);
	    if(visible)
	    {
	    	this.transformContentToBeReDisplayed(textZoom, parent.RESULTFILTEREXPR);
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
	        if (visible)
	        {
	          jQuery('#' + this.headerDivId).show();
	          jQuery('#' + this.headerDivId).html(this.header);
	        }
	        break;
	      }
	      // Update highlighted ontoterms (facets)
	      var remark_ontoterms=false;
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
	        jQuery('#' + this.contentDivId).show();
	        remark_ontoterms=true;
	        break;
	      case 'token':
	        jQuery('#' + this.contentDivId).parent().css('float', '');
	        jQuery('#' + this.contentDivId).parent().css('width', '100%');
	        jQuery('#' + this.contentDivId).parent().css('border-bottom', '1px solid gray');
	        jQuery('#' + this.contentDivId).html(spotlightDivHtml + this.tokenContent);
	        jQuery('#' + this.contentDivId).show();
	        remark_ontoterms=true;
	        break;
	      case 'all':
	        jQuery('#' + this.contentDivId).html(spotlightDivHtml + this.allContent);
	        remark_ontoterms=true;
	        break;
	      default:
	        break;
	      }
	     }
	     else //Not visible: Hide node
	     {
	         jQuery('#' + this.headerDivId).hide();
	         jQuery('#' + this.contentDivId).hide();
	     }
	
	     if (remark_ontoterms)
	     {
	       parent.ONTOTERMS_REDO_HIGHLIGHTING=true;
	       parent.mark_ontoterms_on_resultmatch()
	     }
	
	    }
    };
}