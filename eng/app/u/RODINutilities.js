// HEG - RODIN
// Javascript Functions
// Autor: Fabio Ricci
// fabio.ricci@ggaweb.ch
// Date : 1.1.2010

// THIS AJAX File must be included 
// form inside a php script with root variables


SRC_REFINE_INTERFACE = new Array;
SRC_CURRENT_INTERFACE_ID =-1;
SRC_CURRENT_INTERFACE_TAB_ID = -1;
ONTOS_SYNCH=-1;
ONTOSEARCH_LOCKED=false;
PORTANEO_TAB_INFO = new Array;

/* foreach extension to Array */
Array.prototype.foreach = function( callback ) {
	  for( var k=0; k<this .length; k++ ) {
	    callback( k, this[ k ] );
	  }
}


function fri_adjust_logo_decoration()
{
	var alogo = document.getElementById('showlogo_fri');//See config.js
	if (alogo != null)
	{
	alogo.style.textDecoration='none';
	alogo.style.color='<?php echo $COLOR_LITTLEVISIBLETEXT;?>';
	}
	//else alert('System error: showlogo_fri Please call your administrator and tell him to update see config.js')
}




function compute_ajax_sid (user)
{
	var sid='';
	var now = new Date();
	var mon = format2pos(now.getMonth() + 1);
	var year= now.getYear() + 1900;
	var day	= format2pos(now.getDate()); //monatstag
	var hour= format2pos(now.getHours());
	var mins =format2pos(now.getMinutes());
	var sec	 =format2pos(now.getSeconds());
	var msec =parent.format3pos(now.getMilliseconds());

	sid = year+''+mon+''+day+'.'+hour+mins+sec+'.'+msec+"."+user;
	
	//alert(sid);
	
	return sid;
};


function compute_ajax_calling_id (user)
{
	var cid='';
	

	cid = compute_ajax_sid (user) +'.ref';
	
	return cid;
};






function format2pos(num)
{
 //alert ('ormat2pos:'+num);
	if (num<10)
		return "0"+num;
	else
		return num;
}

function format3pos(num)
{
	if (num<10)
		return "00"+num;
	else if (num<100)
		return "0"+num;
	else
		return num;
}


  function checksidandsubmit(f,sid,userid,frameId)
  {
	 //alert('FRI: checksidandsubmit('+f+')');
	 var eq=cleanupAJAXstring(f.q.value);
	 if (eq || (f.qe!=undefined && cleanupAJAXstring(f.qe.value)))
	 {
	 	 var alltsidvalue=sid.value;
	 	 sid.value = compute_ajax_sid(userid);
	     //alert('RODINutilities.js::checksidandsubmit(): alte sid: '+alltsidvalue+', neue sid:'+sid.value);
	 }
	  //else alert('RODINutilities.js::checksidandsubmit(): alte sid: '+alltsidvalue+', no q or qe');

	 //alert('checksidandsubmit: go='+f.go.value);
	 //alert(f.action);
	 
	 //thisScript = '<?php print "$WEBROOT/".$_SERVER['SCRIPT_NAME']; ?>';

	 var params=get_form_params(f,'all');
	 //alert('get_form_params liefert: '+params);

	 //Submit to self the new URL
	 var iframe = parent.document.getElementById( frameId );
	 var qs=null;
	 var url=null;
	 if (iframe!=null)
				url = iframe.src;
	 else		url = window.location;
		
	 qs = new Querystring(parseUri(url).query);
		
	 if (qs)
	 {
		qs.set('qe',eq);
		qs.set('q',eq);
		qs.set('m',f.m.value);
		qs.set('rerender',0);
		var host = parseUri(url).protocol 
							+ '://' 
							+ parseUri(url).host
							+ ':'
							+ parseUri(url).port;
		if (host=='://') host= '<?php print $WEBROOT; ?>';
		var newuri=uri+'?'+qs.toString();
		var uri= host 
		+ parseUri(url).path;
		var newuri=uri+'?'+qs.toString()+'&'+params;
		
		//alert('about to reload: '+newuri);
		if (iframe!=null)
			iframe.src=newuri; // and reload!
		else
			window.location.href=newuri;
	 }
 
     //f.submit();
     return true;
  }

  /**
   * This function returns the max time (in seconds) the user can be idle before logging
   * him/her out. The PHP variable is set on root.php.
   */
  function get_max_idle_timeout() {
	  if (<?php print $IDLE_MAXTIMEOUT; ?> < 0)
		  return -1;
	  else
		  return <?php print $IDLE_MAXTIMEOUT; ?>;
  }

	function fri_redirect(datasource,url,frameid,target)
	{
		//alert('fri_redirect('+datasource+','+url+','+frameid+','+target+')');
		var parentIsIndexConnected = ! (typeof parent.isIndexConnected == 'undefined');
		
		if (frameid == -1) {
			var tab_id = parentIsIndexConnected ?
					parent.tab[parent.$p.app.tabs.sel].id :
						window.opener.tab[window.opener.$p.app.tabs.sel].id;
			
			var iframe_discovered=false;
			//try to discover it...
			
			var pertinentIframes = getPertinentIframesInfos(tab_id);			
			for(var i=0;i<pertinentIframes.length;i++)
			{
				var iframe=pertinentIframes[i][0];
				
				if (iframe.src.indexOf(datasource) > 0)
				{
					iframe_discovered=true;
					break;
				}
			}

			if (iframe_discovered) // recursive call
			{
				url = url+'&_x='+iframe.id;
				//alert('recursive: frameid= '+iframe.id);
				fri_redirect(datasource,url,iframe.id,target)
			}
			else
				return window.open(url,target);
		
		}
		else {
			if (parentIsIndexConnected) {
				// open url using frame
				var iframe = parent.document.getElementById(frameid);
				
				if (!iframe) {
					// alert('FRI System error: No iframe found to id=('+frameid+')');
				} else
					iframe.src=url; // automatic redirect
			} else {
				window.location.href=url;
			}
		}
	} // fri_redirect

	/**
	 * Called by the Meta-Search button on the interface, its responsible
	 * for launching the onto-facets-search and the meta-search in parallel.
	 */
	function fri_parallel_metasearch(search, maxresults, db_tab_id, nbcol,
									usr, interactive, cloud, pclass)	{	
		eclog('fri_parallel_metasearch Start (search=['+search+'])');
		
		// Detect language with ontofacets and then launch onto-facets-search
		fb_set_node_ontofacet(search);
		detectLanguageInOntoFacets_launchOntoSearch(search,maxresults,db_tab_id,nbcol,usr,interactive,cloud,pclass);
		
		// Detect language and then launch meta-search
		detectLanguage_launchMetaSearch(search,maxresults,db_tab_id,nbcol,usr,interactive,cloud,pclass);
	}
	
	function detectLanguage_launchMetaSearch(search,maxresults,db_tab_id,nbcol,usr,interactive,cloud,pclass) {
		LANGUAGE_OF_RESULT = '';
		LANGUAGE_OF_RESULT_OK = true;

		var languageDetectorUrl = '<?php print "../../app/u/LanguageDetector.php"; ?>';
		languageDetectorUrl += '?text=' + search;

		$p.ajax.call( languageDetectorUrl, {
			'type':'load',
			'callback': {
				'function':detectLanguageResult_launchMetaSearch,
				'variables': {
					'search':search,
					'maxresults':maxresults,
					'db_tab_id':db_tab_id,
					'nbcol':nbcol,
					'usr':usr,
					'interactive':interactive,
					'cloud':cloud,
					'pclass':pclass
				}
			}
		} );
	}
	
	function detectLanguageResult_launchMetaSearch(response, variables) {
		var analyzedText = response.getElementsByTagName("text")[0].textContent;
		var detectedLanguage = response.getElementsByTagName("language")[0].textContent;
		
		if (detectedLanguage == 'un') {
			parent.LANGUAGE_OF_RESULT_CODED = 'en';
			parent.LANGUAGE_OF_RESULT_OK = false;
		} else {
			parent.LANGUAGE_OF_RESULT_CODED = detectedLanguage;
			parent.LANGUAGE_OF_RESULT_OK = true;
		}

		// Launch meta-search
		var search = variables['search'];
		var maxresults = variables['maxresults'];
		var db_tab_id = variables['db_tab_id'];
		var usr = variables['usr']
		var interactive = variables['interactive']
		var nbcol = variables['nbcol']
		var cloud = variables['cloud']
		var pclass = variables['pclass'];
		
		fri_rodin_metasearch(search,maxresults,db_tab_id,nbcol,usr,interactive,cloud,pclass);
	}
	
	function detectLanguageInOntoFacets_launchOntoSearch(search,maxresults,db_tab_id,nbcol,usr,interactive,cloud,pclass) {
		var ontofacetLanguage = document.getElementById("ontofacet_center_language");
		ontofacetLanguage.value = "un";
		
		var languageDetectorUrl = '<?php print "../../app/u/LanguageDetector.php"; ?>';
		languageDetectorUrl += '?text=' + search;

		$p.ajax.call( languageDetectorUrl, {
			'type':'load',
			'callback': {
				'function':detectResultInOntoFacets_launchOntoSearch,
				'variables': {
					'search':search,
					'maxresults':maxresults,
					'db_tab_id':db_tab_id,
					'nbcol':nbcol,
					'usr':usr,
					'interactive':interactive,
					'cloud':cloud,
					'pclass':pclass
				}
			}
		} );
	}
	
	function detectResultInOntoFacets_launchOntoSearch(response, variables) {
		var analyzedText = response.getElementsByTagName("text")[0].textContent;
		var detectedLanguage = response.getElementsByTagName("language")[0].textContent;
		
		if (detectedLanguage == "un") detectedLanguage = "en";
		
		var ontofacetLanguage = document.getElementById("ontofacet_center_language");
		ontofacetLanguage.value = detectedLanguage;
		parent.LANGUAGE_OF_RESULT_CODED=detectedLanguage;

		// Launch Onto-Search
		var search = variables['search'];
		var maxresults = variables['maxresults'];
		var db_tab_id = variables['db_tab_id'];
		var usr = variables['usr']
		var interactive = variables['interactive']
		var nbcol = variables['nbcol']
		var cloud = variables['cloud']
		var pclass = variables['pclass'];
		launch_fri_onto_metasearch(search,maxresults,db_tab_id,nbcol,usr,interactive,cloud,pclass);
	}
	
	function setLanguageForOntofacets(language) {
		var ontofacetLanguage = document.getElementById("ontofacet_center_language");
		ontofacetLanguage.value = language;
	}
	
	function getLanguageForOntofacets() {
		return document.getElementById("ontofacet_center_language").value;
	}
	
	function launch_fri_metasearch(search,maxresults,db_tab_id,nbcol,usr,interactive,cloud,pclass) {
		eclog('launch_fri_metasearch Start (search=['+search+'])');

		var blankurl ='<?php print "$WEBROOT$RODINU"; ?>/blank.html';
		var calledfromoutsideiframe=false;

		if (cloud) {
			calledfromoutsideiframe=true;
		}

		//fri_rodin_metasearch(search,maxresults,db_tab_id,nbcol,usr,interactive,cloud,pclass)
		pclass.ajax.call( blankurl,
				{
			'type':'load',
			'callback':
			{
				'function':fri_rodin_metasearch_wrap,
				'variables':
				{
					'search':search,
					'maxresults':maxresults,
					'db_tab_id':db_tab_id,
					'nbcol':nbcol,
					'usr':usr,
					'interactive':interactive,
					'cloud':cloud,
					'pclass':pclass
				}
			}
		}
		);		 // fri_rodin_metasearch_wrap(blankurl,variables)
	} // launch_fri_metasearch
	
	
	
	function launch_fri_onto_metasearch(search,maxresults,db_tab_id,nbcol,usr,interactive,cloud,pclass) {
		var calledfromoutsideiframe=false;
		var blankurl ='<?php print "$WEBROOT$RODINU"; ?>/blank.html';
		wtslog('launch_fri_onto_metasearch (INIT)): ','ONTOS_SYNCH',ONTOS_SYNCH,0,0);
		ONTOS_SYNCH=0;

		eclog('launch_fri_onto_metasearch Start (terms=['+search+'])');

		pclass.ajax.call( blankurl, {
			'type':'load',
			'callback': {
				'function':fri_rodin_do_onto_search_wrap,
				'variables': {
					'terms':search,
					'calledfromoutsideiframe':calledfromoutsideiframe,
					'pclass':pclass
				}
			}
		});
	}

	
	
	
	
	function lock_ontosearch_dialog()
	{
		//ONTOSEARCH_LOCKED=true; //Rene 28.8.2011: No lock
		//var refining_icon = $("#refining_busy_info2");
		//refining_icon.attr("src", "<?php echo $IMG_REFINING; ?>");
		
		var refining_td_info=parent.document.getElementById('refining_busy_info2');
		refining_td_info.src="<?php echo $IMG_REFINING; ?>";
		refining_td_info.title="<?php echo $IMG_REFINING_TITLE; ?>";
		
		//Disable Ontofacets search
		/*var searchTextField = document.getElementById('ontofacet_center');
		searchTextField.disabled = true;
		var searchIcon = document.getElementById('ontofacet_center_search');
		searchIcon.style.width='0px';
		searchIcon.style.visibility='hidden';
		var waitIcon = document.getElementById('ontofacet_center_wait');
		waitIcon.style.width='auto';
		waitIcon.style.visibility='visible';*/
	}
	
	function unlock_ontosearch_dialog(what)
	{
		var ontofacetON = parent.document.getElementById('top_facet_on');
		var ontofacetTXT = parent.document.getElementById('top_facet_text');
		
		if (what=='onto')
		//Some results came -> reactivate black label
		{
			if (ontofacetON)
			{	
				//delete extra style to mark end of search
				ontofacetON.style.removeProperty('color');
			}
			
			if (ontofacetTXT)
			{
				//delete extra style to mark end of search
				ontofacetTXT.style.removeProperty('color');
			}
		}
		
		//signalisiere, dass (alle) Berechnungen fertig...
//		var refining_icon = $("#refining_busy_info2");
//		refining_icon.attr("src", "<?php echo $IMG_REFINING_DONE; ?>");
		
		var refining_td_info=parent.document.getElementById('refining_busy_info2');
		refining_td_info.src = "<?php echo $IMG_REFINING_DONE; ?>";
		refining_td_info.title="";
		
		ONTOSEARCH_LOCKED=false;
		
		//Re-enable Ontofacets search icon
		/*var waitIcon = document.getElementById('ontofacet_center_wait');
		waitIcon.style.width='0px';
		waitIcon.style.visibility='hidden';
		var searchIcon = document.getElementById('ontofacet_center_search');
		searchIcon.style.width='auto';
		searchIcon.style.visibility='visible';
		var searchTextField = document.getElementById('ontofacet_center');
		searchTextField.disabled = false;*/
	}
	
	
	
	
	/* FILL NEXT FUNCTIONS WITH ACTIONS: */
	function perform_aftersearch_actions(what)
	{
		//alert('aftersearch_actions:  '+what);
		unlock_ontosearch_dialog(what);
	}
	
	
	
	function fri_rodin_metasearch_wrap(blankurlresult,variables)
	{
		var search			=variables['search'];
		var maxresults		=variables['maxresults'];
		var db_tab_id		=variables['db_tab_id'];
		var nbcol 			=variables['nbcol']
		var usr 			=variables['usr']
		var interactive 	=variables['interactive']
		var nbcol 			=variables['nbcol']
		var nbcol 			=variables['nbcol']
		var cloud 			=variables['cloud']
		var pclass			=variables['pclass'];
		
		eclog('fri_rodin_metasearch_wrap Start (search=['+search+'])');
		fri_rodin_metasearch(search,maxresults,db_tab_id,nbcol,usr,interactive,cloud,pclass);
	}
	
	/**
	 * Calls a parallel meta-search in all widgets in the current tab by giving their
	 * iFrames a search URL (with parameter 'q' with the search text).
	 * 1. Computing SID
	 * 2. Send URL to each widget's iFrame
	 */
	function fri_rodin_metasearch(search,maxresults,db_tab_id,nbcol,usr,interactive,cloud,pclass) {
		eclog('fri_rodin_metasearch(' 
				+'\n'+'search='+search
				+'\n'+'maxresults='+maxresults
				+'\n'+'db_tab_id='+db_tab_id
				+'\n'+'nbcol='+nbcol
				+'\n'+'usr='+usr
				+'\n'+'interactive='+interactive
				+'\n'+'cloud='+cloud
				+'\n'+'pclass='+pclass);
		
		var searchtxt = '';
		
		if (cloud) {/* take from cloud! widget? */
			searchtxt = search;
			if (maxresults == -1)
			maxresults = parent.document.getElementById('rodinsearch_m').value;
		} else {
			searchtxt = get_search_text();
		}

		eclog('fri_rodin_metasearch Start (searchtxt=['+searchtxt+'])');
		
		var helptext='Your search here';
		if (interactive && !cloud) {
			if (!searchtxt || searchtxt==helptext) {
				alert(lg('lblPleasesomesearchwords'));
				search.value=helptext;
				return false;
			}
		}
		
		// Compute the correct db_tab_id so that we can
		// identify the widgets in the "current" tab.
		if (db_tab_id != -1) {
			tab_id = get_tab_id(db_tab_id,pclass.app.tabs); 
			pclass.app.tabs.select(tab_id);
			pclass.app.tabs.nav.init();
			pclass.app.tabs.nav.left();
		}
		
		if (db_tab_id == -1) {
			tab_id = pclass.app.tabs.sel;
			db_tab_id = parent.tab[tab_id].id;
		}
		
		if (nbcol == -1)
			nbcol = parent.tab[tab_id].cols.length - 1;
		
		// TODO Check why is this here.
		pclass.app.tabs.select(tab_id);
	
		// TODO Cheky why/when is this necessary.
		var m = parent.document.getElementById('rodinsearch_m');
		m.value = maxresults; 
	
		// 1. Compute SID
		SID = compute_ajax_sid(usr);
		
		// Launch the protective pop-up
		var html_message='';
		var msgtxt = '<div style="padding-left: 6px; padding-right: 28px;">'
			+ '<img src="<?php print $RODINUTILITIES_GEN_URL; ?>/images/ico_waiting.gif"'
			+ 'style="position: absolute; bottom: 18px; right:16px;">'
			+ parent.lg('lblCollectingResultsUpTo', maxresults)
			+ parent.lg('lblCollectingResultsWait', searchtxt) + '</div>';
		
		if (interactive) {
			pclass.app.popup.fadeinFRI(msgtxt,500,100,html_message);
		}
		
		// Find the current tab's widget's iFrames
		var pertinentIframes;
		if (tab_id >= 0) {
			pertinentIframes = getPertinentIframesInfos(db_tab_id);
		}

		if (pertinentIframes.length == 0) {
			FRIdarkProtectionUncache();
			alert(lg("msgPleaseAddWidgetsToTab"));	
			return false;
		}
		
		// 2. Send URL to each widget
		if (tab_id >= 0) {
			var width = Math.max(316, Math.round(parseInt(parent.document.getElementById("modules").offsetWidth) / nbcol)) - 16;
			
			var iframes = new Array;
			var iframe_width = new Array;
			var iframe_height = new Array;
			
			var i;
			for(i=0;i<pertinentIframes.length;i++) {
				var iframeInfo=pertinentIframes[i];

				var x=0;
				var iframex = iframeInfo[x++];
				var title = iframeInfo[x++];
				var height = parseInt(iframeInfo[x++]);

				if (title!='RODIN PARALLEL SEARCH') {
					iframes.push(iframex);
					iframe_width.push(width);
					iframe_height.push(height);
				}
			}

			for( i=0; i<iframes.length; i++) {
				var iframe = iframes[i];
				var _w = iframe_width[i];
				var _h = iframe_height[i];

				// get application (widget) name
				var rodinwidgetname = parseUri(iframe.src).file;

				// cancel any q, subst qe value:
				old_querystring = parseUri(iframe.src).query;
				var qs = new Querystring(old_querystring);
				if (qs.contains('q')) qs.set('q','');
				
				if (!qs.contains('app_id')) {
					var tab_db_id = get_tab_db_id(iframe.id);
					var app_id=parent.$p.app.user.id
					+':'+ tab_db_id
					+':'+ qs.get('p');
					qs.set('app_id',app_id);
				}
				
				qs.set('qe',searchtxt);
				qs.set('q',searchtxt);
				qs.set('sid',SID);
				qs.set('go','1');
				qs.set('textZoomRender', document.getElementById("selectedTextZoom").value);
				qs.set('rerender',0);
				qs.set('show','RDW_widget');
				qs.set('m',maxresults);
				qs.set('_w',_w);
				qs.set('_h',_h);
				qs.set('_x',iframe.id);
				qs.set('user_id',parent.$p.app.user.id);

				if (parent.NOSRC)
					qs.set('nosrc','1');

				if (i==iframes.length - 1) {
					qs.set('uncache',true);
				}
				
				var server = '<?php print $WEBROOT; ?>';
				var newUrl = server + parseUri(iframe.src).path + '?' + qs.toString();
				
				iframe.src = newUrl; // Automatically reloads the iFrame!
			}
		}

		return false;
	} // fri_rodin_metasearch

	
	
function fri_rodin_do_onto_search_wrap(blankurl,variables) {
	var terms = variables['terms'];
	var pclass = variables['pclass'];	
	var calledfromoutsideiframe = variables['calledfromoutsideiframe'];	
	
	var lang = getLanguageForOntofacets();

	eclog('fri_rodin_do_onto_search_wrap Start (terms=['+terms+'])');

	fri_rodin_do_onto_search(terms,lang,calledfromoutsideiframe,pclass);
}

/**
 * NB. Called from document level
 * mode=='direct' => instruct SRC to respond directly (through action)
 * mode=='segmented' => instruct SRC to respond segmented (through action)
 */
function fri_rodin_do_onto_search(terms,lang,calledfromoutsideiframe,pclass)
{
	
	if (parent.NOSRC)
		alert('Problem on finding initialized SRC Modules');
	else
	{
		lock_ontosearch_dialog();
		var srccallverbosity= ('<?php print $RODINSEGMENT;?>'=='st' && 0);
		
		var firstuse = (typeof(gui_refinement_request) == 'undefined');
	
		/* Recompute called without ZEN */
		if (firstuse)
		{
			var this_wid_uniq_id=0;
			var quickq=terms;
			//if (quickq=='') alert('fri_rodin_do_onto_search: empty terms found');
			var maxdur=<?php print $WIDGET_SEARCH_MAX;?>;
			var c='c';
	
			gui_refinement_request=new Object; //global!!
			gui_refinement_request['this_wid_uniq_id']=this_wid_uniq_id;
			gui_refinement_request['maxdur']=maxdur;
			gui_refinement_request['c']=c;
		}
		gui_refinement_request['quickq']=terms; //use the current terms for recalc ontologies
		
		eclog('fri_rodin_do_onto_search Start (terms=['+terms+'])');
		
		/************************
		in the cache protection,
		repeat the semrefinement
		using terms.value as input
		************************/
		//tab_id: the seq of the tab containing the calling module
		var tab_id=pclass.app.tabs.sel; //current tab. 
		// USE gui_refinement_request //global!!
		
		//signalisiere, dass Berechnung zugange...
//		var refining_icon = parent.$("#refining_busy_info2");
//		refining_icon.attr("src", "<?php echo $IMG_REFINING; ?>");
		
		var refining_td_info=parent.document.getElementById('refining_busy_info2');
		refining_td_info.innerHTML='<?php echo $IMG_REFINING_BUSY; ?>';
		refining_td_info.title='<?php echo $IMG_REFINING_TITLE; ?>';

		wtslog('fri_rodin_do_onto_search: ','ONTOS_SYNCH',ONTOS_SYNCH,0,0);
		ONTOS_SYNCH=0; // SYNC INFRA -> dangerous if htere are other processes still running... 
			
		//LAUNCH THE SEMREF INTERFACE TO COMPUTE TERMS
		//FOREACH SRC INTEFRACE ONE LAUNCH IN PARALLEL
		
		for(var k=0;k<parent.SRC_REFINE_INTERFACE.length;k++)
		{
			var src_service_name = parent.SRC_REFINE_INTERFACE[k]['name'];
			var src_service_mode = parent.SRC_REFINE_INTERFACE[k]['mode'];
			var service_id = parent.SRC_REFINE_INTERFACE[k]['id'];
			var src_service_url = parent.SRC_REFINE_INTERFACE[k]['url'];
			var	interface_initialized=parent.SRC_REFINE_INTERFACE[k]['initialized'];
			
			var now = new Date();
			var msec =parent.format3pos(now.getMilliseconds());
			var sid = compute_ajax_sid (pclass.app.user.id);
			var cid = sid +'.'+msec +'.' + this_wid_uniq_id;
			var newsid=sid; // keine Query
	
	
			if (interface_initialized)
			{
				var params='';
				var url_bridge='<?php print "$SRC_INTERFACE_BASE_URL/refine/index.php"; ?>' 
				var action = 'pre';
				var funcname = 'parent.fri_rodin_start_ontosearch_context_skos';
				var url 	 = url_bridge+params;
				var url2show = src_service_url+params;
	
				if (src_service_mode=='direct')
				{
					//alert('direct');
					//------------------------- PREPROCESSING ----------------------
					//Default: Instruct SRC to respond with a pre action and then r/n/b
					action = 'preall';
					funcname = parent.handle_received_src_data;
					ONTOS_SYNCH+=1;
				} // mode=='direct'
				else 
				if (src_service_mode=='segmented')
				{
					//alert('segmented');
					//------------------------- DIRECT SRC CALL ----------------------
					// action==preall -> instruct to preprocess and to compute r/b/n
					// action=all -> instruct to compute r/b/n (no preprocess)
					action = 'pre';
					funcname = parent.fri_rodin_start_ontosearch_context_skos;
				} // mode=='segmented'

				var params=
					'?action='+action
					+'&sid='+sid
					+'&cid='+cid+'.p'
					+'&newsid='+newsid
					+'&q=' /*dismissed*/
					+'&v='+Base64.encode(gui_refinement_request['quickq']) /*the current one*/
					+'&l='+lang
					+'&w='+gui_refinement_request['this_wid_uniq_id']
					+'&maxdur='+gui_refinement_request['maxdur']
					+'&c='+gui_refinement_request['c']
					+'&service_id='+service_id
		            +'&user='+pclass.app.user.id
					;
				url 	 = url_bridge+params;
				
				//CALL SRC
				if (srccallverbosity) alert("CALL SRC (preprocessing) WITH: "+url2show);
				//alert('fri_rodin_do_onto_search: pushing call PRE('+url+') with gui_refinement_request[quickq]='+gui_refinement_request['quickq'] + ' and callback fri_rodin_start_ontosearch_context_skos');

				pclass.ajax.call( url,
									{
										'type':'load',
										'callback':
										 {
											'function':funcname,
											'variables':
											 {
												'sid':sid,
												'cid':cid,
												'newsid':newsid,
												'l':lang,
												'service_id':service_id,
												'src_service_name':src_service_name,
												'src_service_url':src_service_url,
												'this_wid_uniq_id':gui_refinement_request['this_wid_uniq_id'],
												'calledfromoutsideiframe':calledfromoutsideiframe,
												'pclass':pclass
											 }
										 }
									}
				);	
				
			} // interface_initialized
		} //foreach SRC Interface
	} // NOSRC
	return false;
} /*fri_rodin_do_onto_search*/


	
	
	
	
	
	
	
	
	function fri_rodin_start_ontosearch_context_skos(response,variables)
	{
		/************************
		Receive (XML) in srv the preprocessed terms
		Call the three SKOS refinining engines using the submitted words
		************************/
		//tab_id: the seq of the tab containing the calling module
		var pclass				=variables['pclass'];
		var module				=variables['module'];
		var newsid				=variables['newsid'];
		var service_id		=variables['service_id'];
		var src_service_name 	=variables['src_service_name']
		var src_service_url 	=variables['src_service_url']
        var this_wid_uniq_id	=variables['this_wid_uniq_id'];
		var calledfromoutsideiframe=variables['calledfromoutsideiframe'];
		var lang				=variables['l'];

		var response_exists=false;
		var response_xmlwellformed_exists=false;
		var error_response_malformedxml=false;
		var timeout	='no';
		var cid		='';
		var sid 	='';
		var action 	='';
		var c		='';
		
		var maxDur	='';
		var rts		='';
		var cdur	='';
		
		var v		='';
		var q		='';
		var srv		='';
		var preprocessed_terms = '';
		var searchtxt='(xml error)';
			
		//alert('fri_rodin_start_ontosearch_context_skos');
		var srccallverbosity= ('<?php print $RODINSEGMENT;?>'=='st' && 0);
		parent.fb_init_src_display(service_id,'broader');
		parent.fb_init_src_display(service_id,'narrower');
		parent.fb_init_src_display(service_id,'related');
		
		if (response!=null)
		{
			response_exists= response.getElementsByTagName("*")[0] ;
			response_xmlwellformed_exists= response.getElementsByTagName("refine")[0] ;
			error_response_malformedxml = (response_exists && !response_xmlwellformed_exists);
			
			if (response_exists) /*XML*/
			{
				/*
				 cid		=(response.getElementsByTagName("cid")[0]).textContent;
				 sid		=(response.getElementsByTagName("sid")[0]).textContent;
				 c			=(response.getElementsByTagName("c")[0]).textContent;
		
				 maxDur		=(response.getElementsByTagName("maxDur")[0]).textContent;
				 rts		=(response.getElementsByTagName("rts")[0]).textContent;
				 cdur		=(response.getElementsByTagName("cdur")[0]).textContent;
				 action		=(response.getElementsByTagName("action")[0]).textContent;
		
				 v			= Base64.decode( pclass.string.trim( ( response.getElementsByTagName("v")[0]).textContent ) );
				*/
				 var tags		=(response.getElementsByTagName("*"));
				 for(var i=0;i<tags.length;i++)
				 {
					 var tag=tags[i];
					 if (tag.tagName=='timeout')
					 {
						 timeout=tag.textContent;
					 }
					 else 
					 if (tag.tagName=='action')
					 {
						 action=tag.textContent;
					 }
				 }
				var now = new Date();
				var msec =parent.format3pos(now.getMilliseconds()); //undefined???
				var sid = parent.gui_refinement_request['sid'];
				var cid = sid +'.'+msec +'.' + this_wid_uniq_id;
				var newsid=sid; 
				
				var url_bridge='<?php print "$SRC_INTERFACE_BASE_URL/refine/index.php"; ?>' 
				
				 if (response_xmlwellformed_exists)
				 {
					preprocessed_terms_base64_coded = pclass.string.trim( ( response.getElementsByTagName("srv")[0]).textContent );
					preprocessed_terms = pclass.string.trim(parent.Base64.decode(preprocessed_terms_base64_coded));
				 } // response_xmlwellformed_exists
				if (error_response_malformedxml)
				{
					preprocessed_terms_base64_coded = pclass.string.trim( 'Error' );
					preprocessed_terms = pclass.string.trim(parent.Base64.decode(preprocessed_terms_base64_coded));
					//alert("preprocessed_terms: "+preprocessed_terms);
				}
				
				eclog('fri_rodin_start_ontosearch_context_skos Start (service_id='+service_id+', preprocessed_terms=['+preprocessed_terms+'])');

				
				var callparams = '&sid='+sid
				+'&newsid='+newsid
				+'&q=' /*dismissed*/
				+'&v='+preprocessed_terms_base64_coded
				+'&l='+lang
				+'&w='+this_wid_uniq_id
				+'&maxdur='+parent.gui_refinement_request['maxdur']
				+'&c='+parent.gui_refinement_request['c']
				+'&service_id='+service_id
                +'&user='+pclass.app.user.id
				;

				
				if (preprocessed_terms=='' || timeout!='no' || error_response_malformedxml)
				{
					//Use DUMMY
					//------------------------- DUMMY ----------------------
					if (error_response_malformedxml)
						params = '?action=xmlerror' + callparams;
					else if (timeout!='no')
					{
						callparams+='&timeout='+timeout;
						params = '?action=dummytimeout' + callparams;
					}
					else if (timeout=='no')
						params = '?action=dummy' + callparams;
					
					var url 	 = url_bridge+params;
					var url2show = src_service_url+params;
					
					if (srccallverbosity) alert("CALL SRC (broader): "+url2show);
					
					wtslog('fri_rodin_do_onto_search (start with MALFORMED/TIMEOUT/ERROR) service_id='+service_id+'): ','ONTOS_SYNCH',ONTOS_SYNCH,ONTOS_SYNCH+1,1);
					ONTOS_SYNCH=ONTOS_SYNCH+1;
					
					//CALL SRC
					pclass.ajax.call( url,
								{
									'type':'load',
									'callback':
									 {
										'function':parent.handle_received_src_data,
										'variables':
										 {
											'cid':cid,
											'newsid':newsid,
											'service_id':service_id,
											'pclass':pclass
										 }
									 }
								}
					);				
				}
				else /* Content */
				{
					
					if (action=='pre')
					{
						 //LAUNCH THE SEMREF INTERFACE TO COMPUTE TERMS
						
						//------------------------- BROADER ----------------------
						var params = '?action=broader' + callparams+'&cid='+cid+'.b';
						var url 	 = url_bridge+params;
						var url2show = src_service_url+params;
						
						if (srccallverbosity) alert("CALL SRC (broader): "+url2show);
	
						wtslog('fri_rodin_do_onto_search (start broader) service_id='+service_id+'): ','ONTOS_SYNCH',ONTOS_SYNCH,ONTOS_SYNCH+1,1);
						ONTOS_SYNCH=ONTOS_SYNCH+1;
						
						//CALL SRC
						pclass.ajax.call( url,
									{
										'type':'load',
										'callback':
										 {
											'function':parent.handle_received_src_data,
											'variables':
											 {
												'cid':cid,
												'newsid':newsid,
												'service_id':service_id,
												'pclass':pclass
											 }
										 }
									}
						);						
						
						//------------------------- NARROWER ----------------------
						var params = '?action=narrower' + callparams+'&cid='+cid+'.n';
						var url 	 = url_bridge+params;
						var url2show = src_service_url+params;
						
						if (srccallverbosity) alert("CALL SRC (narrower): "+url2show);
						
						wtslog('fri_rodin_do_onto_search (start narrower) service_id='+service_id+'): ','ONTOS_SYNCH',ONTOS_SYNCH,ONTOS_SYNCH+1,1);
						ONTOS_SYNCH=ONTOS_SYNCH+1;
						
						//CALL SRC
						pclass.ajax.call( url,
									{
										'type':'load',
										'callback':
										 {
											'function':parent.handle_received_src_data,
											'variables':
											 {
												'cid':cid,
												'newsid':newsid,
												'service_id':service_id,
												'pclass':pclass
											 }
										 }
									}
						);		
						
						
						//------------------------- RELATED ----------------------
						var params = '?action=related' + callparams+'&cid='+cid+'.r';
						var url 	 = url_bridge+params;
						var url2show = src_service_url+params;
						
						if (srccallverbosity) alert("CALL SRC (related): "+url2show);
						
						wtslog('fri_rodin_do_onto_search (start related) service_id='+service_id+'): ','ONTOS_SYNCH',ONTOS_SYNCH,ONTOS_SYNCH+1,1);
						ONTOS_SYNCH=ONTOS_SYNCH+1;
						
						//CALL SRC
						pclass.ajax.call( url,
									{
										'type':'load',
										'callback':
										 {
											'function':parent.handle_received_src_data,
											'variables':
											 {
												'cid':cid,
												'newsid':newsid,
												'service_id':service_id,
												'pclass':pclass
											 }
										 }
									}
						);		
					} // action=='pre'
					
					else if (action=='preall')
					{
						//content has already all
						
						//------------------------- FALSCH ----------------------
						var params = '?action=preall' + callparams+'&cid='+cid+'.b';
						var url 	 = url_bridge+params;
						var url2show = src_service_url+params;
						
						if (srccallverbosity) alert("CALL SRC (broader): "+url2show);
	
						wtslog('fri_rodin_do_onto_search (start broader) service_id='+service_id+'): ','ONTOS_SYNCH',ONTOS_SYNCH,ONTOS_SYNCH+1,1);
						ONTOS_SYNCH=ONTOS_SYNCH+1;
						
						//CALL SRC
						pclass.ajax.call( url,
									{
										'type':'load',
										'callback':
										 {
											'function':parent.handle_received_src_data,
											'variables':
											 {
												'cid':cid,
												'newsid':newsid,
												'service_id':service_id,
												'pclass':pclass
											 }
										 }
									}
						);						
					} // 
						
						
						
						
				}
			}
		}
	
		//wtslog('fri_rodin_do_onto_search (ending method after having launched other methods, service_id='+service_id+'): ','ONTOS_SYNCH',ONTOS_SYNCH,ONTOS_SYNCH-1,-1);
		//ONTOS_SYNCH=ONTOS_SYNCH - 1;

		return false;
	} /*fri_rodin_start_ontosearch_context_skos*/


	
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



	
	
	
	
	
	function getAbsolutePosition(element) {
    var r = { x: element.offsetLeft, y: element.offsetTop };
    if (element.offsetParent) 
		{
      var tmp = getAbsolutePosition(element.offsetParent);
      r.x += tmp.x;
      r.y += tmp.y;
    }
    return r;
  };
	
	

  
  function fri_popup_help_src_choice()
  {
	 alert('fri_popup_help_src_choice - IMPLEMENT ME - Zeige eine Helpseite zum Dialog')
  }
  

  
  function fri_popup_expl_src_choice()
  {
	  /*
	  var q=document.getElementById('query_res').innerHTML;
	  var r=document.getElementById('res_res').value;//textarea now
	  */
	  var id_src=parent.SRC_CURRENT_INTERFACE_ID;
	  var i=parent.SRC_CURRENT_INTERFACE_TAB_ID;
	  var url= parent.SRC_REFINE_INTERFACE[i]['test'];
	  var engine= parent.SRC_REFINE_INTERFACE[i]['engine'];
	  var lang= parent.LANGUAGE_OF_RESULT_CODED;
	  
	  var allurl=url+'&terms='+r+'&lang=en'+'&action=rel%2Bbr%2Bnarr'+'&lang='+lang;

	 //alert(allurl )
  
	 window.open(allurl,'_blank');
  
  }
  
	


	//*************************
	//*************************
	//*************************
	function fri_initialize_src()
	/*
	 * Calld to initialize each SRC
	 * It sends a small message to each (configured) SRC
	 * and react to the response enabling the SRC on the GUI
	 */
	{	
		var tab_seq				= $p.app.tabs.sel;
		//var modules 			= getModules(tab_seq);
		
		if (!NOSRC) /* In case there is at least one configured SRC */
		{
			for(var k=0;k<SRC_REFINE_INTERFACE.length;k++)
			{
				var interface_id		= SRC_REFINE_INTERFACE[k]['id'];
				var interface_name		= SRC_REFINE_INTERFACE[k]['name'];
				var user_id				= $p.app.user.id;
				var src_params			= "?user_id="+user_id+"&service_id="+interface_id;
				var local_interface_url	= "<?php echo $LOCAL_SRC_START_INTERFACE; ?>"+src_params;
				var SRCitemNamenExpander= document.getElementById("fb_itemname_expander_"+interface_id);
				var SRCitemNamenExpander2= document.getElementById("fb_itemname_expander2_"+interface_id);
				var onclick=SRCitemNamenExpander.onclick; SRCitemNamenExpander.onclick='';
				var onclick2=SRCitemNamenExpander2.onclick; SRCitemNamenExpander2.onclick='';
				var title=SRCitemNamenExpander.title; SRCitemNamenExpander.title=lg('lblSRCuninitialized');
				var title2=SRCitemNamenExpander2.title; SRCitemNamenExpander2.title=lg('lblSRCuninitialized');
				
				//alert('call2 '+local_interface_url);
				
				/*CALL SRC START INTERFACE*/
				$p.ajax.call( local_interface_url,
								{
									'type':'load',
									'callback':
									 {
										'function':react_to_response_to_fri_initialize_src,
										'variables':
										 {
											'user_id':user_id,
											'SRCitemNamenExpander':SRCitemNamenExpander,
											'SRCitemNamenExpander2':SRCitemNamenExpander2,
											'onclick':onclick,
											'onclick2':onclick2,
											'title':title,
											'title2':title2,
											'interface_name':interface_name,
											'service_id':interface_id
										 }
									 }
								}
				);	
				/*alert('CALL fri_initialize_src('+interface_name+')='+local_interface_url);*/
			} /*for*/
		} /*NOSRC*/
			
		return false;
	} // fri_init_src

	
	
	
	
	function fri_warn_nosrc()
	{
		/* LANGUAGE !!! */
		if (NOSRC) alert('NO Semantic Refinement Component was found for this session- Olny RODIN metasearch possible. \n\nContact your RODIN system administrator for more information.');
		
	}
	
		
	
		//*************************
		//*************************
		//*************************
		function react_to_response_to_fri_initialize_src(response,vars)
		{
		var user_id					=vars['user_id'];
		var SRCitemNamenExpander	=vars['SRCitemNamenExpander'];
		var SRCitemNamenExpander2	=vars['SRCitemNamenExpander2'];
		var onclick					=vars['onclick'];
		var onclick2				=vars['onclick2'];
		var title					=vars['title'];
		var title2					=vars['title2'];
		var interface_name			=vars['interface_name'];
		var service_id				=vars['service_id'];
		var INTERFA 				= parent.SRC_REFINE_INTERFACE;
		var errormessage			='';
		var timeout					='';
		var user					='';
	
		
		if (response!=null)
		{
			if (response.getElementsByTagName("src_init_response")[0])
			{
				for(var k=0;k<INTERFA.length;k++)
				{
					var xservice_id = SRC_REFINE_INTERFACE[k]['id'];
					if (xservice_id == service_id)
					{
						errormessage='';
						timeout='';
						 var tags =(response.getElementsByTagName("*"));
						 for(var i=0;i<tags.length;i++)
						 {
							var tag=tags[i];
							//alert('t: '+tag.tagName+'='+tag.textContent);
							if (tag.tagName=='error')
							{
								errormessage=tag.textContent;
								break;
							}
							else if (tag.tagName=='user')
							{
								user=tag.textContent;
								break;
							}
								 
						}
						if (errormessage!='')
						{	/*DoNot initialize but inform sth wrong*/
							errormessage+='\n Please retry later.';
							SRCitemNamenExpander2.setAttribute('class','facetcontrol-td-error');
							SRCitemNamenExpander2.setAttribute('title',errormessage);
						}
						else if (user==user_id)
						{	/*init SRC*/
							var interf=SRC_REFINE_INTERFACE[k];
							interf['initialized']=true;
							/*alert('react_to_response_to_fri_initialize_src('+interface_name+','+service_id+' '+INTERFA.length+')');*/
							SRCitemNamenExpander.className='fb-expander';
							SRCitemNamenExpander.onclick = onclick;
							SRCitemNamenExpander.title = title;
							
							SRCitemNamenExpander2.className='facetcontrol-td';
							SRCitemNamenExpander2.onclick = onclick2;
							SRCitemNamenExpander2.setAttribute('title',lg("ontodatasourceinitialized"));
						}
						/*else alert('user:'+user+' != user_id:'+user_id);*/
					}
				} // for
			} //src_started
		}
	
		return false;
	} // react_to_fri_init_src
		
	

/**
 * AJAX callback function handling SRC responses. 
 */
function handle_received_src_data(response,vars) {
	var pclass = vars['pclass'];
	var module = vars['module'];
	var newsid = vars['newsid'];
	var service_id = vars['service_id'];
	var newtab_db_id = vars['newtab_db_id'];
	var NO_RESULTS_FOUND = 'Sorry, no refinement found';
	
	var timeout = 'no';
	var error = false;
	var cid = '';
	var sid = '';
	var action = '';
	var c = '';

	var maxDur = '';
	var rts = '';
	var cdur = '';

	var v = '';
	var q = '';
	var srv = '';
	var srv64 = '';
	var searchtxt = '(xml error)';
	
	var results = '';
	var results_raw = '';
	var we_had_some_results = false;
		
	if (response!=null) {
		if (response.getElementsByTagName("refine")[0]) /*XML*/
		{
			 var tags =(response.getElementsByTagName("*"));
			 for(var i=0;i<tags.length;i++)
			 {
				 var tag=tags[i];
				 //alert('t: '+tag.tagName+'='+tag.textContent);
				 if (tag.tagName=='timeout')
				 {
					timeout=tag.textContent;
					break;
				 }
			 }
			 
			 //preall
			 action = (response.getElementsByTagName("action")[0]).textContent;

			 if (timeout == 'no') {
				 cid = (response.getElementsByTagName("cid")[0]).textContent;
				 sid = (response.getElementsByTagName("sid")[0]).textContent;
				 c = (response.getElementsByTagName("c")[0]).textContent;
		
				 maxDur = (response.getElementsByTagName("maxDur")[0]).textContent;
				 rts = (response.getElementsByTagName("rts")[0]).textContent;
				 cdur = (response.getElementsByTagName("cdur")[0]).textContent;

				 v = parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("v")[0]).textContent ) );
				 q = parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("q")[0]).textContent ) );
				 srv = parent.html_entity_decode ( parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("srv")[0]).textContent ) ) );

				 results_raw = '';
				 if (response.getElementsByTagName("srv_raw")[0]) {
					 srv_raw = parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("srv_raw")[0]).textContent ) );
					 if (srv_raw!='') {
						 //alert('SRC supplied srv_raw: <'+srv_raw+'> action='+action);
						 /*srv_raw contains a sequence of base64encoded terms separated by a SEPARATOR =comma*/
						 results_raw = pclass.string.trim( srv_raw );
					 }
				 }					 
				 
				 results = pclass.string.trim( ( response.getElementsByTagName("srv")[0]).textContent );
				 
				 if (srv == '') {
					 srv = NO_RESULTS_FOUND;
				 }
				 
				 searchtxt = srv;
			 } else {
				 srv = lg("lblSRCTimeout");
				 srv64 = parent.Base64.encode(srv);
			 }
		} else {
			// Mal-formerd XML?
			error = true;
			srv = 'Sorry, this engine seems to respond with errors.'; 
		}
	}
	
	eclog('handle_received_src_data (service_id='+service_id+', srv=['+srv+'], v=['+v+'])');

	//Set values to be shown
	if (typeof(results)!='undefined') {
		if (timeout != 'no') { // On timeout
			if (action=='broader' || action=='narrower' ||  action=='related') {
				parent.fb_facetboard_set_timeoutinfo(service_id, action, srv64);
			} else if (action=='preall' || action=='all') {
				parent.fb_facetboard_set_timeoutinfo(service_id, action, srv64);
			}
		} else {	
			if (action=='broader' || action=='narrower' ||  action=='related') {
				parent.fb_add_to_facetboard(service_id,action,results,results_raw);
			} else if (action=='preall' || action=='all') {
				parent.fb_resetValidatedTermsList(service_id);
				
				parent.fb_init_src_display(service_id, 'broader');
				parent.fb_init_src_display(service_id, 'narrower');
				parent.fb_init_src_display(service_id, 'related');

				var validated_results = pclass.string.trim((response.getElementsByTagName("pre")[0]).textContent);
				var validated_results_raw = validated_results;
				
				var broader_results = pclass.string.trim((response.getElementsByTagName("broader")[0]).textContent);
				var narrower_results = pclass.string.trim(( response.getElementsByTagName("narrower")[0]).textContent);
				var related_results = pclass.string.trim((response.getElementsByTagName("related")[0]).textContent);
				var broader_results_raw = broader_results;
				var narrower_results_raw = narrower_results;
				var related_results_raw = related_results;
				
				we_had_some_results = (validated_results || broader_results || narrower_results || related_results);
					
				if (response.getElementsByTagName("pre_raw")[0]) {
					 we_had_some_results = true;
					 validated_results_raw = parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("pre_raw")[0]).textContent ) );
				 }
				
				 if (response.getElementsByTagName("broader_raw")[0]) {
					 we_had_some_results=true;
					 broader_results_raw= parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("broader_raw")[0]).textContent ) );
				 }
				
				 if (response.getElementsByTagName("narrower_raw")[0]) {
					 we_had_some_results=true;
					 narrower_results_raw= parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("narrower_raw")[0]).textContent ) );
				 }

				 if (response.getElementsByTagName("related_raw")[0]) {
					 we_had_some_results=true;
					 related_results_raw= parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("related_raw")[0]).textContent ) );
				 }
				
				parent.fb_addToFacetBoardValidatedTerms(service_id, validated_results, validated_results_raw);
				
				parent.fb_add_to_facetboard(service_id,'broader',broader_results,broader_results_raw);
				parent.fb_add_to_facetboard(service_id,'narrower',narrower_results,narrower_results_raw);
				parent.fb_add_to_facetboard(service_id,'related',related_results,related_results_raw);
			} else if (action=='dummy' || action=='dummytimeout' || error) {
				if (typeof(results)!='undefined') {
					parent.fb_add_to_facetboard(service_id,'broader',results,results_raw);
					parent.fb_add_to_facetboard(service_id,'narrower',results,results_raw);
					parent.fb_add_to_facetboard(service_id,'related',results,results_raw);
				}
			}
		} 
	}
	
	// Set ontofacets context menu
	(function(jQuery){
		jQuery(document).ready( function() {
			jQuery(".fb-term").contextMenu({
				menu: 'facetsContextMenu'
			}, function(action, el, pos) {
				
				switch(action) {
					case "addToBreadcrumb":
						bc_add_breadcrumb_unique(jQuery(el).text(),'result');
					break;
					
					case "exploreInOntologicalFacets":
						fb_set_node_ontofacet(jQuery(el).text());
						detectLanguageInOntoFacets_launchOntoSearch(jQuery(el).text(), 0, 0, 0, 0, 0, 0, $p);
						$p.ajax.call('../../app/tests/LoggerResponder.php?action=10&query=' + jQuery(el).text() + '&from=facets', {'type':'load'});
					break;
				}
			});
		});
	})(jQuery);
		
	//Stop wheel and start aftersearchactions, if last process:
	wtslog('handle_received_src_data (check end metasearch, service_id='+service_id+', srv=('+srv+'), v=('+v+')): ','ONTOS_SYNCH',ONTOS_SYNCH,ONTOS_SYNCH-1,-1);
	
	ONTOS_SYNCH = ONTOS_SYNCH - 1;
	if (ONTOS_SYNCH == 0) {
		eclog('handle_received_src_data (END metasearch, service_id='+service_id+')! Perform aftermetasearch_actions');
		
		if (we_had_some_results)
			perform_aftersearch_actions('onto');
		else
			perform_aftersearch_actions('ontonix');
	}

}


	
	


	function react_to_refine_response(response,vars)
	{
		
		//alert('react_to_refine_response cid:'+vars['cid']+' XMLresponse:'+response);
		
		var pclass=vars['pclass'];
		var module=vars['module'];
		var newsid=vars['newsid'];
		var newtab_db_id = vars['newtab_db_id'];
		
		var cid	='';
		var sid ='';
		var c		='';

		var maxDur='';
		var rts='';
		var cdur='';

		var v='';
		var q='';
		var srv='';
		var srv_raw='';
		var searchtxt='(xml error)';
			
		if (response!=null)
		{
			if (response.getElementsByTagName("refine")[0])
			{
				 cid		=(response.getElementsByTagName("cid")[0]).textContent;
				 sid		=(response.getElementsByTagName("sid")[0]).textContent;
				 c			=(response.getElementsByTagName("c")[0]).textContent;
		
				 maxDur	=(response.getElementsByTagName("maxDur")[0]).textContent;
				 rts		=(response.getElementsByTagName("rts")[0]).textContent;
				 cdur		=(response.getElementsByTagName("cdur")[0]).textContent;

				 v			= parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("v")[0]).textContent ) );
				 q			= parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("q")[0]).textContent ) );
				 srv=		parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("srv")[0]).textContent ) );
				 if (response.getElementsByTagName("srv_raw")[0])
				 {
					 srv_raw= parent.Base64.decode( pclass.string.trim( ( response.getElementsByTagName("srv_raw")[0]).textContent ) );
					 if (srv_raw != '')
					 {
						 //alert('SRC supplied srv_raw: ('+srv_raw+')');
						 /*srv_raw contains a sequence of base64encoded terms separated by a SEPARATOR =comma*/
					 }
				 }
				 searchtxt = srv;
			} // refine ok
		} // response <> null
		
		
		var iframe = parent.document.getElementById('modfram'+newtab_db_id+'_'+module.uniq);
		var rodinwidgetname= parseUri(iframe.src).file;
		// cancel any q, subst qe value:

		old_querystring=parseUri(iframe.src).query;
		var x=1; // dummy
		var qs = new Querystring(old_querystring);
		if (qs.contains('q')) qs.set('q','');
		qs.set('qe',searchtxt);
		qs.set('sid',newsid);
		qs.set('go','1');
		qs.set('show','RDW_widget');
		//qs.set('_w',_w);
		//qs.set('_h',_h);
		qs.set('_x',iframe.id);
		qs.set('pid',parent.$p.app.user.id);
		var uri=
			parseUri(iframe.src).protocol + '://' + parseUri(iframe.src).host + parseUri(iframe.src).path;
		var newuri=uri+'?'+qs.toString();
		olduri=uri+'?'+old_querystring;

		iframe.src=newuri; // and reload!
	}


	
	
	
	function get_stopwordlist(pclass)
	//Get the list once on loading from the rodin server
	{
		var url='<?php print $STOPWORD_SERVER; ?>?getStopwords=1';
		//alert('calling url:  '+url);
		pclass.ajax.call( url,
		{
			'type':'load',
			'callback':
			 {
				'function':cb_collect_stopwords,
				'variables':
				 {
				 }
			 }
		}
		);		 // cb_collect_stopwords(response,variables)		
		
	}
	
	
	
	
	function cb_collect_stopwords(response,variables)
	//Set the global var parent.STOPWORDS as an array of stopwords coming from the server
	{
		var arrStopWords=new Array;

		//alert('cb_collect_stopwords');
		if (response!=null)
		{
			var sws = response.getElementsByTagName("stopwords")[0];
			if (sws)
			{
				if (sws.childNodes.length > 0)
				{
					//alert('cb_collect_stopwords: '+sws.childNodes.length+'Woerter erhalten');
					for(var x=0;x<sws.childNodes.length;x++)
					{
						var w = sws.childNodes[x].textContent;
						arrStopWords.push(w);
					}		
				}
			} // stopwords ok
			
		} // response <> null
		else alert('cb_collect_stopwords: GOT NO STOP WORD ?!?')
		
		parent.STOPWORDS = arrStopWords;
		
	} // cb_collect_stopwords

	
	
	

	function getModules(tab_id)
	//**********************************
	{
		var modules= new Array;
		if (tab_id >= 0)
		{
			for (var i=0;i<parent.tab[tab_id].module.length;i++)
			{
				modules.push(parent.tab[tab_id].module[i]);
			}
		}
		return modules;
	} // getModules
	
	
	function get_datasource_name(url_with_params)
	{ //RDW.BAR.Digiblabla.rodin?blablabla
		var datasource_name=null;
		//alert('get_datasource_name('+url_with_params+')');
		var expr = /RDW_(.*)\./; //match RDW.(xxxx).sth
		expr.exec(url_with_params);
		if (RegExp.$1!='') 
			datasource_name = RegExp.$1;
		else // TRY a generalized widget RDW.BAR.(.*).
		{
			expr = /RDW\.(.*)\.(.*)\./; //match RDW.BAR.(xxxx).sth
			expr.exec(url_with_params);
		
			if (RegExp.$2!='') 
				datasource_name = RegExp.$2;		
		}
		//alert('get_datasource_name liefert: '+datasource_name);
		return datasource_name.toUpperCase();		
	}
				
	
	function in_jdom_path(ele,top)
	//**********************************
	{
			ele = ele.frameElement;
			
			while (ele!=top && ele!=null)
			{
				ele = ele.parentNode;
			}
		
			return (ele==top);
	}
	
	
	
	function get_tab_id(db_tab_id,tabs)
	{
		var tabid=-1;
		for(var i=0;i<tabs.length;i++)
		{
			if (tabs[i].id == db_tab_id)
			{
				tabid=i;
				break;
			}
		}
		return tabid;
	}
	
	
	
	function wait_for_tabid(expected_length)
	{
	  if (tab.length != expected_length)
		setTimeout(function(){wait_for_tabid(expected_length);}, 500);	
	}
	
	


	function get_tab_db_id(iframeid)
	{
		var expr = /(\D*)(\d+)_(.+)/; //match framename components
		expr.exec(iframeid);
		return RegExp.$2;
	}
	

	
	/* getElementByClass
	/**********************/
	function getElementsByClass(doc,theClass,theTag) 
	{
		var someHTMLTags = new Array();

		//Create Array of All HTML Tags
		var allHTMLTags=doc.getElementsByTagName(theTag);
		//Loop through all tags using a for loop
		for (i=0; i<allHTMLTags.length; i++) 
		{
			//Get all tags with the specified class name.
			if (allHTMLTags[i].className==theClass) {
			//Place any code you want to apply to all
			//pages with the class specified.
			//In this example is to �display:none;� them
			//Making them all dissapear on the page.
				
				someHTMLTags[someHTMLTags.length]=allHTMLTags[i];
			}
		}
		return someHTMLTags;
	}
	
	
	




	function reload_frame(iframe_id,render) {
		//reload the same url but another render info	
		var qs=null;
		var url=null;

		var iframe = parent.document.getElementById( iframe_id );
		
		if (iframe!=null)
			url = iframe.src;
		else 
			url = window.location;
		
		qs = new Querystring(parseUri(url).query);
		
		if (qs) {
			qs.set('textZoomRender',render);
			qs.set('rerender',1);
			
			var host = parseUri(url).protocol 
								+ '://' 
								+ parseUri(url).host
								+':'
								+ parseUri(url).port;
			if (host=='://') host= '<?php print $WEBROOT; ?>';
			var newuri=uri+'?'+qs.toString();
			var uri= host 
			+ parseUri(url).path;
			var newuri=uri+'?'+qs.toString();
			
			//alert('about to reload: '+newuri);
			if (iframe!=null)
				iframe.src=newuri; // and reload!
			else
				window.location.href=newuri;
		}
	}
	
	function set_zoom_text_icons(render) {
		var mainbuttonimg1 = document.getElementById('img_mainzoombutton1');
		var mainbuttonimg2 = document.getElementById('img_mainzoombutton2');
		var mainbuttonimg3 = document.getElementById('img_mainzoombutton3');
		
		mainbuttonimg1.src = '<?php print $B_MIN_ICON_NORMAL; ?>';
		mainbuttonimg2.src = '<?php print $B_TOKEN_ICON_NORMAL; ?>';
		mainbuttonimg3.src = '<?php print $B_ALL_ICON_NORMAL; ?>';
		
		switch(render) {
		case('min'):
			mainbuttonimg1.src = '<?php print $B_MIN_ICON_SELECTED; ?>';
			mainbuttonimg1.title= lg("titleTextZoomOneSelected");
			break;
		case('token') :
			mainbuttonimg2.src = '<?php print $B_TOKEN_ICON_SELECTED; ?>';
			mainbuttonimg2.title= lg("titleTextZoomTwoSelected");
			break;
		case('all'):
			mainbuttonimg3.src = '<?php print $B_ALL_ICON_SELECTED; ?>';
			mainbuttonimg3.title= lg("titleTextZoomThreeSelected");
			break;
		}
		
		parent.zoomb1 = mainbuttonimg1.src;
		parent.zoomb2 = mainbuttonimg2.src;
		parent.zoomb3 = mainbuttonimg3.src;
		
		document.getElementById("selectedTextZoom").value = render;
	}
	
	/**
	 * Changes the status of the result aggregation ON/OFF
	 */
	function toggle_aggregation() {

	}

	function reload_frames_render(render) {
		set_zoom_text_icons(render);
		
		var	tab_id = parent.tab[parent.$p.app.tabs.sel].id;
		var pertinentIframes = getPertinentIframesInfos(tab_id);			
		for(var i=0;i<pertinentIframes.length;i++) {
			var iframe = pertinentIframes[i][0];
			
			document.getElementById(iframe.id).contentWindow['widgetResultSet'].askResulsToRender(render);
		}
	}
	
	
	function fri_unregister_widget_prefs(pclass,app_id)
	{
		
		var url="<?php print "$WEBROOT$RODINROOT/$RODINSEGMENT"; ?>/app/u/RodinWidgetSMachine.php";
		
		var params="ajax=1&delete=1&app_id="+app_id;
		
		var action=url+'?'+params;
		//alert('fri_unregister_widget_prefs('+action+')');

		pclass.ajax.call( action,
				{
					'type':'load',
				}
		);	
	}

	
	
	function fri_register_tab_iframe(tabid,iframeObj)
	{
//		alert('	function fri_register_tab_iframe('+tabid+',iframeObj='+iframeObj.src+')');
		var iframes = PORTANEO_TAB_INFO[tabid];
		if (iframes == null)
		{
			iframes = [];
			PORTANEO_TAB_INFO[tabid] = iframes;
		}
		iframes.push(iframeObj);
	}
	
	
	function fri_unregister_tab_iframe(tabid, iframeObj)
	{
//		alert('	function fri_unregister_tab_iframe('+tabid+',iframeObj='+iframeObj.src+')');
		
		var iframes = PORTANEO_TAB_INFO[tabid];
		
		if (iframes == null) {
		} else {
			var index = iframes.indexOf(iframeObj);
			iframes.splice(index, 1);
		}
	}
	
	
	
	function getPertinentIframesInfos(db_tab_id)
	{
		//alert('getPertinentIframesInfos('+db_tab_id+')');
		//TESTOUTPUT
		var iframesinfo= new Array;
		var iframes=PORTANEO_TAB_INFO[db_tab_id];
		if (iframes==null) ; //alert('nix tun: iframes=null');
		else
		{
			var iframe=null;
			for(var i=0; i<iframes.length;i++)
			{
				iframe=iframes[i];
				var expr = /modfram(\d+)_(\d+)/; //match framename components
				expr.exec(iframe.name);
				var frame_tab_id = RegExp.$1;
				var frame_mod_uniq = RegExp.$2;
			
				if (frame_tab_id==db_tab_id)
				{
					var div_id = 'module'+frame_tab_id+'_'+frame_mod_uniq;
					var title_id = 'module'+frame_tab_id+'_'+frame_mod_uniq+'_h';
					var iframe_div = parent.document.getElementById( div_id );
					var title_div = parent.document.getElementById( title_id );
					
					if (!iframe_div); // alert('SysError: Could not find father frame with id='+div_id);
					else
					{	
						var height= iframe.height;
						//alert('iframe '+iframe.name+' = '+height);
					}
					
					if (!title_div) ; // alert('SysError: Could not find title_div with id='+title_div);
					else
					{
						var title = title_div.innerHTML;
					}
					//alert(name+': title='+title+', left,top,w,h='+left+','+top+','+width+','+height);
	
					
					//add infos to array
					//alert('push iframe,title,height:'+"\n"+iframe.name+','+title+','+height);
					iframesinfo.push(new Array(iframe,title,height));
				} // framemodulenum matches
				//else alert('discard: title='+title+', framemodulenum='+framemodulenum);
			}//for each iframe in POSH
			
			//alert('getPertinentIframesInfos('+db_tab_id+') liefert:\n'+iframesinfo.length+' Objekte');
		}
		return iframesinfo;
	} // getPertinentIframesInfos
	
	
	
	
	function FRIdarkProtectionUncache(comment)
	{
	 	//alert(comment);
		// 
		//the following opens the modules security restrictions
		for (  var selTab=0;selTab<parent.tab.length;selTab++)
    	{
        	//parent.tab[selTab].open();
    	}
		
		
		var caching_div= parent.document.getElementById( 'cache' );
		if (caching_div)
			caching_div.style.visibility='hidden';
	
		eval('parent.$p.app.popup.hideCacheFRI()');
	}
	
	
	
	function shift_div(d, x , attr)
	{
		if (attr=='top')
		{
			var t = d.style.top;
			var dtopval = parseInt(t.substr(0,t.indexOf('px'))) + parseInt(x);
			d.style.top=dtopval + 'px';
		}
	} //shift_div 
	
	
	
	
	
	
	/* HTTP */
	
   var http_request = false;
   function makeRequest(url, parameters, okaction) {
	  
	  //alert ('makeRequest: url,parameters= '+url+'?'+parameters+','+okaction); 
		var auth = make_basic_auth('<?php echo $AUTH_SELF_USERNAME?>','<?php echo $AUTH_SELF_PASSWD ?>');
	  http_request = false;
      if (window.XMLHttpRequest) { // Mozilla, Safari,...
         http_request = new XMLHttpRequest();
         if (http_request.overrideMimeType) {
         	// set type accordingly to anticipated content type
            //http_request.overrideMimeType('text/xml');
            http_request.overrideMimeType('text/html');
         }
      } else if (window.ActiveXObject) { // IE
         try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
            try {
               http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
         }
      }
      if (!http_request) {
         alert('Cannot create XMLHTTP instance');
         return false;
      }
      http_request.onreadystatechange = alertContents;
			//The following starts a call from your browser.
			//Is the browser authentifiying this call?
			//alert('ACHTUNG: authentified?: '+url);
      http_request.open('GET', url + '?'+parameters, false); //synchronous call
			http_request.setRequestHeader("Authorization", auth);
      http_request.send(null);
      return alertContents(okaction); //call handler synchronously
   }

   function alertContents(okaction) {
      if (http_request.readyState == 4) {
         if (http_request.status == 200) {
            //alert(http_request.responseText);
            result = http_request.responseText; // app meldet nof affected recs=1
			var expr = /(\d+)/;
			var res = expr.exec(result);
			var inserted_elems=RegExp.$1; //1st match
			if (inserted_elems>=1) 
			{
				if (okaction && okaction!='')
				{
					//alert('action: '+okaction);
					eval(okaction); 	
				}
				else 
				{
					//alert('result: '+result);
					//alert('makeRequest returns 1');	
					return 1;
				}
			}
			else alert ('Could not perform the operation: '+result);
     	 
		 } /* else alert('Could not perform the operation! status:'+http_request.status);*/
      }
   }
   
    function performRequest(url, parameters) {
	  
	  //alert ('makeRequest: url,parameters= '+url+','+parameters+','+okaction); 
	  
	  http_request = false;
      if (window.XMLHttpRequest) { // Mozilla, Safari,...
         http_request = new XMLHttpRequest();
         if (http_request.overrideMimeType) {
         	// set type accordingly to anticipated content type
            //http_request.overrideMimeType('text/xml');
            http_request.overrideMimeType('text/html');
         }
      } else if (window.ActiveXObject) { // IE
         try {
            http_request = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
            try {
               http_request = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
         }
      }
      if (!http_request) {
         alert('Cannot create XMLHTTP instance');
         return false;
      }
      http_request.onreadystatechange = alertContents;
      http_request.open('GET', url + '?'+parameters, false); //synchronous call
      http_request.send(null);
      return alertContents_short(); //call handler synchronously
   }

   function alertContents_short() 
   {
      if (http_request.readyState == 4) 
	  {
        if (http_request.status == 200) 
		{
            //alert(http_request.responseText);
            	return http_request.responseText; // app meldet nof affected recs=1
		} 
		else alert ('Could not perform the operation: '+result);
     	 
	  } /*else alert('Could not perform the operation! status:'+http_request.status);*/
   }
   

   
   
   
   function get_and_send_form_x_params(form,url,extraparams,okaction,what) 
   {
	  var getstr=get_form_params(form,what);
		
	  //alert('send_get_request(   '+url+'?'+extraparams+"&"+getstr+'   )');
      makeRequest(url, extraparams+"&"+getstr, okaction);
   } // get_and_send_form_x_params
	
	
   
   
   
   
   function get_form_params(form,what) 
   //########
   {
	  var user_id = typeof parent.isIndexConnected == 'undefined' ?
			  window.opener.$p.app.user.id :
				  parent.$p.app.user.id;
	   
	  //alert('get_and_send_form_x_params - okaction '+okaction); 
	  var getstr = "";
	  var SELECTS	=form.getElementsByTagName('select');
	  var INPUT		=form.getElementsByTagName('input');
	  var TEXTAREA	=form.getElementsByTagName('textarea');
	  var checkboxes = false;
	  var cboxnames = new Array;
	  var cboxes = new Object;
	  var attribute_displays="";
	  var cbx = -1;
	  var all = (what=='all');
	  
	  for (var i=0; i<SELECTS.length; i++) {
		if (all || SELECTS[i].name.substring(0,1) == 'x')
		{
		  var sel = SELECTS[i];
		  for (var j=0; j<sel.length; j++) 
		  {
			var opt = sel[j];
			//alert(j+': '+opt.name+': '+opt.value+' selected='+opt.selected);
            if(opt.selected)
			{
				if (getstr) getstr+="&";
				getstr += sel.name + "[]=" + opt.value;
			}
		  }
		}
	  }
	  for (i=0; i<TEXTAREA.length; i++) {
		if (all || TEXTAREA[i].name.substring(0,1) == 'x')
		{
		  	if (getstr) getstr+="&";
            	getstr += TEXTAREA[i].name + "=" + TEXTAREA[i].value;
	 	}
	  }
	  
	  
		for (i=0; i<INPUT.length; i++) {
				
			if (all || INPUT[i].name.substring(0,1) == 'x')
			{
				//alert(INPUT[i].name+' isa '+INPUT[i].type);
				
				if (INPUT[i].type=='text')
				{	
					if (getstr) getstr+="&";
								getstr += INPUT[i].name + "=" + INPUT[i].value;
				}

				else if (all && INPUT[i].type=='hidden')
				{	
					if (getstr) getstr+="&";
								getstr += INPUT[i].name + "=" + INPUT[i].value;
				}
				
				else if (INPUT[i].type=='radio')
				{	
					//alert('radio name:'+INPUT[i].name+', value:'+INPUT[i].value+', checked: '+INPUT[i].checked);
					//Mehrerewerte getrennt urch %5D schreiben: (name%5B%5D=wert1%5D=wert2%5D=wert3)
					if (INPUT[i].checked)
					{
						if (getstr) getstr+="&";
									getstr += INPUT[i].name + "=" + INPUT[i].value;
					}
				} 
				else if (INPUT[i].type=='checkbox')
				{	
					//alert('checkbox name:'+INPUT[i].name+', value:'+INPUT[i].value+', checked: '+INPUT[i].checked);
				
					if (INPUT[i].checked)
					{
						if(! cboxes[INPUT[i].name] )
						{
							cboxes[INPUT[i].name]='';
							cbx++;
							cboxnames[cbx]=INPUT[i].name;
						}
						else cboxes[INPUT[i].name]+="&";
						
						//alert('adding '+INPUT[i].name +"[]=" +INPUT[i].value+'    soweit:'+cboxes[INPUT[i].name]);
						
						cboxes[INPUT[i].name]+= INPUT[i].name +"[]=" +INPUT[i].value;
						//alert('cb '+INPUT[i].name+' = '+cboxes[INPUT[i].name]);
					}
				} 
			} //x
			else if (INPUT[i].name.substring(0,3) == 'ad_') //attribute displays
			{
				var realname=INPUT[i].name.substring(3,name.length - 3);
				if (INPUT[i].checked)
				{
					if (attribute_displays) attribute_displays+=',';
					attribute_displays+=realname+':c' ;
				}	
				else
				{
					if (attribute_displays) attribute_displays+=',';
					attribute_displays+=realname+':e' ;
				}	
			
			} //ad_
		}
	  
		if (cbx>=0) // add checkbox values
		{
			for(var cbn in cboxes)
			{
				//alert(cbn+'-value:  '+cboxes[cbn] );	
			
			//if (getstr) getstr+="&";
			//getstr += cboxes[cbn];
			
			getstr = cboxes[cbn] +'&' + getstr;
		}

	  }
	  if (attribute_displays)
	  {
		  getstr+= '&' + 'attributes='+attribute_displays;
		  getstr+= '&' + 'pid='+user_id;
	  }
	  //alert('get_form_params(   '+getstr+'   )');

      return getstr;
   } // get_form_params
	   
   
	
   
   
   
	
function make_basic_auth(user, password) 
{
  var tok = user + ':' + password;
  var hash = Base64.encode(tok);
  return "Basic " + hash;
}
	
	
function getFormular(formname)
{
	var f=null;
	for(var i=0;i<parent.document.forms.length;i++)
	{
		var form=parent.document.forms[i];
		if (form.name==formname)
		{
			f=form;
			break;
		}
	}
	return f;
}
	
	
	
	
	
	 

	
	
	
	
	
	

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


function cleanupAJAXstring(s) 
{
	  /*
	  ** Remove NewLine, CarriageReturn and Tab characters and escape simple quotes in a String
	  **   s  string to be processed
	  ** returns new string
	  */
	  r = "";
	  for (i=0; i < s.length; i++) 
	  {
		var c=s.charAt(i);
	    if (c != '\n' &&
	        c != '\r' &&
	        c != '\t') 
	    	{
	    		/* Single comma couls break the string...*/
	    		if (c == "'")
	    			c="\\"+"'";
	    		/* convert backtip to single quote */
	    		else if (c == "`") 
		    			c="\\"+"'";
		    			
	    		r += c;
	    	}
	    }
	  return r;
}




function unescapeQuotes(s) 
{
	  /*
	  ** Remove NewLine, CarriageReturn and Tab characters and escape simple quotes in a String
	  **   s  string to be processed
	  ** returns new string
	  */
	  r = "";
	  for (i=0; i < s.length; i++) 
	  {
		var c=s.charAt(i);
	    if (c == '\\') ; 
	    else
	    {	r += c;  }
	  }
	  return r;
}






function cleanup_refine(searchtxt)
{
	var original = searchtxt;
	var original2= cleanupAJAXstring(searchtxt);
	//if (original==original2) alert('ersetzung nicht gelungen: String sind noch gleich!!!');
	//alert("PRIMA:\n"+original+"\n\nDOPO:\n"+original2);
	return original2;
}


   
function getElementsByClassName(classname, node) 
{
	if(!node) node = document.getElementsByTagName("body")[0];
	var a = [];
	var re = new RegExp(classname);
	var els = node.getElementsByTagName("*");
	for(var i=0,j=els.length; i<j; i++)
	if(re.test(els[i].className))a.push(els[i]);
	return a;
}



function setbgcolor(id,color)
{
	//alert('setbgcolor: '+color+' id: '+id);
	
	var ele = document.getElementById(id);
	ele.bgColor=color;
	
}


function setMetaSearchInputText(currTxtValue) {
	var rodinsearch_s = parent.document.getElementById('rodinsearch_s');
	rodinsearch_s.value = currTxtValue;
}


function setDOMelem(id,value)
{
	var ele = parent.document.getElementById(id);
	//alert('setDOMelem('+id+','+value+') innerhtml='+ele.innerHTML);
	ele.innerHTML=value;
}



function collapse_td(id)
{
	//alert('collapse_td: id='+id);
	
	var td_title 	= document.getElementById(id+'_T');
	var td_result 	= document.getElementById(id+'_R');
	var td_pic 		= document.getElementById(id+'_P');
	td_result.style.visibility='hidden';
	td_result.style.display = 'none';
	
	if (td_pic)
	{
		td_pic.style.visibility='hidden';
		td_pic.style.display = 'none';
	}
	// adapt table cells... in table
	if (td_title)
	{
		
	}
}

function expand_td(id)
{
	//alert('expand_td: id='+id);
	
	var td_result 	= document.getElementById(id+'_R');
	var td_pic 		= document.getElementById(id+'_P');
	td_result.style.visibility='visible';
	td_result.style.display = 'table-cell';
	if (td_pic)
	{
		td_pic.style.visibility='visible';
		td_pic.style.display = 'table-cell';
	}
}



function collapse_result_items(classref)
{
	var TDs=getElementsByClassName(classref,null);
	//alert('collapse_result_items('+classref+'): '+TDs.length+' elem');
	
	for(var i=0;i<TDs.length;i++)
	{
		var TD=TDs[i];
		var id = TD.id;
		var tdx = document.getElementById(id+'_AX');
		if (tdx) {
			//alert('collapse_result_item '+tdx.name);
			tdx.onclick(); // used to expand all the stuff.. on the node
			//tdx.ondblclick(); // used to collapse all stuff
		}
	}
}

function expand_result_items(classref)
{
	var TDs=getElementsByClassName(classref,null);
	//alert('collapse_result_items('+classref+'): '+TDs.length+' elem');
	
	for(var i=0;i<TDs.length;i++)
	{
		var TD=TDs[i];
		var id = TD.id;
		var tdx = document.getElementById(id+'_AX');
		if (tdx) {
			//tdx.onclick(); // used to expand all the stuff.. on the node
			//alert('expand_result_item '+tdx.name);
			tdx.ondblclick(); // used to collapse all stuff
		}
	}
}




// JavaScript DOM Traverse
// Used to modify the JDOM structure of a cloned widget
function DOM_adapt_newmodule(theObject,old_tab_db_id,new_tab_db_id)
{
	var level = 0;
	DOMr_adapt_newmodule(theObject,level,old_tab_db_id,new_tab_db_id);
}

function DOMr_adapt_newmodule(obj, lvl,old_tab_db_id,new_tab_db_id)
{
	if (obj.id)
	{
		if (obj.id.substr(8)=='results_')
		{	obj=null; // no results on cloned widget: delete node
			return;
		}
	}
	
	if (obj.childNodes.length)
	{
		for (var i=0; i<obj.childNodes.length; i++) {
			DOMr_adapt_newmodule(obj.childNodes[i], lvl + 1,old_tab_db_id,new_tab_db_id);
		}
	}
	
	//Process node if an id exists
	if (obj)
	{
		if (obj.id)
		{
			//if (WANTCONSOLELOG) console.log(obj.tagName + ' ' + obj.id + ':\n');	
			var oldid= obj.id;
			obj.id = new_id(obj.id, new_tab_db_id);
			//if (WANTCONSOLELOG) console.log(Math.pow(10,lvl) + ' '+ oldid + '->'+ obj.id +' \n');
			
			refined_q='';
			//in case an iframe is found: get its content and step over...
			if (obj.id.substr(obj.id,7)=='modfram')
			{
				widget_cloning_modify_querystring(obj,refined_q,old_tab_db_id,new_tab_db_id);
			}
		}
	}
}



/**
 * When this function is called it sets the division with id "modules" positioning to 'absolute',
 * forcing it to take a particular size that I'm now resetting.
 * 
 * @param closing
 * @returns
 */
function adapt_widgetsareas_on_openclose_widgetmenu(closing)
{
	var vmenu_div=document.getElementById("addWidgetBoard");
	var facetboard_div=document.getElementById("facetboard");
	var divmodules=document.getElementById("modules");
	
	if (facetboard_div==null)
	{
		facetboard_div=parent.document.getElementById("facetboard");
		if (facetboard_div==null) alert('System error: Did not find facetBoard even not in parent!');
	}
	{
		var repaint_widgets = false;
		var tab_id = parent.tab[parent.$p.app.tabs.sel].id;
		
		var divmodules = document.getElementById("modules");
		if (divmodules != undefined) {
			if (closing) {
				if (facetboard_div.className == 'facetboard_unvisible') {
					divmodules.style.top = '0px'; // wie area
					divmodules.style.position = 'relative'; // wie area
				} else {
					divmodules.style.top = vmenu_div.style.top; /* wie area */
					divmodules.style.left = vmenu_div.style.width; // wie area
				}
			} else {
				divmodules.style.top = '0px'; // wie area
				divmodules.style.position = 'absolute'; // wie area
				
				var modulesDivWidth = document.body.offsetWidth - facetboard_div.offsetWidth - 20;
				divmodules.style.width = "" + modulesDivWidth + "px";
				
				repaint_widgets=true;
			}
		}
		
		if (repaint_widgets) {
			var pertinentIframes = getPertinentIframesInfos(tab_id);			
			for (var i = 0; i < pertinentIframes.length; i++) {
				var iframe = pertinentIframes[i][0];
				adapt_widget_search_input_width(iframe.name);
			}
		}
	}
}

/**
 * Function adapting the with of the local search
 * input field in widgets.
 * @param framename
 * @returns
 */
function adapt_widget_search_input_width(framename) {
	var iframe = jQuery('#' + framename);
	
	if (iframe.length > 0) {
		var preferencesTd = iframe.contents().find('td[name="searchPreferences"]');
		var searchInput = iframe.contents().find('input.localSearch');
		if (preferencesTd.length > 0) {
			searchInput.css('width', iframe.width() - 145 + 'px');
		} else {
			searchInput.css('width', iframe.width() - 125 + 'px');
		}
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
 





function new_id(id, new_tab_db_id)
{
	var expr = /(\D*)(\d+)_(.+)/; //match framename components
	expr.exec(id);
	var mod_was = RegExp.$1;
	var mod_uniq = RegExp.$3;
	var newid=mod_was+new_tab_db_id+'_'+mod_uniq;
	return newid;
}


function widget_cloning_modify_querystring(iframe,q,old_tab_db_id,new_tab_db_id)
{
	old_querystring=parseUri(iframe.src).query;

  var qs = new Querystring(iframe.src);
	
	var app_id=qs.get('app_id');
	var _x=qs.get('_x');
	//Modifiziere app_id und _x
	var expr = /(\d+):(\d+):(\d+)/; //match framename components
	expr.exec(app_id);
	var user_id = RegExp.$1;
	var p = RegExp.$3;
	var new_app_id=user_id+':'+new_tab_db_id+':'+p;
	
	qs.set('app_id',new_app_id);
	qs.set('_x',iframe.id);
	qs.set('cloned_from_app_id',app_id);
	qs.set('go','');
	qs.set('show','RDW_widget');
	qs.set('qe',q);
	qs.set('q','');
	qs.set('sid','');
	qs.set('show','RDW_widget');
	var uri= parseUri(iframe.src).protocol + '://' + parseUri(iframe.src).host + parseUri(iframe.src).path;
	var newuri= qs.toString();

	iframe.src=newuri;
}



function getIframe(mod)
{
	var iframe=null;
	if (mod)
			iframe=DOMr_find_iframe(mod,0);
	
	return iframe;
}


/**
 * @param widgetsColumnHeight the height of the "highest" widget column
 */
function fri_setFacetBoardParameter(widgetsColumnHeight) {
	if (widgetsColumnHeight>0) {
		if ('<?php print $BROWSER?>'=='Mobile')	{
			/* do not set - mobile device does not handle scrolling */
		} else {
			var vmenuDivScrollHeight = $('addWidgetBoard').scrollHeight;
			var facetboardTitleBarDivScrollHeight = $("facetsBoardTitleBar").scrollHeight;
			var facetNodeLabelDivScrollHeight = $("facet-nodelabel").scrollHeight;
			
			// First, set the size for the facetboard div
			var facetboardDiv = document.getElementById('facetboard');
			var facetBoardNewHeight = widgetsColumnHeight - vmenuDivScrollHeight - 4;
			
			facetboardDiv.style.maxHeight = facetBoardNewHeight + "px";
			
			// Second, set the size for the scrollable container
			var facetboardContainerDiv = document.getElementById('facetboard-container');
			var facetBoardContainerNewHeight = facetBoardNewHeight - facetboardTitleBarDivScrollHeight
				- facetNodeLabelDivScrollHeight;
			
			facetboardContainerDiv.style.maxHeight = facetBoardContainerNewHeight + "px";
		}
	}
}




function DOMr_find_iframe(obj, lvl)
{
	if (obj.childNodes.length)
	{
		for (var i=0; i<obj.childNodes.length; i++) {
			var ret = DOMr_find_iframe(obj.childNodes[i], lvl + 1);
			if (ret!=null)
			{
				return ret;
				break;
			}
		}
	}
	
	//Process node if an id exists
	if (obj && !iframe)
	{
		if (obj.tagName == 'iframe')
		{
				return obj;
		}
	}
	
	return null;
}


function submitenter(myfield,e)
{
	alert('submitenter');
var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return true;

if (keycode == 13)
   {
		 alert('go');
   myfield.form.submit();
   return false;
   }
else
   return true;
}


function submitonenter2(f,event)
{ //works well with FF
	var keycode;
	if (event) keycode = event.keyCode;
	else 
	{	
		alert('no windows event');
		return true;
	}
	if (keycode == 13)
		 {
				f.submit();
				return false;
		 }
	else
		 return true;
}

function fri_get_closeIcon()
{
	return '<?php print $RODINIMAGESURL; ?>/ico_close.gif';
}


function getElementByClass(theClass,document) 
{
	//Populate the array with all the page tags
	var allPageTags=document.getElementsByTagName("*");
	//Cycle through the tags using a for loop
	for (i=0; i<allPageTags.length; i++) 
	{
		//Pick out the tags with our class name
		if (allPageTags[i].className==theClass) 
		{
			return allPageTags[i];
		}
	}
	return null; // else
} 





function html_entity_decode(str) 
{
	try {
	
		var tarea=document.createElement('textarea');
		
		tarea.innerHTML = str; 
		return tarea.value;
		
		/*tarea.parentNode.removeChild(tarea); FRI: unreached*/
	
	} catch(e) 
	{
	
	//for IE add <div id="htmlconverter" style="display:none;"></div> to the page
	/*alert('html_entity_decode('+str+') has a problem?');*/
	/* FRI: this does not help with CHROME and neither with MOZILLA -> Just return the str itself*/
	/*	
	document.getElementById("htmlconverter").innerHTML = '<textarea id="innerConverter">' + str + '</textarea>';
	
	var content = document.getElementById("innerConverter").value;
	
	document.getElementById("htmlconverter").innerHTML = "";
	
	return content;
	*/
		return str;
	}
}


function toggleadd2bc(div)
{
	var myLabels = null;
	if(div.className=="change2add")
	{
		//alert('deaktiviere')
		myLabels = getElementsByClass(document,"bcx","lable"); 

		div.title="push2breadcrumbstitle";
		div.className="noadd";

		myLabels.foreach( function( k, label_obj ) {
			label_obj.className="bcx-deakt";
		});
	}
	else
	{
		//alert('aktiviere')
		myLabels = getElementsByClass(document,"bcx-deakt","lable"); 
		//alert('getElementsByClass() hat '+myLabels.length+' Elemente gefunden.');

		div.title="nopush2breadcrumbstitle";
		div.className="change2add"

		myLabels.foreach( function( k, label_obj ) {
			label_obj.className="bcx";
		});
	}
}





function contains_cr(str)
{
   var t = escape(str)

  if(t.indexOf("%0D%0A") > -1 )
  {
	   alert('CR');
     return true;
  }
   alert('no: '+t);
   return false;
}




function eclog(txt)
{
	if (WANTCONSOLELOG) console.log(Date() + ' '+ txt);
}		
		

function wtslog(txt,varname,v0,v1,increment)
{
	if (WANTCONSOLELOG)
	{
		if (increment==0)
		{
			if (v0>0    &&     v0 > v1)
			console.log(Date() + ' '+ txt+': !!!!!!! restart while still some proc running '+varname+' ['+v0+' -> '+v1+']');
		}
		else
			console.log(Date() + ' '+ txt+': '+varname+' ['+v0+' -> '+v1+']');
		
	}
}		


function permitted_ontosearch()
{
	eclog('Start of ONTOSEARCH SUPPRESSED BECAUSE ONTOS_SYNCH='+ONTOS_SYNCH+' !');
	return !ONTOSEARCH_LOCKED;
}


//alert('RODINutilities.js loaded');