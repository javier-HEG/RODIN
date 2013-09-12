// HEG - RODIN semfilters
// Javascript Functions
// Autor: Fabio Ricci
// fabio.ricci@ggaweb.ch
// Date : 15.11.2012

// THIS AJAX File must be included 
// form inside a php script with root variables





/* FRI - MORPHOLOGICAL FILTERS */




function get_morphofilter(morphofilter_id)
{
  if (!morphofilter_id) morphofilter_id=0;
  var morpho_filter_name='boh';
  switch(morphofilter_id)
  {
    case(0):
    case(1):
      morpho_filter_name='morphodirect_match';
      break;
    case(2): morpho_filter_name='morpholevenstein_match';
      break;
    case(3): morpho_filter_name='morphosoundex_match';
      break;
    default: morpho_filter_name='morphodirect_match';
  }
  return morpho_filter_name;
}



/*
 * Returns what the current morhological filter wants to returns
 */
function use_morpho_filter(arg1, arg2)
{
	arg1=arg1?arg1.toLowerCase():'';
	arg2=arg2?arg2.toLowerCase():'';
	
	var morphological_filter = get_morpho_filter();
	var expression ='match = '+morphological_filter+'("'+arg1+'","'+arg2+'")';
	//eclog('use_morpho_filter '+expression);
	eval(expression);
	return match;
}


function get_morpho_filter()
{
	var morphological_filter = 'morphodirect_match';
  //if (document.forms.famenu && document.forms.famenu.ontomorphofilters)
  //   morphological_filter = get_morphofilter(parseInt(get_rb_selected_val(document.forms.famenu.ontomorphofilters)),10);
  //else 
  //if (document.forms.famenux && document.forms.famenux.ontomorphofilters)
  //   morphological_filter = get_morphofilter(parseInt(get_rb_selected_val(document.forms.famenu.ontomorphofilters)),10);
  return morphological_filter;
}

function morphodirect_match(arg1,arg2)
{
	var res = (arg1.indexOf(arg2)>-1 || (arg2.length < arg1.length && arg2.indexOf(arg1)>-1));
  return res;
}



function morpholevenstein_match(arg1,arg2)
{
  return (levenshteinenator(arg1,arg2) < 6);
}



function morphosoundex_match(arg1,arg2)
{
  return (soundex(arg1) == soundex(arg2));
}




function soundex (str) {
  // http://kevin.vanzonneveld.net
  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +    tweaked by: Jack
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Onno Marsman
  // +      input by: Brett Zamir (http://brett-zamir.me)
  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   original by: Arnout Kazemier (http://www.3rd-Eden.com)
  // +    revised by: Rafał Kukawski (http://blog.kukawski.pl)
  // *     example 1: soundex('Kevin');
  // *     returns 1: 'K150'
  // *     example 2: soundex('Ellery');
  // *     returns 2: 'E460'
  // *     example 3: soundex('Euler');
  // *     returns 3: 'E460'
  str = (str + '').toUpperCase();
  if (!str) {
    return '';
  }
  var sdx = [0, 0, 0, 0],
    m = {
      B: 1,
      F: 1,
      P: 1,
      V: 1,
      C: 2,
      G: 2,
      J: 2,
      K: 2,
      Q: 2,
      S: 2,
      X: 2,
      Z: 2,
      D: 3,
      T: 3,
      L: 4,
      M: 5,
      N: 5,
      R: 6
    },
    i = 0,
    j, s = 0,
    c, p;

  while ((c = str.charAt(i++)) && s < 4) {
    if (j = m[c]) {
      if (j !== p) {
        sdx[s++] = p = j;
      }
    } else {
      s += i === 1;
      p = 0;
    }
  }

  sdx[0] = str.charAt(0);
  return sdx.join('');
}




/*************************************************

Copyright (c) 2006. All Rights reserved.

If you use this script, please email me and let me know, thanks!

Andrew Hedges
andrew (at) hedges (dot) name

If you want to hire me to write JavaScript for you, see my resume.

http://andrew.hedges.name/resume/
FRI: Same changes on input args

*/

// calculate the Levenshtein distance between a and b, fob = form object, passed to the function
function levenshteinenator(arg1,arg2) {
	var cost;

	// get values
	var a = arg1;
	var m = a.length;

	var b = arg2;
	var n = b.length;

	// make sure a.length >= b.length to use O(min(n,m)) space, whatever that is
	if (m < n) {
		var c=a;a=b;b=c;
		var o=m;m=n;n=o;
	}

	var r = new Array();
	r[0] = new Array();
	for (var c = 0; c < n+1; c++) {
		r[0][c] = c;
	}

	for (var i = 1; i < m+1; i++) {
		r[i] = new Array();
		r[i][0] = i;
		for (var j = 1; j < n+1; j++) {
			cost = (a.charAt(i-1) == b.charAt(j-1))? 0: 1;
			r[i][j] = minimator(r[i-1][j]+1,r[i][j-1]+1,r[i-1][j-1]+cost);
		}
	}

	return r[m][n];
}

// return the smallest of the three values passed in
minimator = function(x,y,z) {
	if (x < y && x < z) return x;
	if (y < x && y < z) return y;
	return z;
}

/* FRI - MORPHOLOGICAL FILTERS - END */






/* FRI - SEMANTIC FILTERING */

function check_semfilterresults(txt,min_occurrences,concerning_menuitem_idx)
/*
 *
 * Returns in field this.do_menu_item: 0 on error, otherwise
 * +2  if the second menuitem in the contextmenu for RODIN shoul be visible
 * -2  if there is a reason why the menuitem should be made invisible
 * This function is used for testin the existence of a term inside some
 * loaded widgets results. If yes the function returns 2, if no it returns -2
 * since the menu item 2 offers the feature of semanticfiltering inside the
 * current results.
 */
{
  //alert ('checkPassivSemanticFilter ' + txt);
  //Use the following only to get the results:
  var HL = new count_matching_results(txt,"result-word");

  this.occurrences= HL.occurrences;
  this.widgets= HL.widgets;

  //Decide build menuitem or not:

  this.do_menu_item=this.occurrences >= min_occurrences? concerning_menuitem_idx: (- concerning_menuitem_idx);
}



function toggle_highlight_semfilterresults(elem,txt,highlight)
/* Elem ist the base from which a highlicht action should start */
{
	return;
  var eleclass=elem.classList[0];

  //Which morphological filter should be used?

  if (eleclass.indexOf('-hl')>-1)
  {
    if (elem.highlighted)
    {
      highlight_semfilterresults(txt,elem.hl_color,false);
      elem.highlighted=false;
      elem.style.backgroundColor='';
    }
    else
    {
      elem.hl_color=compute_highlightcolor(txt);
      elem.highlighted=true;
      highlight_semfilterresults(txt,elem.hl_color,true);
      elem.style.backgroundColor=elem.hl_color;
    }
  }
}


//Shortcuts to speedup load

//highlight filter results: USED?
function hf(txt)
{simple_highlight_semfilterresults(txt,true);}



/*onmousedown*/
function omd(obj,event)
{
	var LEFT=0;
	if (event.button === LEFT) 
	{
		BUTTONLEFT=true;
		SELECTEDWORDS=[];
		SELECTEDWORDS.push(obj); /*doubles!*/
	}
}

/*onmouseover*/
function omo(obj,event,msdelay)
{
	/*Add only if also leftbuttondown:*/
	if (!event.shiftKey && BUTTONLEFT) 
	{
		SELECTEDWORDS.push(obj); /*doubles!*/
	}
	else if (event.shiftKey && !BUTTONLEFT)
	{
		omu(obj,event,msdelay); //hovering-delayed
	}
	else if (!event.shiftKey) // only coloring (stanrard)
	{
		allow_obj_onmouseout(obj);
		simple_highlight_semfilterresults(obj.innerHTML,true);
	}
}

/*onmouseup*/
function omu(obj,event,msdelay)
{
	BUTTONLEFT=false;
	var LEFT=0;
	/*Add only if also leftbuttondown:*/
  if (event.button === LEFT) {
		var txt='';
		/*Sumup objs uniquely*/
		var objs=jQuery.distinct(SELECTEDWORDS);
		// jQuery(objs).each(function(){
			// txt+=(txt?' ':'')+jQuery(this).get(0).innerHTML;
		// });
		if(obj==null)
		{
			if(objs.length>0)
				obj=objs[objs.length - 1];
		}
		else
		if(objs.length==1)
		{
			txt = jQuery(obj).get(0).innerHTML;
			if (txt=='')
			{
				alert('empty text selected (1) ?');
				jQuery(objs).each(function(){
					txt+=(txt?' ':'')+jQuery(this).get(0).innerHTML;
				});
			}
		}
		else
		if(objs.length>1)
		{
			txt = window.getSelection ? window.getSelection() : document.selection;
			if (txt=='')
			{
				alert('empty text selected (1) ?');
				jQuery(objs).each(function(){
					txt+=(txt?' ':'')+jQuery(this).get(0).innerHTML;
				});
			}
		}
		else
		if (objs.length==0) {
			objs=[obj];
			txt=jQuery(obj).get(0).innerHTML;
		}
		phf(objs,txt,event,0);
		
		erase_text_selection();
		
		forbid_obj_onmouseout(obj);
	} 	
}


/*on mouse out: */
function mut(obj,event)
{
	if(! obj.onmouseout_forbidden)
		simple_highlight_semfilterresults(obj.innerHTML,false);
}

function forbid_obj_onmouseout(obj)
{
	obj.onmouseout_forbidden=true;
}

function allow_obj_onmouseout(obj)
{
	obj.onmouseout_forbidden=false;
}



/* Similar mouse event handling but for FACETS: */
/* onmouseover: */
function fomh(bnr,SRCID,counter,tr)
{
	var token="ricons";
	var id = token+"_"+bnr+"_"+SRCID+"_"+counter;

	/*(re)customize title if facet term selected :*/
	fomu_adapt_title_and_img_action_on_selection_txt(bnr,SRCID,counter,tr);

	/* control breadcrumb_display: */
	fb_check_bc_control_on_breadcrumb_matching(tr,true);
	
	reduce_fbtermwidth_onhover(tr);
	jQuery('#'+id).removeClass('hidden').show();	
	
} // fomh

/* onmouseout: */
function fomo(bnr,SRCID,counter,tr)
{
	var token="ricons";
	var id = token+"_"+bnr+"_"+SRCID+"_"+counter;
	jQuery('#'+id).addClass('hidden').hide();	
	unreduce_fbtermwidth_onhover(tr);
	//alert('mouseout')
	//ERASE selection text (if any)
	//reinit_ontofacet_selection(tr);
} // fomo

/* onmousedown: */
function fomd(bnr,SRCID,counter,event)
{
	var LEFT=0;
	if (event.button === LEFT) 
		BUTTONLEFT=true;
} // fomd

/* onmouseup: */
function fomu(bnr,SRCID,counter,tr,event)
{
	BUTTONLEFT=false;
	var LEFT=0;
	/*Add only if also leftbuttondown:*/
  if (event.button === LEFT) {
		txt = window.getSelection ? window.getSelection() : document.selection;
		var original_txt = $('td.fb a', tr).text();

		/* set this text as basis for the operations */
		if (txt.toString())
		{
			jQuery(tr).attr('st',txt.toString());
		}
		else
			jQuery(tr).attr('st', original_txt );

		/* Adapt bc icon in panel */
		fb_check_bc_control_on_breadcrumb_matching(tr,true);
		
		/* adapt title using selection */
		fomu_adapt_title_and_img_action_on_selection_txt(bnr,SRCID,counter,tr);
		jQuery(tr).attr('title','');
	}
	
} // fomu


/* DUMMY */
function gts64(termid)
{
	var t = jQuery('#'+termid).text().trim();
	return t;
}


function gts(termid)
{
	var t = jQuery('#'+termid).attr('st');
	return t;
}


function reinit_ontofacet_selection(tr)
{
	erase_text_selection();
	var original_selection_txt = $('td.fb a', tr).text();
	var current_selection_txt = jQuery(tr).attr('st');
	jQuery(tr).attr('st',original_selection_txt);
}

/* adapt title and functioncalls to selection txt: */
function fomu_adapt_title_and_img_action_on_selection_txt(bnr,SRCID,counter,tr)
{
	/*(re)customize title if facet term selected :*/
	var basetitle = jQuery('#facetboard-container').attr('tt');
	var title = '';
	var imgbasistitle= '';
	var imgtitle= '';
	var imgselector='';
	var current_selection_txt = jQuery(tr).attr('st');
	var original_selection_txt = $('td.fb a', tr).text();
	
		
	if (original_selection_txt != current_selection_txt)
	{
		/* adapt tr title */
		title=basetitle.replace(/___/,htmlDecode('Selection: &#171;'+current_selection_txt+'&#187; inside term &#171;'+original_selection_txt+'&#187;'));
		
		/* adapt img title for ontofacetterm bc*/ 
		imgselector="td.icons img.bc";
		imgbasistitle= jQuery(imgselector, tr).attr('tt');
		jQuery(imgselector, tr).attr('title', imgbasistitle.replace(/___/,htmlDecode('&#171;'+current_selection_txt+'&#187;')));
		
		/* adapt img title for ontofacetterm mlt*/ imgselector="td.icons img.mlt";
		imgbasistitle= jQuery(imgselector, tr).attr('tt');
		jQuery(imgselector, tr).attr('title', imgbasistitle.replace(/___/,htmlDecode('&#171;'+current_selection_txt+'&#187;')));

		/* adapt img title for ontofacetterm xp*/ imgselector="td.icons img.xp";
		imgbasistitle= jQuery(imgselector, tr).attr('tt');
		jQuery(imgselector, tr).attr('title', imgbasistitle.replace(/___/,htmlDecode('&#171;'+current_selection_txt+'&#187;')));

		/* adapt img title for ontofacetterm sc*/ imgselector="td.icons img.sc";
		imgbasistitle= jQuery(imgselector, tr).attr('tt');
		jQuery(imgselector, tr).attr('title', imgbasistitle.replace(/___/,htmlDecode('&#171;'+current_selection_txt+'&#187;')));

	}
	else
	{
		/* adapt title */
		title=basetitle.replace(/___/,htmlDecode('&#171;'+original_selection_txt+'&#187;'));
		/* adapt img title for ontofacetterm bc*/ 
		imgselector="td.icons img.ontofacetterm.bc";
		imgbasistitle= jQuery(imgselector, tr).attr('tt');
		jQuery(imgselector, tr).attr('title', imgbasistitle.replace(/___/,htmlDecode('&#171;'+original_selection_txt+'&#187;')));
		
		/* adapt img title for ontofacetterm mlt*/ 
		imgselector="td.icons img.mlt";
		imgbasistitle= jQuery(imgselector, tr).attr('tt');
		jQuery(imgselector, tr).attr('title', imgbasistitle.replace(/___/,htmlDecode('&#171;'+original_selection_txt+'&#187;')));

		/* adapt img title for ontofacetterm xp*/ 
		imgselector="td.icons img.xp";
		imgbasistitle= jQuery(imgselector, tr).attr('tt');
		jQuery(imgselector, tr).attr('title', imgbasistitle.replace(/___/,htmlDecode('&#171;'+original_selection_txt+'&#187;')));

		/* adapt img title for ontofacetterm sc*/ 
		imgselector="td.icons img.sc";
		imgbasistitle= jQuery(imgselector, tr).attr('tt');
		jQuery(imgselector, tr).attr('title', imgbasistitle.replace(/___/,htmlDecode('&#171;'+original_selection_txt+'&#187;')));
	}
	jQuery(tr).get(0).title=title;

} // fomu_adapt_title_and_img_action_on_selection_txt



/**
 * Reduce (if necessary) the width of fbterm in a
 */
function reduce_fbtermwidth_onhover(tr)
{
	var limit_len_px=150;
	jQuery('td.fb a',tr).each(function(){
		var txt = $(this).text();
		var len = $(this).width();
		
		if (len > limit_len_px) {
			//alert('term '+txt+' has length ...' + len);
			$(this).attr('ot',txt);
			/* reduce ot */
			var reducingstep=2; var tmp;
			var delta=reducingstep;
			while ($(this).width() > limit_len_px)
			{
				delta+=reducingstep;
				tmp= txt.substr(0,txt.length - 1 - delta);
				$(this).text(tmp+' ...');
			}
		}
	});
} //reduce_fbtermwidth_onhover


/**
 * reset text inside term with ot
 * if contained
 */
function unreduce_fbtermwidth_onhover(tr)
{
	jQuery('td.fb a',tr).each(function(){
		var orig_txt= $(this).attr('ot');
		if (orig_txt) {
			$(this).text(orig_txt);
		}
	});
} //unreduce_fbtermwidth_onhover





function do_bc(termid)
{
	bc_add_breadcrumb_unique(gts(termid),'result');
	reinit_ontofacet_selection( jQuery('#'+termid).get(0) );
}

function do_xp(termid,debug)
{
	extra_rodin_thesearch_results(gts(termid),debug);
	reinit_ontofacet_selection( jQuery('#'+termid).get(0) );
}

function do_sc(termid)
{
	set_query(gts(termid));
	reinit_ontofacet_selection( jQuery('#'+termid).get(0) );
}

function do_mlt(t64,termid)
{
	src_widget_morelikethis(tr, t64, gts(termid), 'en');
	reinit_ontofacet_selection( jQuery('#'+termid).get(0) );
}



/* End Similar mouse event handling but for FACETS */


/**
 * Add text objecst to selection tooltip
 * Distinguish between
 * 1. one element (->filter color siimilar)
 * 2. multiple elements (-> no filter no coloring)
 */
function phf(objs,txt,event,delaymsecs)
{
	var tooltipId  ='elib_tooltip';
	var execonopen='';
	var onmouseover='';
	var onmouseout ='';
	
	if (objs.length==1) { 
		execonopen='simple_highlight_semfilterresults(\''+txt+'\',true);transfer_bgc_to_ttp(\''+tooltipId+'\')';
		onmouseout ='puh(\''+txt+'\')';
	} else {
		onmouseover='transfer_bgc_to_ttp(\''+tooltipId+'\')';
	}
	
	elib_tooltip(objs,event,construct_elib_actions_tooltip(txt,objs),'tlw',execonopen,onmouseover,onmouseout,delaymsecs);}

//undo_highlight:
function puh(txt)
{simple_highlight_semfilterresults(txt,false);}

/** 
 * Transfer bgcolor from obj to tooltip label
 */
function transfer_bgc_to_ttp(tooltipId)
{	
	var it = document.getElementById(tooltipId);
	if (it)
	{
		var label = jQuery('label#ttp.ttp',it);
		if (it.obj && it.obj.style && it.obj.style.backgroundColor && label)
		{
			jQuery(label).css('background-color',it.obj.style.backgroundColor);
			jQuery(label).css('color',it.obj.style.color); /*simply select text to show it better*/
		}
		else {
			jQuery(label).css('background-color','#a00'); /*simply select text to show it better*/
			jQuery(label).css('color','white'); /*simply select text to show it better*/
		}
	}
}

function simple_highlight_semfilterresults(txt,highlight)
{
	if (txt) // we accept only text (no null)
	{
		var hl_color=compute_highlightcolor(txt);
	  highlight_semfilterresults(txt,hl_color,highlight);
 	}
 	//else alert('System error: simple_highlight_semfilterresults called with no txt to highlight !! ');
}


//shortcuts to load faster

function urr(txt)
{undo_restrict_render(txt);}

//And now using parent:
function prr(txt)
{restrict_render(txt);}

function purr(txt)
{parent.urr(txt);}


function erase_text_selection()
{
	var sel = window.getSelection ? window.getSelection() : document.selection;
	if (sel) {
    if (sel.removeAllRanges) {
        sel.removeAllRanges();
    } else if (sel.empty) {
        sel.empty();
   }
	}
}

function bc_on_breadcrum_changed(b)
{
	var bc_height_px=jQuery(b).height();
	var bc_visible_limit_px=145;
	var new_limit=(b.new_limit? b.new_limit: bc_visible_limit_px);
	if (bc_height_px > new_limit)
	{
		var delta = bc_height_px - bc_visible_limit_px;
		jQuery('#subheader').height(  jQuery('#subheader').height() + delta );
		b.new_limit = bc_height_px;
	}
}

/**
 * restrict render and show notification
 */
function restrict_render(txt)
{
	var tooltip_nl='<?php print $TTPNEWLINE;?>';

	jQuery("tr.rresult:not(:contains(\""+txt+"\"))").each(function(){
		jQuery(this).hide();
	});
	/* set warning + set undo */
	jQuery("#lnotification").html("Results view filtered with <b>&#171;"+txt+"&#187;</b>");
	
	jQuery("#anotification").each(function(){
		jQuery(this).attr("onclick", "undo_restrict_render(); jQuery('tr.wnotification').hide()");
//		jQuery(this).attr("title", "The following results contains the text &#171;"+txt+"&#187;"+tooltip_nl+"Some results might be possibly hidden."+tooltip_nl+"Click to show again all results");
		jQuery(this).attr("title", "The following results contains the text '"+txt+"' ."
															+"- Some results might be possibly hidden. "
															+"- Click here to show again all results");
	});
	
	jQuery('tr.wnotification').show();
}


/*
 * Shows all results again
 * containing txt
 */
function undo_restrict_render()
{
	jQuery("tr.rresult").each(function(){
		jQuery(this).show();
	});
}



function compute_highlightcolor(txt)
/*
 * returns the color in function of txt (tbd)
 * select rgb=(first,middle,last) byte in txt
 */
{
  var rx=txt.charAt(0);
  var gx=txt.charAt(length/2);
  var bx=txt.charAt(txt.length-1);
  var r=Math.round( rx.charCodeAt(0) *2.5  );
  var g=Math.round( gx.charCodeAt(0) *2.0  );
  var b=Math.round( bx.charCodeAt(0) *1.5  );

  var minc=180; var maxc=250;
  /* limit color between minc and maxc */
  var r2 = r>maxc?maxc:(r<minc?minc:r);
  var g2 = g>maxc?maxc:(g<minc?minc:g);
  var b2 = b>maxc?maxc:(b<minc?minc:b);
  
  var r16=r2.toString(16);
  var g16=g2.toString(16);
  var b16=b2.toString(16);
  
  //alert('compute_highlightcolor: '+txt+' rgb='+rx+':'+r+'->'+r2+'('+r16+') - '+gx+':'+g+'->'+g2+'('+g16+') - '+bx+':'+b+'->'+b2+'('+b16+')');

  return '#'+r16+g16+b16;

}

function cleanup4semfiltering(txt)
{
	//Eliminate .,:;/\&%
  txt=txt.toLowerCase().replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
  
	return txt;	
}

function highlight_semfilterresults(txt,bgcolor,highlight)
{
   txt=cleanup4semfiltering(txt)
   // alert('highlight_semfilterresults: txt=('+txt+' bgcolor='+bgcolor+')');

  var match = false;
  var resulttermclassmame="result-word";
  var occurrences_in_results=0;
  var widgets=0;
  var obj=null;
  var pertinentIframes=null;
  var arrALL_RESULT_TERMS = null;
  var parentIsIndexConnected = ! (typeof parent.isIndexConnected == 'undefined');

  arrALL_RESULT_TERMS = get_all_elems_by_hlclass(obj,resulttermclassmame);
  
	//arrALL_RESULT_TERMS = jQuery().get();
	//Use into arrALL_RESULT_TERMS:
	for(var i=0;i<arrALL_RESULT_TERMS.length;i++)
  {
    var RESULT_TERM= (arrALL_RESULT_TERMS[i]);
    var RESULT_TERM_TXT= RESULT_TERM.innerHTML;

    //eval morphofilter on cleaned up tokens
    RESULT_TERM_TXT=cleanup4semfiltering(RESULT_TERM_TXT);
    
    if (use_morpho_filter(RESULT_TERM_TXT,txt))
    {
      occurrences_in_results++;

      /*EVTL HIGHLIGHT WORD IN RESULTS*/
      if (highlight)
      {
        if (RESULT_TERM.getAttribute('class')==resulttermclassmame)
        {
          RESULT_TERM.setAttribute('class',resulttermclassmame+'-hl');
          RESULT_TERM.style.backgroundColor=bgcolor;
        }
      }
      else
      { //We need that EVERY result be unhiglighted:
        //if (RESULT_TERM.getAttribute('class')==resulttermclassmame+'-hl')
        {
          RESULT_TERM.setAttribute('class',resulttermclassmame);
          RESULT_TERM.style.backgroundColor='';
        }
      }
    }
 } //for
  this.occurrences= occurrences_in_results;
  this.widgets= widgets;
}


function filter_resultdocuments_on(ele)
{
	alert('filter_resultdocuments_on word');	
}

function filter_resultdocuments_back_on(ele)
{
	alert('filter_resultdocuments_back_on word');	
}


function count_matching_results(txt,classname)
{
  txt=cleanup4semfiltering(txt)

  var match = false;
  var occurrences_in_results=0;
  var widgets=0;
  var parentIsIndexConnected = ! (typeof parent.isIndexConnected == 'undefined');

  var tab_id = parentIsIndexConnected ?
      parent.tab[parent.$p.app.tabs.sel].id :
        window.opener.tab[window.opener.$p.app.tabs.sel].id;

  var pertinentIframes = parent.getPertinentIframesInfos(tab_id);
  for(var f=0;f<pertinentIframes.length;f++)
  {
      var in_widget=false;
      var iframe=pertinentIframes[f][0];
      //alert('check in iframe with selector '+selector);
      /* Here the conversion from nodelist to array is used for concat */
      var obj=(iframe.localName=='div')?iframe:iframe.contentDocument;
      var arrALL_RESULT_TERMS = get_all_elems_by_hlclass(obj,classname);

      for(var i=0;i<arrALL_RESULT_TERMS.length;i++)
      {
        var RESULT_TERM= (arrALL_RESULT_TERMS[i]);
        var RESULT_TERM_TXT= RESULT_TERM.innerHTML;

        //eval morphofilter on cleaned up tokens
    		if (use_morpho_filter(RESULT_TERM_TXT,txt))
        {
          occurrences_in_results++;

          if (!in_widget)
          {
            in_widget=true;
            widgets++; /*Count widgets in wich results occurs*/
          }
        }
      } //for
  } //for

  this.occurrences= occurrences_in_results;
  this.widgets= widgets;
}




function hide_un_highlighted_results(hide_hl)
{
  //alert ('checkPassivSemanticFilter ' + txt);
  /* Search for existence of txt in every widget*/
 
  var resulttermclassmame="result-word";
  var resulttermclassmame_hl=resulttermclassmame+"-hl";
  var resultclassmame="oo-result-container";
  var occurrences_in_results=0;
  var widgets=0;
  var obj=null;
  var arrALL_RESULT=null;
  var parentIsIndexConnected = ! (typeof parent.isIndexConnected == 'undefined');

  var tab_id = parentIsIndexConnected ?
      parent.tab[parent.$p.app.tabs.sel].id :
        window.opener.tab[window.opener.$p.app.tabs.sel].id;

	var index = tabAggregatedStatusTabId.indexOf(tab[$p.app.tabs.sel].id);
	//Take results from aggregated view?
	//Store all into arrALL_RESULT_TERMS:
	if(tabAggregatedStatus[index])
	{
		var aggrview_container_id='aggregated_view_results_'+tab_id;
		obj=document.getElementById(aggrview_container_id);
		arrALL_RESULT = get_all_elems_by_hlclass(obj,resultclassmame);
	}
	else
	{
		pertinentIframes = parent.getPertinentIframesInfos(tab_id);  for(var f=0;f<pertinentIframes.length;f++)
	  {
      var iframe=pertinentIframes[f][0];
      //alert('check in iframe with selector '+selector);
      /* Here the conversion from nodelist to array is used for concat */
      obj=(iframe.localName=='div')?iframe:iframe.contentDocument;
      var arr = get_all_elems_by_class(obj,resultclassmame);
      arrALL_RESULT = arr.length?(arrALL_RESULT?arrALL_RESULT.concat(arr):arr):arrALL_RESULT;
	  } //for
	}

	//Process arrALL_RESULT:
	if (hide_hl)
	{
		for(var i=0;i<arrALL_RESULT.length;i++)
	  {
	    var RESULT= (arrALL_RESULT[i]);
	    var arrALL_HIGHLIGHTED_RESULTTERMS_hl = get_all_elems_by_hlclass(RESULT,resulttermclassmame_hl);
	    if (arrALL_HIGHLIGHTED_RESULTTERMS_hl.length==0) //Nothing highlighted?
	    {
	      jQuery(RESULT.children[0]).hide();
	      jQuery(RESULT.children[1]).hide();
	    }
	 	} //for
	}
	else // show all hidden again!
	{
		for(var i=0;i<arrALL_RESULT.length;i++)
	  {
	    var RESULT= (arrALL_RESULT[i]);
	    var arrALL_HIGHLIGHTED_RESULTTERMS_hl = get_all_elems_by_hlclass(RESULT,resulttermclassmame_hl);
	    if (arrALL_HIGHLIGHTED_RESULTTERMS_hl.length==0) //Nothing highlighted?
	    {
	      jQuery(RESULT.children[0]).show();
	      jQuery(RESULT.children[1]).show();
	    }
	 	} //for
	}
  this.occurrences= occurrences_in_results;
  this.widgets= widgets;
}









// get_all_elems_by_hlclass(iframe.contentDocument,"result-word")
function get_all_elems_by_hlclass(contentdoc,classname)
{
  var i;
  var RESULT_TERMS    ; var arrRESULT_TERMS = [];
  var RESULT_TERMS_HL ; var arrRESULT_TERMS_HL = [];
  var arrALL_RESULT_TERMS;
  if (contentdoc)
  	RESULT_TERMS =jQuery('.'+classname,contentdoc).get();
  else 
  	RESULT_TERMS =jQuery('.'+classname).get();
  
  for(i = RESULT_TERMS.length; i--; arrRESULT_TERMS.unshift(RESULT_TERMS[i]));
  
  if (contentdoc)
	  RESULT_TERMS_HL =jQuery('.'+classname+"-hl",contentdoc).get();
	 else
	  RESULT_TERMS_HL =jQuery('.'+classname+"-hl").get();
  //contentdoc.getElementsByClassName(classname+"-hl",null);
  
  for(i = RESULT_TERMS_HL.length; i--; arrRESULT_TERMS_HL.unshift(RESULT_TERMS_HL[i]));

  arrALL_RESULT_TERMS = arrRESULT_TERMS.concat(arrRESULT_TERMS_HL);
  return arrALL_RESULT_TERMS;
}


// get_all_elems_by_hlclass(iframe.contentDocument,"result-word")
function get_all_elems_by_class(contentdoc,classname)
{
  var i;
  var RESULT_TERMS    ; var arrRESULT_TERMS = [];
  if(contentdoc)
  	RESULT_TERMS =jQuery('.'+classname,contentdoc).get();
  else
  	RESULT_TERMS =jQuery('.'+classname).get();
  
  for(i = RESULT_TERMS.length; i--; arrRESULT_TERMS.unshift(RESULT_TERMS[i]));

  return arrRESULT_TERMS;
}



function highlight_and_filter_results(flag)
{
  var t=document.getElementById('facetContextMenuLabel').innerHTML;
  simple_highlight_semfilterresults(t,flag);
}



function mark_ontoterms_on_resultmatch()
{
  eclog('mark_ontoterms_on_resultmatch Start refreshing onto matches ...');

  if (ONTOTERMS_REDO_HIGHLIGHTING) //Need do do it?
  {
    var facetclassmame="fb-term";
    var resulttermclassmame="result-word";
    var arrALL_FACET_TERMS = get_all_elems_by_hlclass(document,facetclassmame);

    for(var i=0;i<arrALL_FACET_TERMS.length;i++)
    {
      var FACET_TERM= (arrALL_FACET_TERMS[i]);
      var FACET_TERM_TXT= FACET_TERM.innerHTML;

      //eclog('Considering facet term '+FACET_TERM_TXT+' ...');
      //Match with some result?
      var HL = new count_matching_results(FACET_TERM_TXT,resulttermclassmame);
      //eclog('Got '+HL.occurrences+' mathing results for term '+FACET_TERM_TXT+' using '+resulttermclassmame);
      if (HL.occurrences > 0)
      {
        eclog('Marking facet term '+FACET_TERM_TXT+' as matching');
        if (FACET_TERM.getAttribute('class')==facetclassmame)
        {
          FACET_TERM.setAttribute('class',facetclassmame+'-hl');
          FACET_TERM.setAttribute("title", lg('lblOntoFacetsTermActions2'));
          //hover-->higlight
          FACET_TERM.setAttribute('onmouseover',"hf(this.innerHTML)");
          FACET_TERM.setAttribute('onmouseout',"uh(this.innerHTML)");
          //click-->filter
          FACET_TERM.setAttribute('onclick',
          												'this.clicked=!this.clicked; hide_un_highlighted_results(this.clicked);');
        }
      }
      else
      { /*renormalize display*/
        if (FACET_TERM.getAttribute('class')==facetclassmame+'-hl')
          {
            FACET_TERM.setAttribute('class',facetclassmame);
            FACET_TERM.setAttribute("title", lg('lblOntoFacetsTermActions'));
            FACET_TERM.setAttribute('onclick','#');
            FACET_TERM.setAttribute('onmouseover',"");
            FACET_TERM.setAttribute('onmouseout',"");
          //click-->filter
          }
      }
    } //for

    ONTOTERMS_REDO_HIGHLIGHTING=false;
    eclog('mark_ontoterms_on_resultmatch Ended refreshing onto matches ...');

  }
  else
      eclog('mark_ontoterms_on_resultmatch DONOTNEEDTO refresh onto matches (NOTHING DONE)');
}



function mlt_wdoc(id_wdoc)
{
	alert('TBD mlt_wdoc('+id_wdoc+')');
}


function mlt_fb(id_tr)
{
	alert('TBD mlt_fb('+id_tr+')');
}



function dskos_rerank_widgets_results(term)
//Returns vector with rerank info to permute
//Widgets results
{
  //alert ('dskos_rerank_widgets_results');
  //Collect array of widgets results
  //Organize them as vector[widget][ResultNr]=array(terms)
  var arrWIDGETR=getDisplayedWidgetResults();
  
  //Collect skosnodeinfo organized as vector[]=
  var strSKOS_CONTEXT=get_skos_context();

  //alert('skos-context('+term+'): \n\n'+strSKOS_CONTEXT);

  //RERANK Results accordingly to text distance function

  var arrRERANKED_WIDGETR = rank_widgetresults(strSKOS_CONTEXT,arrWIDGETR);

  return arrRERANKED_WIDGETR;
}





function permutate_widgets_result_render(reranked_widgetresults)
{
  for(var i=0;i<reranked_widgetresults.length;i++)
  {
    var arr=reranked_widgetresults[i];
    var widgetname  = arr[0];
    var WRranks     = arr[1];
    var WRpermutated= arr[2];
   
    var iframe=document.getElementById(widgetname);
    var results=iframe.contentDocument.getElementById('$widgetdivid');

    //permutate the children of results according to WRpermutated

    var duplicated_results=results.cloneNode(true);

    var children = results.children;

    //Skip elements: first child is spotlight, last two are other stuff:
    for(var j=WRpermutated.length - 1;j>-1;j--)
    {
      var p=WRpermutated[j];
      // replace children[p] <-- duplicated_results.children[j+1]
      results.replaceChild(duplicated_results.children[j+1], children[p]);
      //children[p].innerHTML=duplicated_results.children[j+1].innerHTML;
    }

    
  }

}





function rank_widgetresults(strSKOS_CONTEXT,arrWIDGETR)
// Test here ...: Invert results each widget
// returns an array with index permutations according to rerank
{
  var arrWIDGETR_R = new Array();
  for(var i=0;i<arrWIDGETR.length;i++)
  {
    var arrResultstowidget=arrWIDGETR[i];
    var widgetname= arrResultstowidget[0];
    var resultstowidget= arrResultstowidget[1];
    var WRpermutated= new Array();
    var WRranks= new Array();

    for(var j=0;j<resultstowidget.length;j++)
    {
      WRpermutated.push(resultstowidget.length - j);
      WRranks.push( j ); // DUMMY RUNK
    }

    // add info to widget id: ranks list, new index list:
    arrWIDGETR_R.push(new Array(widgetname,WRranks,WRpermutated));
  }
  return arrWIDGETR_R;
}






function getDisplayedWidgetResults()
//Returns array of WIDGETS
//each of one is an array of Results
//each of one is a Term
//DO NOT USE results inside the aggregates view
{
  var arrRESULTScontainers = new Array();
  var arrDisplayedWidgetResults = new Array();

  var tab_id = parent.tab[parent.$p.app.tabs.sel].id;

  var pertinentIframes = parent.getPertinentIframesInfos(tab_id);
  for(var f=0;f<pertinentIframes.length;f++) // for each widget
  {
    var iframe=pertinentIframes[f][0];
    //alert('check in iframe with selector '+selector);
    /* Here the conversion from nodelist to array is used for concat */
    var obj=(iframe.localName=='div')?iframe:iframe.contentDocument;
    if (iframe.localName=='iframe')
    {
      var locallyDisplayedWResults = new Array();
      jQuery(".oo-result-container",obj).each(function(){ //for each result in widget
        jQuery(this,".oo-result").each(function(){
        //locallyDisplayedWResults.push(this.toPureContentText());

          var result_number = this.children[0].childNodes[0].data;

          var cleanedtext=cleanresulttext(this.innerText,result_number,result_number.length);
          if (cleanedtext.trim())
            locallyDisplayedWResults.push(cleanedtext);
        });
      });
      arrDisplayedWidgetResults.push(new Array(iframe.name,locallyDisplayedWResults));
    }
  }
  return arrDisplayedWidgetResults;
}





function cleanresulttext(txt,result_number,len)
{
  //Substitute non alfa by blank and double blanks
  //remove result_number at the beginning
  if (txt)
  {
    if (txt.substr(0,len) ==  result_number)
      txt=txt.substr(len);
    var cleantxt=txt.replace(/[^a-z\d ]+/ig,' ').replace(/ +/g, " ").replace(/ +/g, " ");
  }
  else cleantxt = '';
  
  return cleantxt.trim();
}




function get_loaded_skos_context(obj)
// Returns a vector of words (terms) from the skos node corresponding to term
// With a + as separator
{
  //alert('get_loaded_skos_context in obj '+obj+' '+obj.id);
  var arrSKOS_CONTEXT= new Array();
  var strSKOS_CONTEXT='';
  jQuery(".fb-term, .fb-term-hl",obj).each(function(){
    arrSKOS_CONTEXT.push(this.innerHTML);
  });
  for(var i=0;i<arrSKOS_CONTEXT.length;i++) 
  {
    strSKOS_CONTEXT+= strSKOS_CONTEXT?'+':'';
    strSKOS_CONTEXT+= arrSKOS_CONTEXT[i];
  }

  return strSKOS_CONTEXT;
}






function src_widget_morelikethis(obj,semanticcontextbase64,term,lang)
{
  /*get id inside oo-container where obj is*/
  var semcarr = semanticcontextbase64.split(',');
  var semanticcontext = '';
  var semanticcontext_SEPARATOR=',';
  semcarr.foreach( function( k, v ) {
			semanticcontext+=semanticcontext?semanticcontext_SEPARATOR+' ':'';
      semanticcontext+=Base64.decode(v);
		});
  
  var onto_div = obj.parentNode.parentNode.parentNode.parentNode.parentNode;
  var onto_div_id = onto_div.id;
  var ONTO_id = onto_div_id.substr(onto_div_id.lastIndexOf('_')+1);
  
  var group_div = document.getElementById('fb_itemcontent_'+ONTO_id);
  if (!group_div) alert('ALERT: NO OBJECT WITH ID='+'fb_itemcontent_'+ONTO_id);
  
  /* //We do not ask - we do:
  if (confirm("This will show ranked widget results\naccordingly to similarity with the context of '"+term+"':"
          +"\n\n"+semanticcontext
          +"\n\nContinue?")) */
  {
    var solr_id = semanticcontextbase64;
    var solr_path = 'rodin_result';

    var solr_add_doc_uri="<?php print $SOLR_ADD_DOC_URI;?>"
                +"?id="+solr_id
                +"&path="+solr_path
                +"&title=pathcontext-to-"+term
                +"&lang="+lang
                ;

    //Call a SOLR registering service for this doc
    //and prepare a mlt query
    $p.ajax.call( solr_add_doc_uri,
          {
            'type':'load',
            'callback':
             {
              'function':handle_post_solr_doc_update,
              'variables':
               {
                'id':solr_id,
               }
             }
          }
    )
  }
}

/**
	 * callbyk after skos context insertion in SOLR
	 * @param response - the response of the called service 
	 * @param vars - the AJAX passed vars
	 */
function handle_post_solr_doc_update(response,vars) {
	var id = vars['id'];
	var ok = false;
  // response ok?
  //alert('handle_post_solr_doc_update');
  if (response!=null) {
		if (response.getElementsByTagName("add_solr_doc")[0]) /*XML*/
		{
			 var tags =(response.getElementsByTagName("*"));
			 for(var i=0;i<tags.length;i++)
			 {
				 var tag=tags[i];
				 //alert('t: '+tag.tagName+'='+tag.textContent);
				 if (tag.tagName=='result')
				 {
					var add_result =tag.textContent;
          ok= (add_result==0)
          if (!ok)
          {
           alert('System error? wrong result got from SOLR UPDATE: '+add_result); 
           break;
          }
        }
        else if (tag.tagName=='error')
        {
         var errortxt=tag.textContent;
         alert('Error adding context in SOLR: '+errortxt);
         break;
        }
			 
     }
  }
  
  if (ok)
  {
    var slrq='mlt?q=id:'+id+'&mlt.fl=body&fl=score,*&mlt.minwl=3&mlt.mintf=1';

//    alert('rerank_widget_results_using '
//        +'Ontology id '+id
//        +'\n\nUSING SKOS KONTEXT:\n\n'+doc
//        +'\n\nUSING URL:\n\n'+slrq);

     slrq_to_widgets(slrq);
    }
  }
}


	/**
	 * Prepare an mlt/ SOLR call to give to all wirgets to rerank their results
	 * @param sid 
	 * @param datasource
	 * @param resultIdentifier the currend result identifier
	 */
  function widget_morelikethis(solr_id,solr_path) {
    /*Search considering words from 3 letters on*/
    var slrq='mlt?q=id:'+solr_id+'&mlt.fl=body&fl=score,*&mlt.minwl=3&mlt.mintf=1';
    var generic_solr_call=solr_path+slrq;
    //alert('widget_morelikethis generic_solr_call: '+generic_solr_call);
    /* // we do not ask - we do:
    if (confirm("This will show ranked widget results accordingly to similarity with this result."
      //+" \n\n using "+generic_solr_call
      +"\n\nContinue?")) */
    {
      slrq_to_widgets(slrq);
    }
  } // widget_morelikethis
  
  
  
	/**
	 * Call every widget with the given slrq params
   * defining an mlt query
	 * @param slrq 
	 */
  function slrq_to_widgets(slrq) {
    var tab_id = parent.$p.app.tabs.sel;
    var db_tab_id = parent.tab[tab_id].id;

    /* for every widget: reload them with slrq */
   // Find the current tab's widget's iFrames
    var pertinentIframes;
    if (tab_id >= 0) {
      pertinentIframes = parent.getPertinentIframesInfos(db_tab_id);
    }

    // 2. Send URL to each widget
    if (tab_id >= 0) {
      var i;
      for( i=0; i<pertinentIframes.length; i++) {
        var iframe = pertinentIframes[i][0];

        // cancel any q, subst qe value:
        var old_querystring = parseUri(iframe.src).query;
        var qs = new Querystring(old_querystring);
        
        var  slrq_term=slrq
                    //    +'&rows='+qs.get('m') // Need more than that because of duplicated results
                        +'&fq=wdatasource:'
                        +parseUri(iframe.src).path;

        //qs.set('qe',searchtxt);
        //qs.set('q',searchtxt);
        qs.set('slrq',parent.Base64.encode(slrq_term));
        qs.set('go','1');
        qs.set('textZoomRender', parent.document.getElementById("selectedTextZoom").value);
        qs.set('rerender',0);
        qs.set('show','RDW_widget');
        /* get the ifram local localMaxResults value */
        //qs.set('m',localMaxResults);
        //qs.set('_w',_w);
        //qs.set('_h',_h);
        //qs.set('_x',iframe.id);
        //qs.set('user_id',parent.$p.app.user.id);

        //Mark last ifram exec:
        if (i==pertinentIframes.length - 1) {
          qs.set('uncache',1);
        }

        var server = '<?php print $WEBROOT; ?>';
        var newUrl = server + parseUri(iframe.src).path + '?' + qs.toString();
        //alert(newUrl);
        //window.open(newUrl,iframe.name);
        iframe.setAttribute("src", newUrl); // Automatically reloads the iFrame!
      }
    }
  } // slrq_to_widgets
  
  
	/**
	 * Makes an AJAX call to our ZenFilter implementation.
	 * @param textToFilter64 the text that needs to be filtered (Base64 encoded)
	 * @param query64 the query that originated the results from which comes the text to be filetered (Base64 encoded)
	 * @param zenFilterBox the div element in which filtered terms are to be displayed
	 */
	function rodin_zen_filter(textToFilter64, query64, zenFilterBox) {
		zenFilterBox.style.visibility = "visible";
		fillZenFilterBoxLoading(zenFilterBox);
		
		var zenFilterResponderUrl = '<?php print "$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/ZenFilterResponder.php"; ?>';

		jQuery.post(zenFilterResponderUrl,
			{textToFilter: textToFilter64, query: query64, lang: parent.LANGUAGE_OF_RESULT_CODED},
			function(data) {
					rodin_zen_filter_handler(data, zenFilterBox);
			});
	}
	
	/**
	 * Function called when the AJAX call to ZenFilter is successful.
	 * @param data returned by the zen-filter responder
	 * @param zenFilterBox the div element to fill with filtered terms
	 */
	function rodin_zen_filter_handler(data, zenFilterBox) {
		var filtered = data.documentElement;
		var termList = filtered.getElementsByTagName("term");
		var methodUsed = filtered.getAttribute("lastMethodUsed");
				
		if (filtered && termList) {
			fillZenFilterBoxWithTerms(zenFilterBox, termList, methodUsed);
		} else
			alert("[Javier] Zen Filtered : FAIL");
	}
	
	/**
	 * This function fills the spotlight box with a loading message
	 * to be shown while the spotlight terms are computed.
	 * @param zenFilterBox the zen-filter div element
	 */
	function fillZenFilterBoxLoading(zenFilterBox) {
		var loadingElement = document.createElement("p");
		loadingElement.setAttribute("class", "loading");
		loadingElement.setAttribute("title", "");
		loadingElement.appendChild(document.createTextNode(lg("lblZenFiltering")));
		
		zenFilterBox.innerHTML = "";
		zenFilterBox.appendChild(loadingElement);
	}
	
	/**
	 * This function replaces the loading message in the spotlight box
	 * with the list of terms that have been found. It also adds the
	 * "Select all" and the "Close" buttons.
	 * @param zenFilterBox the zen-filter's div element.
	 * @param termList the list of found terms.
	 * @param lastMethodUsed last method used to filter the result.
	 */
	function fillZenFilterBoxWithTerms(zenFilterBox, termList, lastMethodUsed) {
		zenFilterBox.innerHTML = "";

		var closeButton = document.createElement("a");
		closeButton.setAttribute("class", "button");
		closeButton.setAttribute("title", lg("close"));
		closeButton.setAttribute("onmouseover", "javascript: this.className='buttonHover';");
		closeButton.setAttribute("onmouseout", "javascript: this.className='button';");
		closeButton.setAttribute("onclick", "javascript: closeZenFilterBox('" + zenFilterBox.getAttribute("id") + "');");
		closeButton.appendChild(document.createTextNode(lg("close")));

		var boxTitle = document.createElement("h1");
		boxTitle.setAttribute("title", "");
		boxTitle.appendChild(document.createTextNode(lg("lblZenFiltered")));
		
		boxTitle.appendChild(closeButton);
		
		zenFilterBox.appendChild(boxTitle);
		
		if (termList.length > 0) {
			// Add the last method used info as title
			boxTitle.setAttribute("title", lg("lblZenFilteredMethod" + lastMethodUsed));
			
			var addAllButton = document.createElement("a");
			addAllButton.setAttribute("class", "button");
			addAllButton.setAttribute("title", lg("titleZenFilterAddAll"));
			addAllButton.setAttribute("onmouseover", "javascript: this.className='buttonHover';");
			addAllButton.setAttribute("onmouseout", "javascript: this.className='button';");
			addAllButton.setAttribute("onclick", "javascript: zenFilterBoxAddAll('" + zenFilterBox.getAttribute("id") + "');");
			addAllButton.appendChild(document.createTextNode(lg("lblZenFilterAddAll")));
			
			boxTitle.appendChild(addAllButton);

			var allTerms = document.createElement("p");
			allTerms.setAttribute("title", "");
			allTerms.setAttribute("class", "terms");

			for (var i=0; i<termList.length; i++) {
				var singleTermElement = document.createElement("a");
				singleTermElement.setAttribute("class", "term");
				singleTermElement.setAttribute("title", lg("titleActionsOnWord"));
				singleTermElement.setAttribute("onmouseover", "javascript: this.className='hover';");
				singleTermElement.setAttribute("onmouseout", "javascript: this.className='term';");
				singleTermElement.appendChild(document.createTextNode(termList[i].textContent));

				allTerms.appendChild(singleTermElement);
				allTerms.appendChild(document.createTextNode(" "));
			}

			zenFilterBox.appendChild(allTerms);
			
			// update context menu binding
			setContextMenu('widgetContextMenu');
		} else {
			var noTerms = document.createElement("p");
			noTerms.setAttribute("class", "terms");
			noTerms.appendChild(document.createTextNode(lg("lblZenFilterNoResults")));
			
			zenFilterBox.appendChild(noTerms);
		}
	}
	
	function closeZenFilterBox(zenFilterBoxId) {
		var zenFilterBox = document.getElementById(zenFilterBoxId);
		zenFilterBox.innerHTML = "";
		zenFilterBox.style.visibility = "hidden";
	}
	
	function zenFilterBoxAddAll(zenFilterBoxId) {
		var zenFilterBox = document.getElementById(zenFilterBoxId);
		var termLinks = zenFilterBox.getElementsByTagName("p")[0].getElementsByTagName("a");
		
		for (var i=0; i<termLinks.length; i++) {
			var correctParent = (typeof parent.isIndexConnected == 'undefined') ? window.opener : parent;
			correctParent.bc_add_breadcrumb_unique(termLinks[i].textContent, 'zen');
		}
	}







//alert('RODINsemfilters.js loaded');