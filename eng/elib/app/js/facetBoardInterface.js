/**
 * FILE: facetBoardInterface.js
 * AUTHOR: fabio.ricci@semweb.ch for HEG
 * DATE: 2013
 */

// THIS AJAX File must be included 
// form inside a php script with root variables

/**
 * Shows/hides all the concepts found by the SRC engine to
 * whom the 'facet_id' corresponds.
 */
function fb_toggle_allItemContent(facet_id) {
	var facetgroup_div_id = 'fb_itemcontent_'+facet_id;
	var facetgroup_div=friGetElementId(facetgroup_div_id);
	
	if (facetgroup_div.className == 'facetgroup-inactive') 
		fb_show_allItemContent(facet_id,facetgroup_div);
	else 
		fb_hide_allItemContent(facet_id,facetgroup_div);
}

function fb_show_allItemContent(facet_id,facetgroup_div)
{
fb_show_itemcontentgroup(facet_id, 'show');
fb_show_itemcontent(facet_id, '_b_','show');
fb_show_itemcontent(facet_id, '_n_','show');
fb_show_itemcontent(facet_id, '_r_','show');
jQuery( friGetElementId('fb_itemcount_b_'+facet_id) ).hide();
jQuery( friGetElementId('fb_itemcount_n_'+facet_id) ).hide();
jQuery( friGetElementId('fb_itemcount_r_'+facet_id) ).hide();
}

function fb_hide_allItemContent(facet_id,facetgroup_div)
{
	fb_show_itemcontentgroup(facet_id, 'hide');
	fb_show_itemcontent(facet_id, '_b_','hide');
	fb_show_itemcontent(facet_id, '_n_','hide');
	fb_show_itemcontent(facet_id, '_r_','hide');
	
	jQuery( friGetElementId('fb_itemcount_b_'+facet_id) ).show();
	jQuery( friGetElementId('fb_itemcount_n_'+facet_id) ).show();
	jQuery( friGetElementId('fb_itemcount_r_'+facet_id) ).show();
}

/**
 * 
 */
function fb_toggleonto_temponoff(cb,src_id,maybezero)
{
	var iid = 'iyn_'+src_id;
	var checkservice='<?php print $FB_SRC_TCHECKSERVICEURL; ?>'
									+'?i='+src_id
									+'&c='+cb.getAttribute('shouldchecked');
	//alert('checkservice: '+checkservice);
	//CALL SRC
	var shouldchecked = (cb.getAttribute('shouldchecked') == 'true');
	$p.ajax.call( checkservice,
		{
			'type':'load',
			'callback':
			 {
				'function':parent.cbfb_toggleonto_temponoff,
				'variables':
				 {
					'i':src_id,
					'c':shouldchecked,
					'cbid':cb.id,
					'iid':iid,
					'maybezero':maybezero,
					'srcname':cb.getAttribute('srcname'),
				 }
			 }
		}
	);	
} //fb_toggleonto_temponoff



/**
 * Toogle on/off the ontofacets engine
 */
function cbfb_toggleonto_temponoff(response,vars)
{
	var i = vars['i'];
	var c = vars['c'];
	var srcname = vars['srcname'];
	var cbid = vars['cbid'];
	var iid = vars['iid'];
	var maybezero = vars['maybezero'];
	var cb_item=document.getElementById(cbid);

	var iconsrc_on_off = cb_item.getAttribute('iconsrc_on_off');
	var iconsrc_off_on = cb_item.getAttribute('iconsrc_off_on');
	var title_on_off = cb_item.getAttribute('title_on_off');
	var title_off_on = cb_item.getAttribute('title_off_on');
	
	var affectedrows = -1;
	var SRCitemNamenExpander1= document.getElementById("fb_itemname_expander_"+i);
	var SRCitemNamenExpander2= document.getElementById("fb_itemname_expander2_"+i);
	var img=document.getElementById(iid);
	
	if (response!=null) 
	{
		affectedrows = (response.getElementsByTagName("affectedrows")[0]).textContent;
	}
	var ok = ((!maybezero && (affectedrows==1))
					 || (maybezero && (affectedrows==0 || affectedrows==1)));
	//alert('cbfb_toggleonto_temponoff affected rows: '+affectedrows);
	if (ok)
	{
		if (c)
		{ // checked -> undo graying off
			SRCitemNamenExpander1.setAttribute('class','fb-expander');
			if (SRCitemNamenExpander1.getAttribute('realonclick'))
			    SRCitemNamenExpander1.setAttribute('onclick',SRCitemNamenExpander1.getAttribute('realonclick'));
			
			SRCitemNamenExpander2.setAttribute('class','facetcontrol-td');
			SRCitemNamenExpander2.setAttribute('title','This ontological facets engine is switched on - check again on the box (on the left) to switch it off');
			//?Call with 100ms delay?
			if (SRCitemNamenExpander2.getAttribute('realonclick'))
				SRCitemNamenExpander2.setAttribute('onclick',SRCitemNamenExpander2.getAttribute('realonclick'));
			cb_item.setAttribute('checked','true');
			cb_item.setAttribute('shouldchecked','false');
			cb_item.setAttribute('title',title_on_off);
			img.src=iconsrc_on_off;
			img.style.width='15px';
			img.style.height='15px';
		}
		else // not checked -> gray off
		{
			//Hide facets content
			fb_show_itemcontentgroup(i, 'hide');
			fb_show_itemcontent(i, '_b_','hide');
			fb_show_itemcontent(i, '_n_','hide');
			fb_show_itemcontent(i, '_r_','hide');
			
			fb_init_src_display(i, 'broader');
			fb_init_src_display(i, 'narrower');
			fb_init_src_display(i, 'related');

			//Gray off:
			SRCitemNamenExpander1.setAttribute('class','fb-expander-temp-off');
			SRCitemNamenExpander1.setAttribute('realonclick',SRCitemNamenExpander1.getAttribute('onclick'));
			SRCitemNamenExpander1.setAttribute('onclick',"document.getElementById('"+cbid+"').click()");
		
			SRCitemNamenExpander2.setAttribute('class','facetcontrol-td-temp-off');
			SRCitemNamenExpander2.setAttribute('title','This ontological facets engine is temporarily switched off - click once on the engine\'s name \''+srcname+'\' or check again on the box (on the left) to switch it on');
			SRCitemNamenExpander2.setAttribute('realonclick',SRCitemNamenExpander2.getAttribute('onclick'));
			cb_item.setAttribute('checked','false');
			cb_item.setAttribute('shouldchecked','true');
			cb_item.setAttribute('title',title_off_on);
			img.src=iconsrc_off_on;
			img.style.width='19px';
			img.style.height='19px';
		}
	}	

	
} // cbfb_toggleonto_temponoff



function setclick_SRCitemNamenExpanders()
{
	var SRCitemNamenExpander1= document.getElementById("fb_itemname_expander_"+i);
	var SRCitemNamenExpander2= document.getElementById("fb_itemname_expander2_"+i);
	alert('setclick_SRCitemNamenExpanders: '
					+'\n\n'+SRCitemNamenExpander1.getAttribute('realonclick')
					+'\n\n'+SRCitemNamenExpander2.getAttribute('realonclick'));

	SRCitemNamenExpander1.setAttribute('onclick',SRCitemNamenExpander1.getAttribute('realonclick'));
	SRCitemNamenExpander2.setAttribute('onclick',SRCitemNamenExpander2.getAttribute('realonclick'));
}

/**
 * Shows/hides the segments (narrower, broader, related) corresponding
 * to the SRC engine referred by the 'facet_id'. It doesn't force the
 * segments to expand and reveal their cantained terms.
 */
function fb_toggle_itemcontentgroup(facet_id) {
	var facet_div = friGetElementId('fb_itemcontent_' + facet_id);
	if (facet_div != null) {
		if (facet_div.className === 'facetgroup-inactive') {
			alert('show term count facetgroup-inactive')
		} else {
			fb_show_itemcontentgroup(facet_id, 'hide');
		}
	}
}


/**
 * Forces an item (SRC engine) to show or hide its segments (narrower, broader, related)
 * according to the 'mode' parameter. It leaves the segments' expansion (show/hide)
 * untouched.
 */
function fb_show_itemcontentgroup(facet_id, mode) {
	var itemname_div = friGetElementId('fb_itemname_' + facet_id);
	var expander_td = friGetElementId('fb_itemname_expander_' + facet_id);
	var facet_div = friGetElementId('fb_itemcontent_' + facet_id);
	
	if (expander_td != null && facet_div != null) {
		if (mode == 'show') {
			if (itemname_div != null)
				itemname_div.className='facetcontrol-active';
			
			expander_td.className='fb-collapser';
			facet_div.className = 'facetgroup-active';
		} else { //hide
			if (itemname_div != null)
				itemname_div.className='facetcontrol-normal';
			
			expander_td.className='fb-expander';
			facet_div.className = 'facetgroup-inactive';
		}	
	}
}

/**
 * Shows/hides an item (SRC engine) segment's term list.
 */
function fb_toggle_itemcontent(facet_id, extension) {
	var facet_div = friGetElementId('fb_itemcontent' + extension + facet_id);

	if (facet_div != null) {
		var fb_counterobj_name = 'fb_itemcount'+extension+facet_id;
		var fb_counterobj = friGetElementId(fb_counterobj_name);
			
		if (facet_div.className === 'facetlist-inactive') {
			fb_show_itemcontent(facet_id, extension, 'show');
			jQuery(fb_counterobj).hide();
		} else {
			fb_show_itemcontent(facet_id, extension, 'hide');
			jQuery(fb_counterobj).show();
		}
	}
}


/**
 * Forces an item's segment (SRC engine's narrower, broader, related) to show
 * or hide its term list, according to the 'mode' parameter.
 */
function fb_show_itemcontent(facet_id, extension, mode) {
	var itemname_div = friGetElementId('fb_itemname' + extension + facet_id);
	var expander_td = friGetElementId('fb_itemname_expander' + extension + facet_id);
	var facet_div = friGetElementId('fb_itemcontent' + extension + facet_id);
	
	if (expander_td != null && facet_div != null) {
		if (mode=='show') {
			if (itemname_div != null)
				itemname_div.className='facetcontrol-active';
			
			expander_td.className='fb-collapser';

			facet_div.className = 'facetlist-active';
		} else { //hide
			if (itemname_div != null)
				itemname_div.className='facetcontrol-normal';
			
			expander_td.className='fb-expander';
			
			facet_div.className = 'facetlist-inactive';
		}	
	}
}


/**
 * Sets a time-out message as the title of the name of the SRC service that
 * has timed out. 
 */
function fb_facetboard_set_timeoutinfo(src_service_id, skos_relation, base64codedWarning) {
	var fb_itemname_expander2id = 'fb_itemname_expander2_'+src_service_id;
	var fb_itemname_expander2 = friGetElementId(fb_itemname_expander2id);
	
	fb_itemname_expander2.setAttribute("class", "facetcontrol-td-timeout");
	fb_itemname_expander2.setAttribute("title", Base64.decode(base64codedWarning));
}


/**
 * Adds a list of terms (together with their URIs) to the facetboard explorer.
 */
function fb_add_to_facetboard(src_service_id, cached, skos_relation, base64codedterms, base64codedrawterms, base64lightcodedroots, existing_widgetsresults) {
	var fb_itemname_expander2id = 'fb_itemname_expander2_'+src_service_id;
	var fb_table_id = 'fb_table_'+fb_skos_relation2art(skos_relation)+'_'+src_service_id;
	var fb_counterobj_name = 'fb_itemcount_'+fb_skos_relation2art(skos_relation)+'_'+src_service_id;
	var fb_itemcount_name = 'fb_itemcount_'+src_service_id;
	
	var fb_itemname_expander2 = friGetElementId(fb_itemname_expander2id);
	var fb_table = friGetElementId(fb_table_id);
	var fb_counterobj = friGetElementId(fb_counterobj_name);
	var fb_general_itemcount = friGetElementId(fb_itemcount_name);
	
	var terms = (base64codedterms!='') ? Base64.decode(base64codedterms) : '';
	var terms_raw = base64codedrawterms;

  if (cached) //show SRC content is cached
  {	fb_itemname_expander2.setAttribute("class","facetcontrol-td cached");
    /*alert('set fb_itemname_expander2id cached');*/}
   else
	fb_itemname_expander2.setAttribute("class","facetcontrol-td");
	fb_itemname_expander2.setAttribute("title",'');

	if (fb_table) {
		if (terms) {
			var ranked_preterms = terms.split(',');
			var ranked_preterms_raw = terms_raw.split(',');
			var ranked_preroots = base64lightcodedroots.split('|');
      
      //alert('ranked_preroots: '+ranked_preroots[0])
      
			var ranked_root = new Array;
			var ranked_term = new Array;
			var ranked_term_raw = new Array;
			
			ranked_preterms.foreach(function(k, v) {
				if (v != '') {
					ranked_term[k] = v.trim();
					ranked_root[k] = ranked_preroots[k];

          if (ranked_preterms_raw != '') {
						ranked_term_raw[k] = Base64.decode(ranked_preterms_raw[k]).trim();
					} else {
						ranked_term_raw[k] = '';
					}
				}
			});
			
			fb_table.ranked_terms = ranked_term;
			fb_table.ranked_roots = ranked_root;
			fb_table.ranked_terms_raw = ranked_term_raw;
			
			fb_render_terms(fb_general_itemcount, fb_counterobj, fb_table, src_service_id, skos_relation, existing_widgetsresults);
			
			//Expand render of thesaurus
			var facetgroup_div_id = 'fb_itemcontent_'+src_service_id;
			var facetgroup_div=friGetElementId(facetgroup_div_id);
			fb_show_allItemContent(src_service_id,facetgroup_div);
		}
	}	
}

function fb_resetValidatedTermsList(src_service_id) {
	jQuery('#fb_itemcontent_v_' + src_service_id).hide();
	jQuery('#fb_table_v_' + src_service_id).empty();
	
}

/**
 * Resets the general item count for the SRC service and clears the
 * table and result count for the specified skos_relation.
 */
function fb_reset_src(src_service_id, skos_relation) {
	var fb_table_id = 'fb_table_' + fb_skos_relation2art(skos_relation) + '_' + src_service_id;
	var fb_counterobj_name = 'fb_itemcount_' + fb_skos_relation2art(skos_relation) + '_' + src_service_id;
	var fb_itemcount_name = 'fb_itemcount_' + src_service_id;
	var fb_table = friGetElementId(fb_table_id);
	var fb_counterobj = friGetElementId(fb_counterobj_name);
	var fb_general_itemcount = friGetElementId(fb_itemcount_name);
	var terms = '';

	fb_general_itemcount.innerHTML = '';
	
	if (fb_table) {
		fb_counterobj.innerHTML = '';
		fb_deleteRows(fb_table);
	} else {
		alert ('No obj '+fb_table_id);
	}
}





function fb_init_src_display(src_service_id, skos_relation) {
	var fb_itemcount_name = 'fb_itemcount_'+src_service_id;
	var fb_general_itemcount = friGetElementId(fb_itemcount_name);
	fb_general_itemcount.innerHTML = '';
	fb_reset_src(src_service_id, skos_relation);
		
	var fb_item_id="fb_item_"+skos_relation.substring(0,1)+'_'+src_service_id;
	//hide facetgroup-header to skos_relation
	//alert('ZERO HIDE '+fb_item_id);
	jQuery('#'+fb_item_id).each(function() {
		this.style.visibility='hidden';
		this.style.display='none';
	});
}



function fb_display_more_skos_facets(fb_tablebody_id, src_service_id,skos_relation)
{
	//hide seemore element and show hidden elements (fb-hidden)
	var fb_table=document.getElementById(fb_tablebody_id);
	//Hide seemore
	jQuery('.fb-seemore',fb_table).each(function() {
		jQuery(this).hide();
	});
	
	//Show hidden elements	
	jQuery('.fb-hidden',fb_table).each(function() {
		jQuery(this).removeClass('fb-hidden');
	});
	
}



function fb_set_node_ontofacet(label) {
	var ontofaceCenterTextField = document.getElementById('ontofacet_center');
	
	if (label) {
		ontofaceCenterTextField.value = label;
	} else {
		ontofaceCenterTextField.value = '';
	}
}

/**
 * A single character is used to represent skos_relations in the IDs of the
 * html elements composing the ontological facet explorer. The character
 * used is the first one of the skos_relation name string. 
 */
function fb_skos_relation2art(skos_relation) {
	if (skos_relation != '') {
		return skos_relation.substring(0, 1);
	} else {
		return skos_relation;
	}
}

function fb_deleteRows(table)
{
	var rowcnt= table.rows.length;
	for(var i=rowcnt - 1;i>0;i--)
		table.deleteRow(i);
}	


function fb_sort_rank(srcname,art)
{
	alert('fb_sort_rank('+srcname+','+art+'): COMING SOON :-)');
}


function fb_sort_alfa(srcname,art)
{
	alert('fb_sort_alfa('+srcname+','+art+'): COMING SOON :-)');
}

// **************
// * CLOUDBOARD *
// **************

function resetCloudBoard(userId) {
	var url = "<?php print $TAGCLOUDRESPONDER; ?>";
	url += "?pid=" + userId + "&skos_relation=reset";
	
	$p.ajax.call(url, {
		'type':'load',
		'callback': { 'function':fillCloudBoard, 'variables': {} }
	});
}

function refreshCloudBoard(userId) {
	var url = "<?php print $TAGCLOUDRESPONDER; ?>";
	var max = (jQuery("#sizeBySelect option:selected").attr("value") == "recency") ? 5 : 10;
	
	url += "?pid=" + userId + "&skos_relation=refresh&max=" + max + "&sizeby=" + jQuery("#sizeBySelect option:selected").attr("value");
	
	$p.ajax.call(url, {
		'type':'load',
		'callback': { 'function':fillCloudBoard, 'variables': {} }
	});
}

function fillCloudBoard(response, vars) {
	var cloudElement = response.documentElement;
	
	var nbOfResults = parseInt(cloudElement.getAttribute("count"));
	
	if (nbOfResults > 0) {
		var tagList = cloudElement.getElementsByTagName("tag");

		(function(jQuery){
			var cloudBoard = jQuery("#cloudBoardTags");
			cloudBoard.html('');

			if (tagList.length > 0) {
				var showAsList = cloudElement.getAttribute("kind") == "recency";
				
				if (showAsList) {
					var listElement = jQuery("<ul />");
					for (var i=0; i<tagList.length; i++) {
						var tag = tagList[i];
						var tagElement = createTagCloudItem(tag, i);
						var itemElement = jQuery("<li />");
						itemElement.append(tagElement);
						listElement.append(itemElement);
					}
					cloudBoard.append(listElement);

					jQuery("#tagCloudReloadButton").hide();
				} else {
					for (var i=0; i<tagList.length; i++) {
						var tag = tagList[i];
						var tagElement = createTagCloudItem(tag, i);
						cloudBoard.append(tagElement);
						cloudBoard.append(document.createTextNode(" "));
					}
					
					jQuery("#tagCloudReloadButton").show();
				}
			}
		})(jQuery);
	} else {
		(function(jQuery){
			var cloudBoard = jQuery("#cloudBoardTags");
			cloudBoard.html('');
			
			cloudBoard.append(document.createTextNode(lg("lblTagCloudEmpty")));
		})(jQuery);
	}
}

function createTagCloudItem(tag, position) {
  var setword="javascript: bc_clearBreadcrumbIfNeeded('" + tag.textContent + "'); fb_set_node_ontofacet('" + tag.textContent + "'); setMetaSearchInputText('" + tag.textContent + "');";
		
	var tagElement = jQuery("<a />", {
		class: "cloudTag color" + (position % 2),
		onClick: setword,
		ondblclick: setword+"document.getElementById('metasearchrodinbutton').click();",
		title: lg("titleClickOnTag"),
		style: "font-size: " + (12 + parseInt(tag.getAttribute("size")) + "px"),
		text: tag.textContent
	});
	
	return tagElement;
}

//**********
//* BOARDS *
//**********

function forceBoardExpanded(boardID) {
	(function(jQuery){
		var icon = jQuery("#" + boardID + " div.rodinBoardTitleBar img.toggleBoardIcon");
		var contentDiv = jQuery("#" + boardID + " div[name=\"boardContent\"]");
		
		contentDiv.removeClass("collapsedContent");
		icon.attr("src", "<?php print $RODINIMAGESURL; ?>/icon_collapse_board.gif");
		icon.attr("title", lg("minimize"));
	})(jQuery);
}

function toggleBoardExpanded(boardID) {
	(function(jQuery){
		var icon = jQuery("#" + boardID + " div.rodinBoardTitleBar img.toggleBoardIcon");
		var contentDiv = jQuery("#" + boardID + " div[name=\"boardContent\"]");
		
		var boardIsCollapsed = contentDiv.hasClass("collapsedContent");
		
		if (boardIsCollapsed) {
			contentDiv.removeClass("collapsedContent");
			icon.attr("src", "<?php print $RODINIMAGESURL; ?>/icon_collapse_board.gif");
			icon.attr("title", lg("minimize"));
			jQuery('#'+boardID).attr('title',lg("minimize"));
			jQuery(contentDiv).show();
		} else {
			contentDiv.addClass("collapsedContent");
			icon.attr("src", "<?php print $RODINIMAGESURL; ?>/icon_expand_board.gif");
			icon.attr("title", lg("restore"));
			jQuery('#'+boardID).attr('title',lg("restore"));
			jQuery(contentDiv).hide();
		}
	})(jQuery);
}

// ***************
// * BREADCRUMBS *
// ***************

/**
 * Show the breadcrumbs
 */
function bc_show() {
	(function(jQuery){
		var b = jQuery("#breadcrumbs");
		b.removeClass("breadCrumbsHidden");
	})(jQuery);
}

/**
 * Hide the breadcrumbs
 */
function bc_hide() {
	(function(jQuery){
		var b = jQuery("#breadcrumbs");
		b.addClass("breadCrumbsHidden");
	})(jQuery);
}

/**
 * Clears the breadcrumb and hides it
 */
function bc_clear_breadscrumbs() {
	(function(jQuery){
		var bc_terms = jQuery("#breadcrumbs_terms");
		bc_terms.html('');
	})(jQuery);

	bc_hide();
}

function bc_make_breadcrumb_term_element(term,channel,facettype,id) {
	var comment="Click to refilter or research without this term";
	switch(facettype)
	{
		case 'semfacet':
					comment="Click to re-search without this semantical facet";
					break;
		case 'wsearchfacet':
					comment="Click to re-search without this term";
					break;
		case 'elibfacet':
					comment="Click to re-filter without this term";
					break;
	}
	var channelclass='crumb-'+channel+'-normal '+facettype; /*two classes*/
	
	var termElement = document.createElement("a");
	termElement.setAttribute("href", "#");
	termElement.setAttribute("id", id);
	termElement.setAttribute("onmouseout", "javascript:this.className='" + channelclass + "';");
	termElement.setAttribute("class", channelclass);
	termElement.setAttribute("onclick", "javascript:bc_breadcrumb_remove('"+id+"');");
	termElement.setAttribute("title", comment);
	
	var termText = document.createTextNode(term);
	
	
	// Add the image suggesting the possibility of removing this term
	var imgElement = document.createElement("img");
	imgElement.setAttribute("src", "<?php print $RODINUTILITIES_GEN_URL; ?>/images/ico_close.gif");
	
	termElement.appendChild(termText);
	termElement.appendChild(imgElement);
	
	return termElement;
}

/**
 * Returns a string composed by all terms in the breadcrumb bar
 * joined by delimiter passed as parameter.
 */
function bc_get_terms(delimiter) {
	var queryText = '';
	jQuery('#breadcrumbs_terms a').each(function() {
		queryText += jQuery(this).text() + delimiter;
	});
	
	return queryText.substring(0, queryText.lastIndexOf(delimiter));
}

/**
 * Adds a term to the breadcrumbs, only if it is not already part of it.
 * 
 * @param term the term to be added
 * @param channel the source of the term: 'meta', 'zen', 'survista', 'onto' or 'result'
 */
function bc_add_breadcrumb_unique(term, channel,facettype) {
//	alert('bc_add_breadcrumb_unique('+term+'): Adding '+term+' ('+channel+') to breadcrumbs');
	var bc 	= friGetElementId('breadcrumbs_terms');
	var b 	= friGetElementId('breadcrumbs');
	//alert('bc_add_breadcrumb_unique - adding a '+facettype);
	term = term.trim();
	//term = removePunctuationMarks(term);
	
	if (term == "") {
		alert(lg("lblAfterCleaningEmptyTerm"));
		return;
	} else {
		// Check if term is already in breadcrumb
		var lowercaseterm = term.toLowerCase();
		var lowecasephrase = bc.innerHTML.toLowerCase();
		
		var lowercasequery = jQuery('#elibsearchinput').val().toLowerCase();

		if (lowecasephrase.match( '/^'+lowercaseterm+'$/')) {
			alert(lg('lblAlreadyInBreadCrumbs', term));
			return;
		} else if (lowercasequery.match(lowercaseterm)) {
			alert(lg('lblAlreadyInSearchQuery', term));
			return;
		}
		
		var id = randomUUID();
		var termObject = bc_make_breadcrumb_term_element(term, channel, facettype, id);
		var facetContainer = bc_make_semfacetDescription(channel,termObject,facettype);
		bc.appendChild(facetContainer);
			
		bc_show();
		
		/* re-move in all controls */
		bc_hide_in_control_panels(term.trim());
		
		/* Trigger some events on size of breadcrumb */
		bc_on_breadcrum_changed(b);
	}
}

/**
 * Creates a secondary search button
 * 
 * @deprecated The button is not longer used
 */
function make_bc_searchbutton() {
	var button = document.createElement("input");
	button.setAttribute("type", "button");
	button.setAttribute("name", "rodinbcsearchbutton");
	button.setAttribute("id", "rodinbcsearchbutton");
	button.setAttribute("title", lg("lblBreadScrumbSearchBTitle"));
	button.setAttribute("value", lg("lblBreadScrumbSearchBLable"));
	button.setAttribute("onclick", "javascript:return launch_fri_metasearch(bc_get_terms(),document.getElementById('rodinsearch_m').value,-1,-1,2,true,false,window.\$p);");
	button.setAttribute("style", "font-weight:bold;padding:2px;visibility:hidden;display:none;");
	
	return button;
}


function bc_check_delete(bc,term,channel)
/*
 * Check if a node with channel is in bc.innerHTML
 * and if yes, delete the part && return true
 */
{
	//alert('bc_check_delete:\n\n'+term);

	var str=bc.innerHTML.trim();
	var channelclass='crumb-'+channel+'-normal';
	var JOKER_IN_NODE1='(.{100,150})';/* 141 empirical !! */
	var JOKER_IN_NODE2='(.{100,200})';/* 112 each node empirical without term!! */
	var term1='<a href=\"#\"';
	var term2='\<\/a>';
	var term3='&nbsp;'; /* is also added with <a> */
	var pattern=term1+JOKER_IN_NODE1+channelclass+JOKER_IN_NODE2+term2+term3; 
	var re = new RegExp(pattern); /* 234 empirical without term!! */
	alert('PATTERN: ('+pattern+') \n\nIn STRING:\n\n('+str+')');
	var match = str.match(re);
	var res = false;
	var str_rest='';
	var match_term=term1+RegExp.$1+channelclass+RegExp.$2+term2+term3;	
	if (match) 
	{	res=true;
		//alert('MATCH:\n\n'+match_term+' channel='+channel);
		bc.innerHTML = str.replace(match_term,""); /*delete it*/
		str_rest = bc.innerHTML.replace('&nbsp;',''); /* kill blanks */
	}
	//alert('bc_check_delete returns ('+res+')');
	return res;
}


/**
 * Removes the term with the given id from the breadcrumb.
 * It also checks if the resulting breadcrumb is empty and
 * if so hides it.
 */
function bc_breadcrumb_remove(id) {
	var bc = friGetElementId('breadcrumbs_terms');
	var bt = friGetElementId('breadcrumbs_title');
	var b = friGetElementId('breadcrumbs');
	var txt = jQuery("#"+id).text();
	
	jQuery("#s"+id).remove(); /*the corr container*/
	var elements = bc.getElementsByTagName("a");
	
	if (elements.length < 1) {
		bc_hide();
	}
	
	/* re-add in all controls */
	bc_show_in_control_panels(txt.trim());
	
	refilter_widgets();
}

/**
 * Takes the value given as parameter and compares it to the
 * value held in parent.OLDSEARCH. If no change can be detected,
 * the breadcrumb is kept.
 * 
 * @param currTextValue
 */
function bc_clearBreadcrumbIfNeeded(currTextValue) {
	var oldText = bc_getMetaSearchText().trim();
	var newText = currTextValue.trim();
	
	if (oldText !== newText) {
		bc_clear_breadscrumbs();
		$p.app.widgets.closeSurvista();
	}
}

function bc_registerMetaSearchText(currTxtValue) {
	parent.OLDSEARCH = currTxtValue;
}

function bc_getMetaSearchText() {
	return parent.OLDSEARCH;
}

/**
 * Creates and returns a span obj containing label and a crumb
 */
function bc_make_semfacetDescription(channel,termObject,facettype)
{
	var id = termObject.getAttribute('id');
	var spanElement = document.createElement("span");
	spanElement.setAttribute("class", "crumbssemfacetdesc");
	spanElement.setAttribute("id", 		"s"+id);
	spanElement.setAttribute("ftype", facettype);
	
	var labelElement = document.createElement("label");
	labelElement.innerHTML= channel+":";
	labelElement.setAttribute("class", "crumbssemfacetdesc");
	
	spanElement.appendChild(labelElement);
	spanElement.appendChild(termObject);
	
	return spanElement;
}	

//*************
//* UTILITIES *
//*************

/**
 * Accesses the lg() function from a secondary script
 * @param id
 * @returns {String}
 */
function friLG(id) {
	var comment = '';
	
	if (eval("typeof(lg) == 'function'")) {
		comment = lg(id);
	} else { 
		comment = parent.lg(id);
	}
	
	return comment;
}

/**
 * Returns the whole text for the query, this includes
 * the breadcrumb words if set.
 */
function get_search_text() {
	var delimiter = ', ';
	
	var searchFieldText = jQuery('#elibsearchinput').val();
	var breadCrumbsText = bc_get_terms(delimiter);
	
	if (breadCrumbsText != '')
		return searchFieldText + delimiter + breadCrumbsText;
	else
		return searchFieldText;
}

/**
 * Gets an element from the document or its parent.
 * TODO Should be reimplemented using jQuery and probably discarded.
 * 
 * @param id the id of the element to retrieve
 * @returns the element
 */
function friGetElementId(id) {
	var x = document.getElementById(id);
	if (x == null) {
		x = parent.document.getElementById(id);
	}
	
	return x;
}




/**
 * Called by bc_breadcrumb_remove
 * on remove word in breadcrumb.
 * Re-add breadcrum icon in action tooltip if open (pinned)
 * @param txt - the text of a breadcrumb TAG
 */
function bc_show_in_control_panels(txt)
{
		var label_txt='?';
	//alert('bc_show_in_control_panels('+txt+')');
	/*readd in tooltip if opened and if contains txt*/
	jQuery("div#elib_tooltip label#ttp.ttp:contains(\""+txt+"\")").each(function(){
		jQuery("div#elib_tooltip td#a2bc.ttpi").removeClass('hidden').show();
	});
	
	/*readd in semantic facet if existing*/
	jQuery("tr.fb-term-row:contains(\""+txt+"\")").each(function(){
		label_txt = jQuery(this).attr('st'); 
		if (label_txt == txt)
			jQuery("img.ontofacetterm",this).removeClass('hidden').show();
	});
}


/**
 * Called by bc_add_breadcrumb_unique or from facet board onmouseover
 * on adding word in breadcrumb.
 * Re-move breadcrum icon in action tooltip if open (pinned)
 * @param txt - breadcrumb TAG
 */
function bc_hide_in_control_panels(txt)
{
	//alert('bc_hide_in_control_panels('+txt+')');
	/*hide in tooltip: */
	var label_txt='?';
	jQuery("div#elib_tooltip label#ttp.ttp:contains(\""+txt+"\")").each(function(){
		jQuery("div#elib_tooltip td#a2bc.ttpi").addClass('hidden').hide();
	});
	/*hide in semantical facettes: */
	jQuery("tr.fb-term-row:contains(\""+txt+"\")").each(function(){
		/*real on exact match!!!*/
		label_txt = jQuery(this).attr('st'); 
		
		//Hide only if exact match
		if (label_txt == txt)
			jQuery("img.ontofacetterm.bc",jQuery(this)).addClass('hidden').hide();
	});
}


/**
 * Called from facet board onmouseover
 * Shows the bc icon on exact txt match (current_facet term selection against some breadcrumbs TAGS)
 * @param txt - breadcrumb TAG
 */
function fb_check_bc_control_on_breadcrumb_matching(tr, hideonmatch)
{
	//alert('fb_check_bc_control_on_breadcrumb_matching('+txt+')');
	/*hide in tooltip: */
	var current_selection_txt = jQuery(tr).attr('stc'); /* ctl the stopwordcleaned version of the facet term */
	var imgselector="td.icons img.bc";
	var bc_img = jQuery(imgselector, tr);
	var tagterm = '?';
	var operationfound = false;
	
	if(hideonmatch)
	{
		/*if current_selection is not in breadcrumbs tags -> show bc in semantical facettes, otherwise hide it */
		/*
		 * The jQuery string comparison = does not function here !!!!
		 * must use a hand made coding
		 *jQuery("#breadcrumbs_terms a[text='"+current_selection_txt+"']").each(function(){
		 */
		jQuery("#breadcrumbs_terms a").each(function(){
			if (jQuery(this).text()==current_selection_txt)
			{
				operationfound=true;
				jQuery("img.ontofacetterm.bc",tr).addClass('hidden').hide();
			}
		});
		//If nothing selected: show bc icon 
		if (!operationfound)
		{
			jQuery("img.ontofacetterm.bc",tr).removeClass('hidden').show();
		}
	} // hideonmatch
	
	
	else // !hideonmatch
	{
		/*if current_selection is not in breadcrumbs tags -> show bc in semantical facettes, otherwise hide it */
		jQuery("#breadcrumbs_terms a").each(function() 
		{
			if (jQuery(this).text()==current_selection_txt)
			{
				operationfound=true;
				jQuery("img.ontofacetterm.bc",tr).removeClass('hidden').show();
			}
		});
		
		//If nothing selected: show bc icon 
		if (!operationfound)
		{
			jQuery("img.ontofacetterm.bc",tr).removeClass('hidden').show();
		}
	} // !hideonmatch
}



