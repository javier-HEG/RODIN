/**
 * fabio.ricci@semweb.ch
 * calls and sets the result of RODIN/web
 * into the content div
 * involve also semantical and elib facets
 */
function show_rodin_search_results(querytext,debug)
{
	//querytext = querytext.trim();
	LASTUSERQUERY=querytext;
	var url="./rodinelib_search_and_render.php"
					+"?query="+encodeURIComponent(querytext)
					+"&DEBUG="+debug;
	clear_mainsearch();
	set_busy();

	//alert(url);
	jQuery.ajax({
		context: document.body,
    type: "GET",
    url: url,
    cache: true
	}).done(function( resultdata ) {
     //alert(querytext+" --> "+resultdata);
     jQuery('#content').html(resultdata);
     init_mainsearch();
     unset_busy();
     METASEARCH_FINISHED=true;
	});
}


/**
 * get data and put it into rodin_main_column (basta)
 */
function show_rodin_mlt(id_wdoc, sid, txt, oldqueery, debug)
{
	//alert('show_rodin_mlt txt=('+txt+')');
	var url="./rodinelib_result_filter_and_rerender.php?sid="+sid
															+"&rid="+id_wdoc
															+"&notify=1"
															+"&txt="+txt
															+"&previousquery="+oldqueery
															+"&DEBUG="+debug;
	widgets_refilter_and_display(url);
}




function refilter_widgets()
{
	filter_rodin_widgets_using_facets(getSid(), getSearchTerm(), 0);
}


function getSid()
{
	return jQuery('#sid').val();
}

function getSearchTerm()
{
	return jQuery('#elibsearchinput').val();
}



/**
 * get data and put it into rodin_main_column (basta)
 */
function filter_rodin_widgets_using_facets(sid, oldquery, debug)
{
	//collect all semantical facets:
	var semfacets_txts=collect_facets_terms('semfacet');
	//collect all widget facets:
	var wdgfacets_txts=collect_facets_terms('wsearchfacet');
	//collect all elib facets:
	var elibfacets_txts=collect_facets_terms('elibfacet');
	//alert('show_rodin_mlt txt=('+txt+')');
	
	var txt = 'Results found on current search using additionally ';
	
	if (semfacets_txts.length + wdgfacets_txts.length + elibfacets_txts.length == 0)
	txt = 'Normal results';
	
	if (semfacets_txts.length)
		txt+='semantical facet(s) '+semfacets_txts;
	if (wdgfacets_txts.length)
	{ 
		txt+=semfacets_txts?' and ':'';
		txt+='search term(s) '+wdgfacets_txts;
	}
	if (elibfacets_txts.length)
	{ 
		txt+=semfacets_txts?' and ':'';
		txt+='elib facet(s) '+elibfacets_txts;
	}
	
	var url="./rodinelib_result_filter_and_rerender.php"
											+"?sid="+sid
											+"&sfacets="+semfacets_txts	.join('|')
											+"&wfacets="+wdgfacets_txts	.join('|')
											+"&efacets="+elibfacets_txts.join('|')
											+"&notify=1"
											+"&txt="+encodeURIComponent(txt)
											+"&previousquery="+encodeURIComponent(oldquery)
											+"&DEBUG="+debug;
	widgets_refilter_and_display(url);
}






/**
 *  refilter widget area using url
 */
function widgets_refilter_and_display(url)
{
	var debug=0;
	if(debug) {
		alert('widgets_refilter_and_display:\n\n'+url);
	}
	
	clear_mainsearch();
	set_busy();
	jQuery.ajax({
		context: document.body,
    type: "GET",
    url: url,
    cache: true
  }).done(function( resultdata ) {
     //alert(querytext+" --> "+resultdata);
     jQuery('#rodin_main_column').html(resultdata);
     init_mainsearch();
     unset_busy();
     METASEARCH_FINISHED=true;
     
     jQuery('tr.wnotification').show();
		jQuery('table.onotification').show();

  });
}




function show_rodin_thesearch_results(querytext,debug)
{
	//return; // debug
	//alert('show_rodin_thesearch_results('+querytext+')');
	if (!debug) debug='';
	var url="./rodinelib_thesearch_and_render.php?query="+querytext+"&DEBUG="+debug;
	
	//alert(url);
	set_busy();
	jQuery.ajax({
		context: document.body,
    type: "GET",
    url: url,
    cache: true
  }).done(function( resultdata ) {
     //alert(querytext+" --> "+resultdata);
     jQuery('#rodin_left_column').html(resultdata);
     //The following disabled - toomuch
     //mark_ontoterms_on_resultmatch();
     unset_busy();
  });
}







function show_rodinelib_elib_facets(querytext,debug)
{
	//return; // debug
	//alert('show_rodin_thesearch_results('+querytext+')');
	if (!debug) debug='';
	var url="./rodinelib_elib_facets_search_and_render.php?query="+querytext+"&DEBUG="+debug;
	set_busy();
	jQuery.ajax({
		context: document.body,
    type: "GET",
    url: url,
    cache: true
  }).done(function( resultdata ) {
     //alert(querytext+" --> "+resultdata);
     jQuery('#rodin_right_column').html(resultdata);
     unset_busy();
  });
}





function clear_mainsearch()
{
	$('#zoomcontroldiv').hide();
	$('#zoomcontrol').hide();
	hide_autocomplete();
	elib_tooltip_hide();
}

function init_mainsearch()
{
	$('#zoomcontroldiv').show();
	/*if there are some results -> show button */
	$('.elibresult').each(function(){
		$('#zoomcontrol').show();
	});
	
	jQuery('tr.wnotification').hide();
	jQuery('table.onotification').hide();
	
	hide_autocomplete();
	show_results_light();
}




function set_busy()
{
	EXECSEMAPHOR-= 1;
	$('#header div.searchlupe').removeClass('wait4userinput').addClass('denyuserinput');
	$('#elibsearchinput').removeClass('wait4userinput').addClass('denyuserinput');
	$('#block_on_busy').fadeIn();;
}

function unset_busy()
{
	EXECSEMAPHOR+= 1;
	//Set back only if 0
	if (EXECSEMAPHOR==0)	{ 
		$('#header div.searchlupe').removeClass('denyuserinput').addClass('wait4userinput');
		$('#elibsearchinput').removeClass('denyuserinput').addClass('wait4userinput');
		$('#block_on_busy').fadeOut();;
	}
}


function hide_autocomplete()
{
	if (AUTOCOMPLETECONTAINER_ID)
	{
		var autocomplete= document.getElementById(AUTOCOMPLETECONTAINER_ID);
		if (autocomplete)
		{
			autocomplete.style.display='none';
			//DEBG TEST:	
			//alert('hide_autocomplete')
		}
		else 
		{
			autocomplete= parent.document.getElementById(AUTOCOMPLETECONTAINER_ID);
			autocomplete.style.display='none';
		}
	}
}






function toggle_show_abstracts_in_results(imgButton)
{
	//$('#'+imgButton.id).toggleClass('elibresvisibleabstracts elibreshiddenbstracts');
	
	if (imgButton.getAttribute('class')=='showingAbstracts')
	{
		//Set imgButton to shownAbstract
		//Set other button to hidingAbstract
		//Show abstracts
		// ----------------------------
		// Set button to shownAbstracts
		imgButton.setAttribute('class','shownAbstracts');
		//Set other button to hidingAbstracts
		$('#zoomcontrol_light')
			.addClass('hidingAbstracts')
			.removeClass('hiddenAbstracts');
		//Show abstracts
		show_results_full();
	}
}

function show_results_full()
{
	$('.elibreshiddenabstract')
		.addClass('elibresvisibleabstract')
		.removeClass('elibreshiddenabstract');
}

function show_results_light()
{
	$('.elibresvisibleabstract')
			.removeClass('elibresvisibleabstract')
			.addClass('elibreshiddenabstract');
}


function toggle_hide_abstracts_in_results(imgButton)
{
	//$('#'+imgButton.id).toggleClass('elibresvisibleabstracts elibreshiddenbstracts');
	
	if (imgButton.getAttribute('class')=='hidingAbstracts')
	{
		//Set imgButton to hiddenAbstract
		//Set other button to showingAbstract
		//Hide abstracts
		// ----------------------------
		// Set button to hiddenAbstracts
		imgButton.setAttribute('class','hiddenAbstracts');
		//Set other button to showingAbstracts
		$('#zoomcontrol_full')
			.addClass('showingAbstracts')
			.removeClass('shownAbstracts');
		
		//Hide abstracts
		show_results_light()
	}
}



function launch_query_for_debug()
{
	set_query('"Information Economy" AND Time'); 
	launch_query();
}

function set_query(searchterm)
{
	//alert('launch_query_for_debug()');
	$('#elibsearchinput').each(function(){
		$(this).val(searchterm);
	});
}

function launch_query()
{
	$('div.searchlupe').each(function(){
		$(this).trigger('click');
	});
}



/**
 * Removes from the given text most non-alphanumeric characters.
 * @param text
 * @returns
 */
function removePunctuationMarks(text) {
	var cleanText = text.replace(/[\.,-\/#!$%\^&\*;:{}=\-`~()]/g, " ").replace(/\s+/g, " ");
	return cleanText.trim();
}


/**
 * construct a menu for every widget word
 * @param txt
 * @param objs - an array of text objs
 */
function construct_elib_actions_tooltip(txt,objs)
{
	//erase <y> </y> tags in txt
	txt=txt.replace("<y>","").replace("</y>","");
	var tooltip_nl='<?php print $TTPNEWLINE;?>';
	var title_tooltip="This tooltip shows you available actions (on its above leftside) to be started using the text &#171;"+txt+"&#187;"
									 +tooltip_nl+tooltip_nl+"Start the desired action by clicking on the corresponding icon"
									 +tooltip_nl+"or leave the tooltip by hovering on another area; "
									 +"You might also want to pin or close explicitly this actions tooltip "
									 +"by clicking on the apposites icons on the above rightside of this tooltip";
;
	var title_add_to_breadcrumb="Click to use as a filter";
	var title_filter_wresults_containing="Click to filter results containing &#171;"+txt+"&#187;";
	var title_close_action_menu="Click to close (and unpin) this actions tooltip";
	var title_pin_action_menu="Click to (un)pin this actions menu"+tooltip_nl+"This will fix the selection and leave the tooltip open";
	var title_search_with_this_text_go="Click to search directly for &#171;"+txt+"&#187;";
	var title_search_with_this_text="Click to set &#171;"+txt+"&#187; as search text";
	var title_onto_explore="Click to explore facets using &#171;"+txt+"&#187;";

	var a2bchideclass='';
	/*Add to action emu only if not present in breadcrumbs or in searchtext:*/
	if(term_contained_in_search_or_bc(txt))
	{
		a2bchideclass='hidden';
	}
	
	var html=
"<table class='ttp' cellpadding=0 cellspacing=0 border=1 width='100%' title='"+title_tooltip+"'> \
<tr class='ttp'> \
<td class='ttpleft'> \
<table class='ttpleft' cellpadding=0 cellspacing=0><tr class='ttpleft'> \
<td class='ttpi'> \
 <a onclick='restrict_render(\""+txt+"\")' title='"+title_filter_wresults_containing+"'><img class='ttp' src='../../../../gen/u/images/funnel.png' /></a> \
</td> \
<td id='a2bc' class='ttpi "+a2bchideclass+"'> \
 <a onclick='wdg_do_bc(\""+txt+"\",\"search term\",\"wsearchfacet\");' title='"+title_add_to_breadcrumb+"'><img class='ttp' src='../../../../gen/u/images/add-to-breadcrumb.png' /></a> \
</td> \
<td class='ttpi'> \
 <a onclick='extra_rodin_thesearch_results(\""+txt+"\",<?php print $DEBUG; ?>)' title='"+title_onto_explore+"'><img class='ttp' width='17' src='../../../../gen/u/images/magnifier-onto.png' /></a> \
</td> \
<td class='ttpi' > \
 <a onclick='set_query(\""+txt+"\");launch_query();' title='"+title_search_with_this_text_go+"'><img class='ttp' width='17' src='../img/input_right_search_hover.png' /></a> \
</td> \
</tr></table> \
</td> \
<td class='ttpright'> \
<table class='ttpright' cellpadding=0 cellspacing=0><tr class='ttpright'> \
<td class='ttpit' > \
 <a onclick='toggle_pin_tooltip(this)' title='"+title_pin_action_menu+"'><img class='ttp' src='../../../../gen/u/images/pin18.png' /></a> \
</td> \
<td class='ttpit' > \
 <a onclick='xstooltip_hide(\"elib_tooltip\",true)' title='"+title_close_action_menu+"'><img class='ttp' id='close' src='../../../../gen/u/images/ico_close.gif' /></a> \
</td> \
</tr></table></td> \
</tr> \
<tr class='ttp'> \
<td class='ttp' colspan=4> \
 <label id='ttp' class='ttp'>"+txt+"</label> \
</tr> \
</table>";
			
		return html;
}




function term_contained_in_search_or_bc(txt)
{
	txt=txt.toLowerCase();
	var contained = false;
	jQuery(".semfacet:contains(\""+txt+"\")").each(function(){
		contained=true;
	});
	jQuery(".wsearchfacet:contains(\""+txt+"\")").each(function(){
		contained=true;
	});
	jQuery(".elibfacet:contains(\""+txt+"\")").each(function(){
		contained=true;
	});
	
	if (!contained)
	{ /* jQuery only so */
		contained=jQuery("#elibsearchinput").val().toLowerCase().contains(txt);
	}
	return contained;
}





function wdg_do_bc(txt,channel,facettype)
{
	bc_add_breadcrumb_unique(txt,channel,facettype);
	refilter_widgets();
} 




if(!String.prototype.trim || true){  
  String.prototype.trim = function(){
    return this.replace(/^\s+|\s+$/g,'');  
  };  
}

function htmlDecode(value) {
   return (typeof value === 'undefined') ? '' : $('<div/>').html(value).text();
}



function eclog(txt)
{
	if (WANTCONSOLELOG) console.log(Date() + ' '+ txt);
}	


/**
 * @return an array of facetnames collected using facettype
 * SPECIALITY: In case of facettype=elibfacet, also the facetgroup is delivered in the format 
 * group:facetvalue 
 * 
 */
function collect_facets_terms(facettype)
{
	var facets_terms = new Array;
	jQuery('span.crumbssemfacetdesc').each(function(){
		if(jQuery(this).attr('ftype')==facettype)
		{		
			if (facettype=='semfacet'
			|| facettype=='wsearchfacet')
				facets_terms.push( jQuery('a',this).text().replace(' ','+'));	
			else if (facettype=='elibfacet')
			{ /* collect also facet group */
				var facetgroup=jQuery('label',this).html(); /*here is a : already contained!*/
				var elibfacetassignment=facetgroup + jQuery('a',this).text().replace(' ','+');
				facets_terms.push( elibfacetassignment );	
			}
		}
	});
	return facets_terms;
}





$(document).ready(function() {
	//comment this line:
	launch_query_for_debug();
	
	//RODINelib: Init buttons:
	clear_mainsearch();
	
	//set focus
	$('#elibsearchinput').focus();

	//Exec every 5 sec
	//setInterval(function(){ }, 5000);
	

	// Set autocomplete plugin to 
	var options = {
		serviceUrl : '<?php print $AUTOCOMPLETERESPONDER ?>',
		delimiter: ', ',
		user_id: '<?php print  $ELIB_USERID ?>',
		setversion: '2013',
		deferRequestBy: 500
	};
	$('#elibsearchinput').autocomplete(options);
	
	
	
	// init button click for ie
	$("a.button").click(function(){
  	if($(this).attr("href") != "") {
	  	window.location = $(this).attr("href");
  	}
  });


	//ADD distinct jQuery function:
	jQuery.extend({
    distinct : function(anArray) {
       var result = [];
       jQuery.each(anArray, function(i,v){
           if (jQuery.inArray(v, result) == -1) result.push(v);
       });
       return result;
    }
	});


	// Initial: hide all unnecessary elements
	$('div.project div.info').each(function(index, domEle) {
		$(this).hide();
	});
	$('#loginform').each(function(index, domEle) {
		$(this).hide();
	});
	//$('#languages').show();
	// Post: parse/hide/show
	/*$('.parse').each(function(index, domEle) {
		$(this).hide();
	});*/
	$('.parse').truncatable({ limit: 0, more: $('.more').html(), less: true, hideText: $('.close').html() }); 
	

	// Overlay: replace div with dialog tree
	$(document).ready(function() {
		$('div.overlay').wrap('<div class="dialog"><div class="bd">'+'<div class="c"><div class="s"></div></div></div></div>');
	});
	$('div.dialog').prepend('<div class="hd"><div class="c"></div><div class="r"></div></div>').append('<div class="ft"><div class="c"></div><div class="r"></div></div>');
	
	// Setzt Tooltip fÃ¼r die Kartenmarker
	$('#map span.marker').tooltip({
		tipClass: 'tooltip',
		offset: [13, 0],
		relative: true
	});
	// Setzt Marker-Button fÃ¼r Tooltip
	$('.marker').hover(function() {
		$('.tooltip').hide();
		$(this).next().show();
	});

	// Project Info: show/hide
	$('div.project').click(function() {
		if($(this).children('div.info').css("display") == "block") {
			$(this).children('h3').removeClass("active");
		} else {
			$(this).children('h3').addClass("active");
		}
		$(this).children('div.info').slideToggle();
	});
	
	// Login Form: show/hide
	$('#login').click(function() {
		$('#loginform').toggle();
	});
	$('#loginform .hide').click(function() {
		$('#loginform').hide();
	});
	
	// Language Form: show/hide
	$('#lang').mouseover(function() {
		$('#languages').show();
	});
	
	$('#languages').hover(function() {
		$('#languages').show();
	}, function(){
		$('#languages').hide();
	});
	
	// Search: change icon on mouse over
	$('#searchsubmit').hover(function() {
		$('.jqTransformInputInner').addClass("active");
	}, function(){
		$('.jqTransformInputInner').removeClass("active");
	});
	
/*	$('#languages .langitem').click(function(){
		$('#languages').hide();
	});*/
	
	// News: show more
	$('.more').click(function() {
		$('#newsurl').attr('value', $('.more').index($(this)));
		$('#newsform').submit();
	});
	$('.show-news').click(function() { 
		$('#newsurl').attr('value', $('.show-news').index($(this)));
		$('#newsform').submit();
	});
	// had to add show-news2, so that the read article link on home worked
	$('.show-news2').click(function() { 
		$('#newsurl').attr('value', $('.show-news2').index($(this)));
		$('#newsform').submit();
	});
	// News: read GET parameter
	$.extend({
		getUrlVars: function(){
			var vars = [], hash;
			var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for(var i = 0; i < hashes.length; i++) {
				hash = hashes[i].split('='); 
				vars.push(hash[0]);
				vars[hash[0]] = hash[1];
			}
			return vars;
		},
		getUrlVar: function(name) {
			return $.getUrlVars()[name];
		}
	});
	// News: jump to and show entry
	if($.getUrlVar('newsid')){
		$('.more_'+$.getUrlVar('newsid')).click();
		var id = parseInt($.getUrlVar('newsid'));
		var offset = $('#jump'+id).offset();
		window.scrollTo(offset.left, offset.top);
	}
	// Search: submit
	$('div.search .jqTransformInputInner').click(function() {
		// @todo alert("absenden");
	});
	
});





/**
*
*  Base64 encode / decode
*  http://www.webtoolkit.info/
*
**/
 
var Base64 = {
 
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
 
	// public method for encoding
	encode : function (input) {
		var output = "";
		var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
		var i = 0;
 
		input = Base64._utf8_encode(input);
 
		while (i < input.length) {
 
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
 
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
 
			if (isNaN(chr2)) {
				enc3 = enc4 = 64;
			} else if (isNaN(chr3)) {
				enc4 = 64;
			}
 
			output = output +
			this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
			this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
 
		}
 
		return output;
	},
 
	// public method for decoding
	decode : function (input) {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
 
		if (input!=null)
		{
			input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
		 	//var fri_output='';
			while (i < input.length) {
	 
				enc1 = this._keyStr.indexOf(input.charAt(i++));
				enc2 = this._keyStr.indexOf(input.charAt(i++));
				enc3 = this._keyStr.indexOf(input.charAt(i++));
				enc4 = this._keyStr.indexOf(input.charAt(i++));
	 
				chr1 = (enc1 << 2) | (enc2 >> 4);
				chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
				chr3 = ((enc3 & 3) << 6) | enc4;
	 
				output = output + String.fromCharCode(chr1);
				//fri_output = fri_output + ' '+ chr1;
	 			
				
				if (chr2>0) //FRI
				{
					if (enc3 != 64) {
						output = output + String.fromCharCode(chr2);
						//fri_output = fri_output + ' c2='+ chr2;
					}
				}
				
				if (chr3>0) //FRI
				{
					if (enc4 != 64) {
						output = output + String.fromCharCode(chr3);
						//fri_output = fri_output + ' c3='+ chr3;
					}
				}
			}
			output = Base64._utf8_decode(output);
		}
		else
			output=''; /*FRI protect against null data */
 		
		
 		//fri_output = Base64._utf8_decode(fri_output);
 
 
 		//alert('decoded: '+fri_output);
 
		return output;
 
	},
 
	// private method for UTF-8 encoding
	_utf8_encode : function (string) {
		var utftext = "";
		if (string)
		{
			string = string.replace(/\r\n/g,"\n");
			for (var n = 0; n < string.length; n++) {
	 
				var c = string.charCodeAt(n);
	 
				if (c < 128) {
					utftext += String.fromCharCode(c);
				}
				else if((c > 127) && (c < 2048)) {
					utftext += String.fromCharCode((c >> 6) | 192);
					utftext += String.fromCharCode((c & 63) | 128);
				}
				else {
					utftext += String.fromCharCode((c >> 12) | 224);
					utftext += String.fromCharCode(((c >> 6) & 63) | 128);
					utftext += String.fromCharCode((c & 63) | 128);
				}
			}
		}
 
		return utftext;
	},
 
	// private method for UTF-8 decoding
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
 
		while ( i < utftext.length ) {
 
			c = utftext.charCodeAt(i);
 
			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
 
		}
 
		return string;
	}
}


/* randomUUID.js - Version 1.0
*
* Copyright 2008, Robert Kieffer
*
* This software is made available under the terms of the Open Software License
* v3.0 (available here: http://www.opensource.org/licenses/osl-3.0.php )
*
* The latest version of this file can be found at:
* http://www.broofa.com/Tools/randomUUID.js
*
* For more information, or to comment on this, please go to:
* http://www.broofa.com/blog/?p=151
*/
 
/**
* Create and return a "version 4" RFC-4122 UUID string.
*/
function randomUUID() {
  var s = [], itoh = '0123456789ABCDEF';
 
  // Make array of random hex digits. The UUID only has 32 digits in it, but we
  // allocate an extra items to make room for the '-'s we'll be inserting.
  for (var i = 0; i <36; i++) s[i] = Math.floor(Math.random()*0x10);
 
  // Conform to RFC-4122, section 4.4
  s[14] = 4;  // Set 4 high bits of time_high field to version
  s[19] = (s[19] & 0x3) | 0x8;  // Specify 2 high bits of clock sequence
 
  // Convert to hex chars
  for (var i = 0; i <36; i++) s[i] = itoh[s[i]];
 
  // Insert '-'s
  s[8] = s[13] = s[18] = s[23] = '-';
 
  return s.join('');
}

/* foreach extension to Array */
Array.prototype.foreach = function( callback ) {
	  for( var k=0; k<this .length; k++ ) {
	    callback( k, this[ k ] );
	  }
}
