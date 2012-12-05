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




function morphodirect_match(arg1,arg2)
{
  return (arg1.indexOf(arg2)>-1 || (arg2.length < arg1.length && arg2.indexOf(arg1)>-1))
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




function simple_highlight_semfilterresults(txt,highlight)
{
  var hl_color=compute_highlightcolor(txt);
  highlight_semfilterresults(txt,hl_color,highlight);
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
  //alert('compute_highlightcolor: '+txt+' rgb='+rx+':'+r+'->'+r2+' - '+gx+':'+g+'->'+g2+' - '+bx+':'+b+'->'+b2);

  return '#'+r.toString(16)+g.toString(16)+b.toString(16);

}



function highlight_semfilterresults(txt,bgcolor,highlight)
{
  //alert ('checkPassivSemanticFilter ' + txt);
  /* Search for existence of txt in every widget*/
  var morphological_filter = '';
  if (document.forms.famenu.ontomorphofilters)
  {
     morphological_filter = get_morphofilter(parseInt(get_rb_selected_val(document.forms.famenu.ontomorphofilters)),10);
  }
  var match = false;
  var resulttermclassmame="result-word";
  var occurrences_in_results=0;
  var widgets=0;
  var parentIsIndexConnected = ! (typeof parent.isIndexConnected == 'undefined');
  txt=txt.toLowerCase();

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
      var arrALL_RESULT_TERMS = get_all_elems_by_hlclass(obj,resulttermclassmame);

      for(var i=0;i<arrALL_RESULT_TERMS.length;i++)
      {
        var RESULT_TERM= (arrALL_RESULT_TERMS[i]);
        var RESULT_TERM_TXT= RESULT_TERM.innerHTML;

        //eval morphofilter
        eval('match = '+morphological_filter+'("'+RESULT_TERM_TXT.toLowerCase()+'","'+txt+'")');
        if (match)
        {
          occurrences_in_results++;

          if (!in_widget)
          {
            in_widget=true;
            widgets++; /*Count widgets in wich results occurs*/
          }
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
          {
            if (RESULT_TERM.getAttribute('class')==resulttermclassmame+'-hl')
            {
              RESULT_TERM.setAttribute('class',resulttermclassmame);
              RESULT_TERM.style.backgroundColor='';
            }
          }
        }
     } //for
  } //for

  this.occurrences= occurrences_in_results;
  this.widgets= widgets;
}



function count_matching_results(txt,classname)
{
  //alert ('checkPassivSemanticFilter ' + txt);
  /* Search for existence of txt in every widget*/
  var morphological_filter = get_morphofilter(parseInt(get_rb_selected_val(document.forms.famenu.ontomorphofilters)),10);
  var match = false;
  var occurrences_in_results=0;
  var widgets=0;
  var parentIsIndexConnected = ! (typeof parent.isIndexConnected == 'undefined');
  txt=txt.toLowerCase();

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

        //eval morphofilter
        eval('match = '+morphological_filter+'("'+RESULT_TERM_TXT.toLowerCase()+'","'+txt+'")');
        if (match)
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






function hide_un_highlighted_results()
{
  //alert ('checkPassivSemanticFilter ' + txt);
  /* Search for existence of txt in every widget*/
  var resulttermclassmame="result-word-hl";
  var resultclassmame="oo-result-container";
  var occurrences_in_results=0;
  var widgets=0;
  var parentIsIndexConnected = ! (typeof parent.isIndexConnected == 'undefined');

  var tab_id = parentIsIndexConnected ?
      parent.tab[parent.$p.app.tabs.sel].id :
        window.opener.tab[window.opener.$p.app.tabs.sel].id;

  var pertinentIframes = parent.getPertinentIframesInfos(tab_id);
  for(var f=0;f<pertinentIframes.length;f++)
  {
      var iframe=pertinentIframes[f][0];
      //alert('check in iframe with selector '+selector);
      /* Here the conversion from nodelist to array is used for concat */
      var obj=(iframe.localName=='div')?iframe:iframe.contentDocument;
      var arrALL_RESULT = get_all_elems_by_class(obj,resultclassmame);

      for(var i=0;i<arrALL_RESULT.length;i++)
      {
        var RESULT= (arrALL_RESULT[i]);
        var arrALL_HIGHLIGHTED_RESULTTERMS = get_all_elems_by_hlclass(RESULT,resulttermclassmame);
        if (arrALL_HIGHLIGHTED_RESULTTERMS.length==0) //Nothing highlighted?
        {
          jQuery(RESULT.children[0]).hide();
          jQuery(RESULT.children[1]).hide();
        }
        
     } //for
  } //for

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
  RESULT_TERMS =contentdoc.getElementsByClassName(classname,null)
  for(i = RESULT_TERMS.length; i--; arrRESULT_TERMS.unshift(RESULT_TERMS[i]));
  RESULT_TERMS_HL =contentdoc.getElementsByClassName(classname+"-hl",null)
  for(i = RESULT_TERMS_HL.length; i--; arrRESULT_TERMS_HL.unshift(RESULT_TERMS_HL[i]));

  arrALL_RESULT_TERMS = arrRESULT_TERMS.concat(arrRESULT_TERMS_HL);
  return arrALL_RESULT_TERMS;
}


// get_all_elems_by_hlclass(iframe.contentDocument,"result-word")
function get_all_elems_by_class(contentdoc,classname)
{
  var i;
  var RESULT_TERMS    ; var arrRESULT_TERMS = [];
  RESULT_TERMS =contentdoc.getElementsByClassName(classname,null)
  for(i = RESULT_TERMS.length; i--; arrRESULT_TERMS.unshift(RESULT_TERMS[i]));

  return arrRESULT_TERMS;
}



function highlight_and_filter_results(flag)
{
  var t=document.getElementById('facetContextMenuLabel').innerHTML;
  simple_highlight_semfilterresults(t,flag)
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

      eclog('Considering facet term '+FACET_TERM_TXT+' ...');
      //Match with some result?
      var HL = new count_matching_results(FACET_TERM_TXT,resulttermclassmame);
      if (HL.occurrences > 0)
      {
        eclog('Marking facet term '+FACET_TERM_TXT+' as matching');
        if (FACET_TERM.getAttribute('class')==facetclassmame)
        {
          FACET_TERM.setAttribute('class',facetclassmame+'-hl');
          FACET_TERM.setAttribute("title", lg('lblOntoFacetsTermActions2'));
          //hover-->higlight
          FACET_TERM.setAttribute('onmouseover',"simple_highlight_semfilterresults(this.innerHTML,true)");
          FACET_TERM.setAttribute('onmouseout',"simple_highlight_semfilterresults(this.innerHTML,false)");
          //click-->filter
          FACET_TERM.setAttribute('onclick','hide_un_highlighted_results()');
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






function src_widget_morelikethis(obj,ranked_term_raw,term,lang)
{
  var onto_div = obj.parentNode.parentNode.parentNode.parentNode.parentNode;
  var onto_div_id = onto_div.id;
  var ONTO_id = onto_div_id.substr(onto_div_id.lastIndexOf('_')+1);
  
  var group_div = document.getElementById('fb_itemcontent_'+ONTO_id);
  if (!group_div) alert('ALERT: NO OBJECT WITH ID='+'fb_itemcontent_'+ONTO_id);
  var loaded_skos_terms = get_loaded_skos_context(group_div);
  var id = Base64.encode(loaded_skos_terms);
  var path = 'rodin_result';
  
  var solr_add_doc_uri="<?php print $SOLR_ADD_DOC_URI;?>"
              +"?id="+id
              +"&path="+path
              +"&doc="+loaded_skos_terms
              +"&title=rankingcontext1"
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
              'id':id,
              'doc':loaded_skos_terms
             }
           }
        }
  )
}


/**
	 * callbyk after skos context insertion in SOLR
	 * @param response - the response of the called service 
	 * @param vars - the AJAX passed vars
	 */
function handle_post_solr_doc_update(response,vars) {
	var id = vars['id'];
	var doc = vars['doc'];
	
  var slrq='mlt?q=id:'+id+'&mlt.fl=body&fl=score,*&mlt.minwl=3&mlt.mintf=1';
  var solr_path="<?php print $SOLR_RODIN_RESULT_URL;?>";
  var generic_solr_call=solr_path+slrq;
  
  alert('rerank_widget_results_using '
      +'Ontology id '+id
      +'\n\nUSING SKOS KONTEXT:\n\n'+doc
      +'\n\nUSING URL:\n\n'+generic_solr_call);
    
  if (confirm("This will rerank every widget result with respect to the skos context:"
          +"\n\n"+doc
          +"\n\nusing "+generic_solr_call+"\n\nContinue?"))
    {
      slrq_to_widgets(slrq);
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
    if (confirm("This will rerank every widget result with respect to this result \n\n using "+generic_solr_call+"\n\nContinue?"))
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
        
        var  slrq_term=slrq+'&rows='+qs.get('m')+'&fq=wdatasource:'+parseUri(iframe.src).path;

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
			setContextMenu();
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