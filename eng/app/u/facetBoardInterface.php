<?php
#FACET BOARD INTERFACE
#Created by: Fabio Ricci, fabio.ricci@ggaweb.ch
#Date: 13.2.2011

//Creation of a facet board.
// Base:   http://collections.si.edu/search/results.jsp?view=list&date.slider=&q=Calvin&dsort=title&fq=object_type:%22Sculpture+%28visual+work%29%22
// Creation of a single facetboard for  a single SRC

require_once 'FRIdbUtilities.php';

/**
 * Generates the code for the ontological facets board. This includes the
 * search input field and the list of found concepts for each SRC engine.
 */
function generate_facetboard($INIT_SRC_REF_TABS) {
	global $RODINIMAGES, $RODINIMAGESURL;
	global $COLOR_BROADER, $COLOR_NARROWER, $COLOR_RELATED;
	global $TITLESTYLE, $FONTPREFS_TD, $FONTPREFS_TD_QUERY;
	
	global $RODINU;
	global $RODINSEGMENT;
	global $BROWSER;
	global $SRCREFIFRAMENAME;
	global $RODIN_LOGO_YELLOW;
	global $IMG_REFINING_DONE;
	global $nosrc;
    	
	$SRC_ACTION = '';
	$USER_ID = $_SESSION['user_id'];

	if (!$nosrc) {
		$SRC_ACTION = "fri_rodin_repeat_search_context(bc_get_terms(),\$p)";	
	}
	
	$ontoFacetsLaunchSearchTitle = lg("lblOntoExploreFacets");
	$ontoFacetsSearchFieldTitle = lg("titleOntoFacetsSearch");
	//FRI - changed LoggerResponder.php?action=10 --> action=25
	$FACETBOARD = <<<EOF
		<div id="facet-nodelabel" class="facet-nodelabel boardConfiguration">
				<input id="ontofacet_center" type="text" title="$ontoFacetsSearchFieldTitle"
					onkeyup="if (event.keyCode==13) { document.getElementById('ontofacet_center_search').click(); }" />
				<img id="ontofacet_center_wait" style="display: none;" src="$RODINIMAGESURL/magnifier-onto-wait.png" title="Undefined" />
				<img id="ontofacet_center_search" style="cursor:pointer;" src="$RODINIMAGESURL/magnifier-onto.png" title="$ontoFacetsLaunchSearchTitle"				
					onclick="var text=document.getElementById('ontofacet_center').value;
						\$p.ajax.call('../../app/tests/LoggerResponder.php?action=25&query=' + text + '&from=field', {'type':'load'});
						detectLanguageInOntoFacets_launchOntoSearch(text, 0, 0, 0, 0, 0, 0,\$p);" />
				<input id="ontofacet_center_language" type="hidden" value="un" />
		</div>
		<div id="facetboard-container" class="facetboard-container">
EOF;
	
	if (count($INIT_SRC_REF_TABS)) {
		foreach($INIT_SRC_REF_TABS as $src_service_id=>$ARR) {
			list($SRCNAME,$TEMPORARILY_USED) = $ARR;
			$TEMPORARILY_USED_CHECKED=$TEMPORARILY_USED?' checked ':' ';
			$temporary_onoff= "<input type='checkbox' 
													id='tyn_$src_service_id' 
													title='Switch ontological facets on/off for $SRCNAME' 
													$TEMPORARILY_USED_CHECKED
													srcname='$SRCNAME'
													onclick=fb_toggleonto_temponoff(this,$src_service_id,false) >";
			$broaderSegment 	= generate_ontosegment($src_service_id,$SRCNAME,'broader');
			$narrowerSegment 	= generate_ontosegment($src_service_id,$SRCNAME,'narrower');
			$relatedSegment 	= generate_ontosegment($src_service_id,$SRCNAME,'related');

			$FACETBOARD.=<<<EOF
				<div id="fb_itemname_$src_service_id" class="facetcontrol-normal">
				 	<table cellpadding=0 cellspacing=0>
				 		<tr>
				 			<td align=left valign=top>$temporary_onoff</td>
				 			<td>
								<div id="fb_itemcontent_v_{$src_service_id}" style="padding-right: 2px; display:none;">
									<table id="fb_table_v_{$src_service_id}" cellpadding="0" cellspacing="0" border=0 width="100%" class="fb-group-table">
										<tr style="cursor:pointer;font-style:italic;" class="$CLASS_ITEMEXPANDER1">
											<td class="facetcontrol-td" valign="center" style="font-style:italic;" />
										</tr>
									</table>  
								</div>
								<table cellpadding="1" cellspacing="1" border="0" style="width: 100%;">
									<tr style="cursor: pointer;">
										<td id="fb_itemname_expander_$src_service_id" class="fb-expander-uninit" alt="Expand" valign="center" width="10"
										 onclick="\$p.ajax.call('../../app/tests/LoggerResponder.php?action=11&name={$SRCNAME}', {'type':'load'});
										 	fb_toggle_allItemContent('{$src_service_id}');"> 	
										</td>
										<td id="fb_itemname_expander2_$src_service_id" align="left" valign="center" class="facetcontrol-td-uninit"
											onclick="document.getElementById('fb_itemname_expander_$src_service_id').onclick();">$SRCNAME</td>
										<td id="fb_itemcount_$src_service_id" class="facet-result-count"></td>
									</tr>
								</table>  
							</div>
							<div id="fb_itemcontent_{$src_service_id}" class="facetgroup-inactive">
								$broaderSegment
								$narrowerSegment
								$relatedSegment
							</td>
						</tr>
					</table>
				</div id="fb_itemname_$src_service_id">
EOF;
		} // foreach
	}
$FACETBOARD.="</div id='facetboard-container'>";

return $FACETBOARD;
}



/**
 * Generates two divs, one holding the name of the segment Broader/Narrower/Related
 * and the other one holding the list of terms.
 * 
 * @param $src_service_id
 * @param $srcName
 * @param string $longName holds the name of the segment (Broader, Narrower or Related).
 * @param tring $shortName holds the abbreviation of the segment's name (b, n or r).
 */
function generate_ontosegment($src_service_id, $srcName, $segmentName) {
	switch ($segmentName) {
		case 'broader':
			$shortName = 'b';
			$longName = lg('lblOntoFacetsBroader');
			$segmentToggleTitle = lg('titleOntoFacetsBroader', $srcName);
			break;
		case 'narrower':
			$shortName = 'n';
			$longName = lg('lblOntoFacetsNarrower');
			$segmentToggleTitle = lg('titleOntoFacetsNarrower', $srcName);
			break;
		case 'related':
		default:
			$shortName = 'r';
			$longName = lg('lblOntoFacetsRelated');
			$segmentToggleTitle = lg('titleOntoFacetsRelated', $srcName);
	}
	
	return <<<EOR
		<!-- Segment name -->
		<div id="fb_item_{$shortName}_{$src_service_id}" class="facetgroup-header">
			<table cellpadding="1" cellspacing="1" border="0" style="width: 100%;">
				<tr style="cursor: pointer;font-style:italic;" onclick="\$p.ajax.call('../../app/tests/LoggerResponder.php?action=12&name=$srcName/$segmentName', {'type':'load'});
					fb_toggle_itemcontent('{$src_service_id}','_{$shortName}_')" title="$segmentToggleTitle">
					<td id="fb_itemname_expander_{$shortName}_$src_service_id" class="fb-expander" alt="Expand" valign="center" width="12"></td>
					<td align="left" class="facetcontrol-td" style="font-style:italic;">$longName</td>
					<td id="fb_itemcount_{$shortName}_{$src_service_id}" class="facet-result-count"></td>
				</tr>
			</table>  
		</div>
		
		<!-- Segment list of terms -->
		<div id="fb_itemcontent_{$shortName}_{$src_service_id}" class="facetlist-inactive">
			<table id="fb_table_{$shortName}_{$src_service_id}" cellpadding="0" cellspacing="0" border=0 width="100%" class="fb-group-table">
				<tr style="cursor:pointer;font-style:italic;" class="fb-sorting">
					<td class="facetcontrol-td" valign="center" style="font-style:italic;" />
				</tr>
			</table>  
		</div>
EOR;
}

?>
