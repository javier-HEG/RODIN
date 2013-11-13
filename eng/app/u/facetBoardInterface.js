// HEG - RODIN
// Javascript Functions
// Autor: Fabio Ricci
// fabio.ricci@ggaweb.ch
// Date : 1.1.2010

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


/**
 * Extracts terms from fb_tablebody and adds them in the table-list
 * that corresponds to the skos_relation specified.
 */
function fb_render_terms(	fb_general_itemcount, fb_counterobj, fb_tablebody, src_service_id, skos_relation, existing_widgetsresults) {
	var fb_tablebody_id = fb_tablebody.id;
	var ranked_term = fb_tablebody.ranked_terms;
	var ranked_term_raw = fb_tablebody.ranked_terms_raw;
  var ranked_roots = fb_tablebody.ranked_roots;
	var rodinsegment = '<?php echo $RODINSEGMENT; ?>';
	var rankinfos = fb_tablebody.rows[0];
	var tBody = fb_tablebody.getElementsByTagName('tbody')[0];
	var showmaxlistelems = <?php echo $FACETBOARDSHOWMAXLISTELEMS; ?>;
	var fb_item_id="fb_item_"+skos_relation.substring(0,1)+'_'+src_service_id;
	
	// Update SRC global count of results
	var general_count = 0;
	if (/(\d+)\s\w+/.test(fb_general_itemcount.innerHTML)) {
		var match = /(\d+)\s\w+/.exec(fb_general_itemcount.innerHTML);
		general_count = parseInt(match[1]);
	}
	
	general_count = general_count + ranked_term.length;
	//alert('skos_relation= '+skos_relation+'\n\nranked_term.length='+ranked_term.length+'\n\n'+ranked_term);
	
	if (general_count > 0) {
		if (general_count > 1)
			fb_general_itemcount.innerHTML = lg('lblOntoFacetsFound', general_count);
		else
			fb_general_itemcount.innerHTML = lg('lblSingleOntoFacetFound', general_count);
	}

	// Update local count of found terms
	if (ranked_term.length > 0) {
		if (ranked_term.length > 1)
			fb_counterobj.innerHTML = lg('lblOntoFacetsFound', ranked_term.length);
		else
			fb_counterobj.innerHTML = lg('lblSingleOntoFacetFound', ranked_term.length);
	}
	// always hide skos_relation count (to be shown on closed item)
	jQuery(fb_counterobj).hide();
	
	
	// Add each new element to table up to showmaxlistelems

	var limit=Math.min(showmaxlistelems,ranked_term.length);
	//alert('limit='+limit+'\nranked_term.length='+ranked_term.length);
	
	for (var i = 0; i < limit; i++) {
		//alert('this term: '+ranked_term[i]+'\n\nranked terms: ('+ranked_term+')')
		termTableRow = generate_ontofacet(ranked_term[i], ranked_term_raw[i], ranked_roots[i], fb_tablebody_id, src_service_id, i, skos_relation, parent.LANGUAGE_OF_RESULT_CODED, rodinsegment, false, false, 0, existing_widgetsresults);
		tBody.appendChild(termTableRow);		
	}
		
	var delta = ranked_term.length - limit;
	
	//Special case 1 further term: Allow it to go with the others...
	if (delta==1)
	{
		i=limit;
		//alert('allow one more term to be displayed at idx='+i+' skos_relation:'+skos_relation)
		termTableRow = generate_ontofacet(ranked_term[i], ranked_term_raw[i], ranked_roots[i], fb_tablebody_id, src_service_id, i, skos_relation, parent.LANGUAGE_OF_RESULT_CODED, rodinsegment, false, false, 0, existing_widgetsresults);
		tBody.appendChild(termTableRow);
	}
	else
	if (delta > 0 )
	{
		i='more';
		var plural = (delta==1?'':'s');
		var itemtxt = 'Show all terms (+'+delta+')...';
		
		
		termTableRow = generate_ontofacet(itemtxt, '', null, fb_tablebody_id, src_service_id, i, skos_relation, '', rodinsegment, true, false, delta, existing_widgetsresults);
		tBody.appendChild(termTableRow);
		
		//Add the remaining terms but hidden!
		for (var i = limit; i < ranked_term.length; i++) {
			termTableRow = generate_ontofacet(ranked_term[i], ranked_term_raw[i], ranked_roots[i], fb_tablebody_id, src_service_id, i, skos_relation, parent.LANGUAGE_OF_RESULT_CODED, rodinsegment, false, true, 0, existing_widgetsresults);
			tBody.appendChild(termTableRow);
		}
	}
	
	//show facetgroup-header to skos_relation if there were at least one facet:
	if (ranked_term.length)
	{
		jQuery('#'+fb_item_id).each(function() {
			this.style.visibility='visible';
			this.style.display='block';
		});
	}
}


/*
 * FACETBOARD CONSTRUCTION
 * Generates the table row for the given term.
 */
function generate_ontofacet(term, ranked_term_raw, rootbase46, fb_tablebody_id, src_service_id, i, skos_relation, lang, rodinsegment, ismore, ishidden, counthiddenterms, existing_widgetsresults) 
{
	var of_icons_id="ricons_"+skos_relation.substring(0,1)+'_'+src_service_id+'_'+i;
	var tableRow = document.createElement('tr');
	
	if (ismore)
	{
	}
	else
	{
		tableRow.setAttribute("onmouseover", "document.getElementById('"+of_icons_id+"').style.visibility='visible'");
		tableRow.setAttribute("onmouseout", "document.getElementById('"+of_icons_id+"').style.visibility='hidden'");
	}
	if (ishidden) //Make row unvisible
		tableRow.setAttribute("class", "fb-term-row fb-hidden");
	else
	{
		if (ismore)
		{
			tableRow.setAttribute("class", "fb-seemore");
		}
		else
		{
			tableRow.setAttribute("class", "fb-term-row");
		}
	}
	
	var tempTableCell = document.createElement('td');
	tempTableCell.setAttribute("align","left");
	
	var termLink = document.createElement('a');
	if (ismore) 
	{
		tempTableCell.setAttribute("class", "fb-ismore");
		termLink.setAttribute("class", "fb-ismore");
		
		//var moretitle=lg('lblOntoFacetsMoreskos_relations').replace('xxx',counthiddenterms);
		//termLink.setAttribute("title", moretitle);
		termLink.setAttribute("onclick", "fb_display_more_skos_facets('"+fb_tablebody_id+"','"+src_service_id+"','"+skos_relation+"')");
	}
	else
	{
		termLink.setAttribute("class", "fb-term");
		termLink.setAttribute("title", lg('lblOntoFacetsTermskos_relations'));
	}
	termLink.innerHTML = term;
	tempTableCell.appendChild(termLink);
	tableRow.appendChild(tempTableCell);
	
	// A new TD for buttons
	tempTableCell = document.createElement('td');
	tempTableCell.setAttribute("align", "right");
	tempTableCell.setAttribute("id", of_icons_id);
	tempTableCell.style.visibility='hidden';

	//Add both ex menu items here too
	//re-search-in-onto
	var rio_img_src='<?php echo $RODINIMAGESWEB; ?>/magnifier-onto-small.png';
	var researchontoButton = document.createElement('img');
  researchontoButton.setAttribute("src", rio_img_src);
	researchontoButton.setAttribute("class", "ontofacetterm");
  researchontoButton.setAttribute("title", lg("lblSurvistaExploreOntoFacets"));
  researchontoButton.setAttribute("onClick", "exploreInOntologicalFacets('"+term+"',null)");
  tempTableCell.appendChild(researchontoButton);
	

	//add-to-breadcrumb:
	var atb_img_src='<?php echo $RODINIMAGESWEB; ?>/add-to-breadcrumb.png';
	var add2breadcrumbButton = document.createElement('img');
  add2breadcrumbButton.setAttribute("src", atb_img_src);
	add2breadcrumbButton.setAttribute("class", "ontofacetterm");
  add2breadcrumbButton.setAttribute("title", lg("lblSurvistaAddToBreadcrumb"));
  add2breadcrumbButton.setAttribute("onClick", "bc_add_breadcrumb_unique('"+term+"','result')");
  tempTableCell.appendChild(add2breadcrumbButton);

	if (rootbase46 && !ismore)
  {
    // Show the rank button ONLY IF THERE ARE MORE THAN 2 WIDGET RESULTS PRESENT
    var rankButton = document.createElement('img');
    
    //Add content only if existing_widgetsresults, otherwise leave img with class
	  rankButton.setAttribute("onClick", "javascript:src_widget_morelikethis(this,'"+ rootbase46 + "', '" + term+ "', '" + lang + "');");
    rankButton.setAttribute("title", lg("lblClick2RankResults", term));
    if(existing_widgetsresults)
    {
		  rankButton.setAttribute("class", "ontofacetterm4exwr");
		}
		else
		  rankButton.setAttribute("class", "ontofacetterm4exwr hidden");
	
		rankButton.setAttribute("src", "../../../gen/u/images/funnel.png");
    tempTableCell.appendChild(rankButton);
  }
  
  // Show the Survista button only if the raw form is a ZBW URI or a DBPedia category
	if (ranked_term_raw && !ismore && (ranked_term_raw.indexOf("http://zbw") >= 0 || ranked_term_raw.indexOf("http://dbpedia.org/resource/Category:") >= 0)) {
	  var survistaButton = document.createElement('img');
    
		survistaButton.setAttribute("src", "../../../gen/u/images/survista-icon.png");
		survistaButton.setAttribute("class", "ontofacetterm");
		survistaButton.setAttribute("title", lg("lblOntoFacetsShowOnSurvista", term));
		survistaButton.setAttribute("onClick", "javascript:$p.app.widgets.placeSurvista('" + ranked_term_raw + "', '" + term+ "', '" + lang + "','" + rodinsegment+"');");
		
		tempTableCell.appendChild(survistaButton);
	}
  
	tableRow.appendChild(tempTableCell);

	return tableRow;
}

/**
 * Checks widget results and update menus on ontobox items
 * Allo=shows/Disallow=hide specific menuitems
 */
function fb_updatefacettermsctxmenuitems4exwr()
{
	//alert('fb_updatefacettermsctxmenuitems4exwr');
	var existing_widgetsresults = getNumberOfExistingWidgetResults() > 1;
	var img_facettermctxmenuitem4exwr_nodelist = document.querySelectorAll(".ontofacetterm4exwr");
	if (img_facettermctxmenuitem4exwr_nodelist.length)
	{
		for(var i=0;i<img_facettermctxmenuitem4exwr_nodelist.length;i++)
		{
			var img = img_facettermctxmenuitem4exwr_nodelist[i];
			if (existing_widgetsresults) 
				jQuery(img).removeClass('hidden');
			else
				jQuery(img).addClass('hidden');
		}
	}
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
		jQuery(this).removeClass('fb-hidden')
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

function bc_make_breadcrumb_term_element(term,channel,id) {
	var comment=friLG("lblClick2Del0bcrumbs");
	var channelclass='crumb-'+channel+'-normal';
	
	var termElement = document.createElement("a");
	termElement.setAttribute("href", "#");
	termElement.setAttribute("id", id);
	termElement.setAttribute("onmouseout", "javascript:this.className='" + channelclass + "';");
	termElement.setAttribute("class", channelclass);
	termElement.setAttribute("onclick", "javascript:bc_breadcrumb_remove('"+id+"');");
	termElement.setAttribute("title", comment);
	
	// Adding a trailing space so that the breadcrumb text can wrap if necessary.
	var termText = document.createTextNode(term + " ");
	termElement.appendChild(termText);
	
	// Add the image suggesting the possibility of removing this term
	var imgElement = document.createElement("img");
	imgElement.setAttribute("src", "<?php print $RODINUTILITIES_GEN_URL; ?>/images/ico_close.gif");
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
function bc_add_breadcrumb_unique(term, channel) {
//	alert('bc_add_breadcrumb_unique('+term+'): Adding '+term+' ('+channel+') to breadcrumbs');
	var bc = friGetElementId('breadcrumbs_terms');
	var b = friGetElementId('breadcrumbs');
	
	term = term.trim();
	term = removePunctuationMarks(term);
	
	if (term == "") {
		alert(lg("lblAfterCleaningEmptyTerm"));
		return;
	} else {
		// Check if term is already in breadcrumb
		var lowercaseterm = term.toLowerCase();
		var lowecasephrase = bc.innerHTML.toLowerCase();
		
		var lowercasequery = jQuery('#rodinsearch_s').val().toLowerCase();

		if (lowecasephrase.match(lowercaseterm)) {
			alert(lg('lblAlreadyInBreadCrumbs', term));
			return;
		} else if (lowercasequery.match(lowercaseterm)) {
			alert(lg('lblAlreadyInSearchQuery', term));
			return;
		}

		/* No longer needed judging by the usability document */
		var searchButton = bc.getElementById("rodinbcsearchbutton");
		if (!searchButton) {
			bc.appendChild(make_bc_searchbutton());
			searchButton = bc.getElementById("rodinbcsearchbutton");
		}

		var id = randomUUID();

		var termObject = bc_make_breadcrumb_term_element(term, channel, id);
		bc.insertBefore(termObject, searchButton);
			
		bc_show();
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
	alert('PATTERN: ('+pattern+') \n\nIn STRING:\n\n('+str+')')
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
	
	var element = bc.getElementById(id);
	if (element) {
		bc.removeChild(element);
	}
	
	var elements = bc.getElementsByTagName("a");
	
	if (elements.length < 1) {
		bc_hide();
	}
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
	
	var searchFieldText = jQuery('#rodinsearch_s').val();
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
