<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once("../u/RodinResult/RodinResultManager.php");
require_once("../u/SOLRinterface/solr_interface.php");
require_once("../u/RDFprocessor.php");
//    global $SOLR_RODIN_CONFIG;
//    global $SOLARIUMDIR;
//    global $USER;
//		$sorted_ranked_subjects = rank_vectors_vsm($referencetext,$subject_list, $this->sid, $this->USER_ID);

		$referencetext="ladies economic time book";
		$subject_list= array(
			'galilean economy time jesus',
			'economy',
			'ladi',
			'time',
			'house',
			'model',
		);

	
	$cr = count($subject_list);
	
	print "<br>TEST with reference text: '$referencetext'<br>";
	print "<br>TEST with $cr subjects: ";
	foreach($subject_list as $k=>$label) print "<br>$k=>$label";

	$sorted_ranked_subjects = rank_vectors_vsm($referencetext,$subject_list, '2370239874.3.3.3', 2, 3);
	$cr = count($sorted_ranked_subjects);

	print "<hr>$cr sorted ranked subjects: ";

	if(is_array($sorted_ranked_subjects)&&count($sorted_ranked_subjects))
		foreach($sorted_ranked_subjects as $label=>$REC)
		{
			list($rank,$k)=$REC;
			print "<br>$rank: $k: $label";
		} // foreach
		
?>
